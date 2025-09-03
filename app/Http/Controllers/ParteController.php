<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Importa la clase Cache
use App\Http\Controllers\ProductoController;

class ParteController extends Controller
{
    private $productoController;
    // Se elimina la propiedad $origen para que no se cargue en cada petición

    public function __construct(ProductoController $productoController)
    {
        $this->productoController = $productoController;
        // Se elimina la llamada a la API del constructor para mejorar el rendimiento
    }

    public function index()
    {
        return view('parteFormulario', ['origenes' => $this->consultarOrigen()]);
    }

    public function buscar(Request $request)
    {
        $id = $request->input('buscar');

        if ($id == null) {
            return back()->with('error', 'No se buscó ningún cliente.');
        }
        $busqueda = strtoupper(trim($id));
        $clientes = $this->consultarClientes($busqueda);

        if (empty($clientes)) {
            return back()->with('error', 'No se encontró ningún cliente.');
        }
        $partes = $this->consultarPartes($clientes[0]['CardCode'], 'CustomerCode');

        $tecnico = null;
        if (!empty($partes) && isset($partes[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($partes[0]['TechnicianCode']);
        }

        if (count($partes) >= 2) {
            return view('parteFormulario', ['cliente' => $clientes[0], 'partes' => $partes, 'origenes' => $this->consultarOrigen(), 'tecnico' => $tecnico])
                ->with('success', 'Parte encontrado para el cliente.');
        }

        return view('parteFormulario', ['cliente' => $clientes[0], 'parte' => $partes[0] ?? null, 'origenes' => $this->consultarOrigen(), 'tecnico' => $tecnico])
            ->with('success', 'Parte encontrado para el cliente.');
    }

    public function buscarRMA(Request $request)
    {
        $id = $request->input('busquedaRMA');
        if ($id == null) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }
        $busqueda = strtoupper(trim($id));
        $rmas = $this->consultarPartes($busqueda, 'U_H8_RMA');

        if (empty($rmas)) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }

        $cliente = $this->consultarClientes($rmas[0]['CustomerCode'] ?? '');

        $tecnico = null;
        if (isset($rmas[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($rmas[0]['TechnicianCode']);
        }

        return view('parteFormulario', ['parte' => $rmas[0], 'cliente' => $cliente[0] ?? null, 'origenes' => $this->consultarOrigen(), 'tecnico' => $tecnico])
            ->with('success', 'Parte encontrado.');
    }

    public function nuevoParte($id)
    {
        $cliente = $this->consultarClientes($id);
        return view('parteFormulario', ['cliente' => $cliente[0] ?? null, 'origenes' => $this->consultarOrigen(), 'tecnico' => null]);
    }

    public function sugerencias(Request $request)
    {
        $term = strtoupper(trim($request->get('term')));
        $busquedaConES = $term;
        if (!str_starts_with($term, 'ES')) {
            $busquedaConES = 'ES' . $term;
        }
        $term = str_replace("'", "''", $term);

        $accion = "consultar_BusinessPartners";
        $data = array(
            "select" => "CardCode,CardName,Phone1,FederalTaxID",
            "where" => "substringof('$term', CardCode) or substringof('$term', CardName) or substringof('$term', Phone1) or FederalTaxID eq '$term' or FederalTaxID eq '$busquedaConES'"
        );

        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = json_decode($response->body(), true);
        Log::info('Datos recibidos: ', ['datos' => $body['value']]);

        return ($body['value'] ?? []);
    }

    public function consultarClientes($busqueda)
    {
        if (empty($busqueda))
            return [];

        $busquedaConES = $busqueda;
        if (!str_starts_with($busqueda, 'ES')) {
            $busquedaConES = 'ES' . $busqueda;
        }
        $busqueda = str_replace("'", "''", $busqueda);

        $accion = "consultar_BusinessPartners";
        $data = array(
            "select" => "CardCode,CardName,Phone1,FederalTaxID",
            "where" => "substringof('$busqueda', CardCode) or substringof('$busqueda', CardName) or substringof('$busqueda', Phone1) or FederalTaxID eq '$busqueda' or FederalTaxID eq '$busquedaConES'"
        );

        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = json_decode($response->body(), true);
        Log::info('Datos recibidos DE CLIENTE: ', ['datos' => $body['value'] ?? '']);

        return ($body['value'] ?? []);
    }

    public function consultarPartes($data, $col)
    {
        if (empty($data)) {
            return [];
        }

        $accion = "consultar_ServiceCalls";
        $whereClause = "";

        if ($col == 'ServiceCallID' || $col == 'DocNum') {
            $whereClause = "$col eq $data";
        } else {
            $data = str_replace("'", "''", $data);
            $whereClause = "substringof('$data', $col)";
        }
        $data = array(
            "where" => $whereClause,
            "order" => "CreationDate desc",
        );

        Log::info('Consultado parte: ', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post(env('API_SAP_URL'), [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => env('API_SAP_USER', 'dani'),
                'datos' => $data
            ])
        ]);

        $body = json_decode($response->body(), true);
        Log::info('Parte encontrado: ', ['accion' => $accion, 'respuesta' => $body]);

        return ($body['value'] ?? []);
    }

    public function showParte($callID)
    {
        $parte = $this->consultarPartes($callID, 'ServiceCallID');
        $parte = $parte[0] ?? null;

        if (!$parte) {
            return redirect()->route('parte.index')->with('error', 'Parte no encontrado.');
        }

        $cliente = $this->consultarClientes($parte['CustomerCode']);
        $tecnico = $this->nombreTecnico($parte['TechnicianCode'] ?? null);

        return view('parteFormulario', ['cliente' => $cliente[0] ?? null, 'parte' => $parte, 'origenes' => $this->consultarOrigen(), 'tecnico' => $tecnico]);
    }

    public function crear(Request $request)
    {
        $this->validarCamposSAP($request);
        $datos = $request->all();

        $accion = (empty($datos['ServiceCallID']) && empty($datos['DocNum']))
            ? 'crear_ServiceCalls'
            : 'modificar_ServiceCalls';

        try {
            Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $datos]);

            $response = Http::asForm()->post(env('API_SAP_URL'), [
                'json' => json_encode([
                    'accion' => $accion,
                    'usuario' => env('API_SAP_USER', 'dani'),
                    'datos' => $datos
                ])
            ]);

            // Decodificamos la respuesta JSON de la API. Ahora debería ser un array limpio.
            $body = $response->json();
            Log::info('Respuesta de SAP', ['body' => $body]);

            if ($response->successful() && !isset($body['error'])) {
                $successMessage = ($accion == 'crear_ServiceCalls') ? 'Parte creado correctamente.' : 'Parte modificado correctamente.';

                // --- INICIO DE LA CORRECCIÓN ---

                // 1. Obtenemos el ID del parte de la respuesta.
                //    Si es una modificación, el ID ya viene en $datos.
                //    Si es una creación, viene en $body.
                $serviceCallID = ($accion == 'crear_ServiceCalls')
                    ? $body['ServiceCallID']
                    : $datos['ServiceCallID'];

                // 2. Con el ID, volvemos a consultar el parte para tener TODOS los datos actualizados.
                $parteCompleto = $this->consultarPartes($serviceCallID, 'ServiceCallID');
                $parte = $parteCompleto[0] ?? null;

                if (!$parte) {
                    return back()->withInput()->withErrors(['api_error' => 'El parte se guardó, pero no se pudo recuperar para mostrarlo.']);
                }

                // 3. Obtenemos los datos del cliente y del técnico asociados al parte.
                $cliente = $this->consultarClientes($parte['CustomerCode']);
                $tecnico = $this->nombreTecnico($parte['TechnicianCode'] ?? '');

                // 4. Devolvemos la vista con todos los datos frescos y completos.
                return view('parteFormulario', [
                    'success' => $successMessage,
                    'parte' => $parte,
                    'cliente' => $cliente[0] ?? null,
                    'origenes' => $this->consultarOrigen(),
                    'tecnico' => $tecnico
                ]);

                // --- FIN DE LA CORRECCIÓN ---
            }

            Log::error('Error de SAP', ['body' => $body]);
            return back()->withInput()->withErrors(['api_error' => 'Error de SAP: ' . $this->extraerMensajeErrorSAP($body)]);

        } catch (\Exception $e) {
            Log::error('Excepción al enviar a SAP', ['exception' => $e->getMessage()]);
            return back()->withInput()->withErrors(['exception' => 'Excepción: ' . $e->getMessage()]);
        }
    }


    public function consultarOrigen()
    {
        // Usamos la caché para almacenar los orígenes durante 60 minutos
        return Cache::remember('sap.origenes', 3600, function () {
            $accion = "consulta_ServiceCallsOrigins";
            $data = [
                'select' => "OriginID,Name",
                'where' => "OriginID gt 0",
                'order' => "Name"
            ];

            try {
                Log::info('Consultando origenes desde SAP (SIN CACHÉ)', ['accion' => $accion]);
                $response = Http::asForm()->post(env('API_SAP_URL'), [
                    'json' => json_encode([
                        'accion' => $accion,
                        'usuario' => env('API_SAP_USER', 'dani'),
                        'datos' => $data
                    ])
                ]);
                $result = $response->json();
                return $result['value'] ?? [];
            } catch (\Exception $e) {
                Log::error('Excepción al consultar la lista de origenes', ['exception' => $e->getMessage()]);
                return [];
            }
        });
    }

    private function extraerMensajeErrorSAP($body)
    {
        $decoded = json_decode($body, true);
        if (isset($decoded['resultado']['error']['message']['value'])) {
            return $decoded['resultado']['error']['message']['value'];
        }
        return $decoded['error']['message']['value'] ?? 'Respuesta inesperada de SAP.';
    }

    private function validarCamposSAP(Request $request)
    {
        return $request->validate([
            'CustomerName' => 'required|string',
            'ItemCode' => 'required|string',
            'ServiceBPType' => 'required|string',
            'Subject' => 'required|string',
            'U_H8_MOTIVO' => 'required|string'
        ], [
            'CustomerName.required' => 'El nombre del cliente es obligatorio.',
            'ItemCode.required' => 'El código del artículo es obligatorio.',
            'ServiceBPType.required' => 'El tipo de interlocutor comercial es obligatorio.',
            'Subject.required' => 'El asunto es obligatorio.',
            'U_H8_MOTIVO.required' => 'El tipo de llamada es obligatorio.'
        ]);
    }

    public function nombreTecnico($id)
    {
        if (empty($id)) {
            return null;
        }

        $accion = "consulta_EmployeesInfo";
        $data = array(
            "select" => "FirstName",
            "where" => "EmployeeID eq $id",
        );

        try {
            Log::info('Enviando datos a SAP para obtener técnico', ['accion' => $accion, 'datos' => $data]);
            $response = Http::asForm()->post(env('API_SAP_URL'), [
                'json' => json_encode([
                    'accion' => $accion,
                    'usuario' => env('API_SAP_USER', 'dani'),
                    'datos' => $data
                ])
            ]);
            $body = json_decode($response->body(), true);
            Log::info('Datos del técnico recibidos: ', ['datos' => $body['value'] ?? '']);
            return ($body['value'][0] ?? null);
        } catch (\Exception $e) {
            Log::error('Excepción al consultar técnico en SAP', ['exception' => $e->getMessage()]);
            return null;
        }
    }

    public function vistaImprimir($parteID, $clienteID)
    {
        $parte = $this->consultarPartes($parteID, 'ServiceCallID');
        $cliente = $this->consultarClientes($clienteID);

        $tecnico = null;
        if (!empty($parte) && isset($parte[0]['TechnicianCode'])) {
            $tecnico = $this->nombreTecnico($parte[0]['TechnicianCode']);
        }

        $origen = $this->consultarOrigen();

        return view('partes.vista_imprimir', [
            'parte' => $parte[0] ?? null,
            'cliente' => $cliente[0] ?? null,
            'tecnico' => $tecnico,
            'origen' => $origen
        ]);
    }
}
