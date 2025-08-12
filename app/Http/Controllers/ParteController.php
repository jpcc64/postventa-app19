<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SapService;

class ParteController extends Controller
{
    public function index()
    {
        return view('parteFormulario');
    }

    public function buscar(Request $request)
    {
        $id = $request->input('id');
        if ($id == null) {
            return back()->with('error', 'No se encontró ningún cliente.');
        }
        $busqueda = strtoupper(trim($id));
        $clientes = $this->consultarClientes($busqueda);
        if (empty($clientes)) {
            return back()->with('error', 'No se encontró ningún cliente.');
        }

        $partes = $this->consultarPartes($clientes[0]->CardCode);
        if (count($partes) >= 2) {
            // si tiene 2 o mas partes saltará el model para elegir que parte usa
            return view('parteFormulario', ['cliente' => $clientes[0], 'partes' => $partes])
                ->with('success', 'Parte encontrado para el cliente.');
        }

        return view('parteFormulario', ['cliente' => $clientes[0], 'parte' => $partes[0] ?? null])
            ->with('success', 'Parte encontrado para el cliente.');
    }

    public function sugerencias(Request $request)
    {
        $term = strtoupper(trim($request->get('term')));
        $term = str_replace("'", "''", $term);

        $sql = <<<EOT
        SELECT TOP 15 "CardCode", "CardName", "LicTradNum", "Phone1"
        FROM OPENQUERY(HANA, '
            SELECT "CardCode", "CardName", "LicTradNum", "Phone1"
            FROM "SBO_TEST_PREFACIERRE"."OCRD"
            WHERE "CardCode" LIKE ''$term%'' 
               OR "CardName" LIKE ''%$term%'' 
               OR "LicTradNum" LIKE ''%$term%''
            ORDER BY "CardName" ASC
        ')
    EOT;

        $result = DB::select($sql);
        return response()->json($result);
    }


    private function consultarClientes($busqueda)
    {
        $busqueda = str_replace("'", "''", $busqueda);

        $sql = <<<EOT
            SELECT CardCode, CardName, LicTradNum, Phone1, Phone2, E_Mail, MailAddres, City, Country
            FROM OPENQUERY(HANA, '
                SELECT "CardCode", "CardName", "LicTradNum", "Phone1", "Phone2", "E_Mail", "MailAddres", "City", "Country"
                FROM "SBO_TEST_PREFACIERRE"."OCRD"
                WHERE "CardCode" LIKE ''%$busqueda%''
                   OR "CardName" LIKE ''%$busqueda%''
                   OR "LicTradNum" LIKE ''%$busqueda%''
            ')
        EOT;


        return DB::select($sql);
    }

    private function consultarPartes($customer)
    {
        return DB::select(<<<EOT
            SELECT callID, customer, custmrName, contctCode, internalSN, itemCode, itemName, itemGroup, status, assignee, descrption, origin, technician,
            resolution, DocNum, BPType, BPContact, BPPhone1, BPPhone2, BPCellular, BPFax, BPE_Mail, BPShipCode, BPShipAddr, BPBillCode, BPBillAddr, Telephone,
            U_H8_SerieEurowin, U_H8_Clientecontado, U_H8_Nombre, U_H8_NIF, U_H8_Telefono, U_H8_RMA, U_H8_MOTIVO
            FROM OPENQUERY(HANA, '
                SELECT * FROM "SBO_TEST_PREFACIERRE".OSCL
            ')
            WHERE customer = ?
        EOT, [$customer]);
    }

    public function showParte($callID)
    {

        $parte = DB::select(<<<EOT
            SELECT callID, customer, custmrName, contctCode, internalSN, itemCode, itemName, itemGroup, status, assignee, descrption, origin, technician,
            resolution, DocNum, BPType, BPContact, BPPhone1, BPPhone2, BPCellular, BPFax, BPE_Mail, BPShipCode, BPShipAddr, BPBillCode, BPBillAddr, Telephone,
            U_H8_SerieEurowin, U_H8_Clientecontado, U_H8_Nombre, U_H8_NIF, U_H8_Telefono, U_H8_RMA, U_H8_MOTIVO
            FROM OPENQUERY(HANA, '
                SELECT * FROM "SBO_TEST_PREFACIERRE".OSCL
            ')
            WHERE callID = ?
        EOT, [$callID]);
        if (empty($parte)) {
            return redirect()->back()->with('error', 'Parte no encontrado');
        }

        $parte = $parte[0];
        $cliente = $this->consultarClientes($parte->customer);

        return view('parteFormulario', ['cliente' => $cliente[0], 'parte' => $parte]);
    }


        protected $sapService; //variable para llamada a la api de SAP

    // Inyectamos el servicio en el constructor
    public function __construct(SapService $sapService)
    {
        $this->sapService = $sapService;
    }

    /**
     * Gestiona tanto la creación como la modificación de partes.
     */
    public function crear(Request $request)
    {
        $formData = $request->except('_token');

        $esCreacion = empty($formData['callID']) && empty($formData['DocNum']);
        $accion = $esCreacion ? 'crear' : 'modificar';


        // Preparamos los datos con el formato que SAP necesita
        $sapData = $this->preparaDatosSap($formData);
        try {
            Log::info("Intentando {$accion} en SAP", ['datos_enviados' => $sapData]);

            if ($esCreacion) {
                $resultado = $this->sapService->createServiceCall($sapData);
            } else {
                $callID = $formData['callID'];
                $resultado = $this->sapService->updateServiceCall($callID, $sapData);
            }

            Log::info('Respuesta de SAP Service Layer', ['resultado' => $resultado]);

            if (isset($resultado['error'])) {
                $errorMessage = $resultado['error']['message']['value'] ?? 'Error desconocido de SAP.';
                return back()->withInput()->withErrors(['api_error' => $errorMessage]);
            }

            return redirect()->route('parte')
                ->with('success', $accion === 'crear' ? 'Parte creado correctamente.' : "Parte modificado correctamente.");

        } catch (\Exception $e) {
            Log::error('Excepción al comunicar con SAP', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->withErrors(['exception' => 'Excepción del sistema: ' . $e->getMessage()]);
        }
    }

    /**
     * Transforma los datos planos del formulario a la estructura que SAP espera.
     */
    private function preparaDatosSap(array $formData): array
    {
        $sapData = [];

        // --- Mapeo de campos principales ---
        $mapeoCampos = [
            'Subject',
            'CustomerCode',
            'CustomerName',
            'ContactCode',
            'InternalSerialNum',
            'ItemCode',
            'ItemDescription',
            'ItemGroupCode',
            'Status',
            'Description',
            'Origin',
            'TechnicianCode',
            'Resolution',
            'ServiceBPType',
            'BPContactPerson',
            'BPPhone1',
            'BPPhone2',
            'BPCellular',
            'BPFax',
            'BPeMail',
            'BPShipToCode',
            'BPShipToAddress',
            'BPBillToCode',
            'BPBillToAddress',
            'Telephone',
            'U_H8_SerieEurowin',
            'U_H8_Clientecontado',
            'U_H8_Nombre',
            'U_H8_NIF',
            'U_H8_Telefono',
            'U_H8_RMA',
            'U_H8_MOTIVO'
        ];

        foreach ($mapeoCampos as $campo) {
            if (isset($formData[$campo])) {
                $sapData[$campo] = $formData[$campo];
            }
        }

        // --- Construcción del bloque de dirección anidado ---
        $addressEntry = [];
        $mapeoDireccion = [
            'BPShipToStreet' => 'ShipToStreet',
            'BPShipToCity' => 'ShipToCity',
            'BPShipToZipCode' => 'ShipToZipCode',
            'BPShipToCounty' => 'ShipToCounty',
            'BPShipToCountry' => 'ShipToCountry',
        ];

        foreach ($mapeoDireccion as $formKey => $sapKey) {
            if (isset($formData[$formKey])) {
                $addressEntry[$sapKey] = $formData[$formKey];
            }
        }

        // Solo añadimos el sub-array si contiene al menos un dato.
        if (!empty($addressEntry)) {
            $sapData['ServiceCallBPAddressComponents'][] = $addressEntry;
        }

        return $sapData;
    }
    private function extraerMensajeErrorSAP($body)
    {
        $decoded = json_decode($body, true);
        dd($decoded['resultado']);

        if (isset($decoded['resultado']['error']['message']['value'])) {
            return $decoded['resultado']['error']['message']['value'];
        }
        return $decoded['resultado']['error']['message']['value'] ?? 'Respuesta inesperada de SAP.';
    }

    private function validarCamposParte(Request $request)
    {
        return $request->validate([
            'CustomerCode' => 'required|string',
            'CustomerName' => 'required|string',
            'ContactCode' => 'nullable|numeric',
            'DocNum' => 'string',
            'Telephone' => 'nullable|string',
        ], [
            'CustomerCode.required' => 'El código del cliente es obligatorio.',
            'CustomerCode.string' => 'El código del cliente debe ser una cadena de texto.',
            'CustomerName.required' => 'El nombre del cliente es obligatorio.',
            'CustomerName.string' => 'El nombre del cliente debe ser una cadena de texto.',
            'ContactCode.numeric' => 'La persona de contacto debe ser un número.',
            'DocNum.integer' => 'El número de documento debe ser un número entero.',
            'Telephone.string' => 'El teléfono debe ser una cadena de texto.'
        ]);
    }
}
