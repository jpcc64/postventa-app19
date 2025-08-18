<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParteController extends Controller
{
    public function index()
    {
        return view('parteFormulario');
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
    //    dd($clientes[0]['CardCode']);
        $partes = $this->consultarPartes($clientes[0]['CardCode']);
        if (count($partes) >= 2) {
            // si tiene 2 o mas partes saltará el model para elegir que parte usa
            return view('parteFormulario', ['cliente' => $clientes[0], 'partes' => $partes])
                ->with('success', 'Parte encontrado para el cliente.');
        }

        return view('parteFormulario', ['cliente' => $clientes[0], 'parte' => $partes[0] ?? null])
            ->with('success', 'Parte encontrado para el cliente.');
    }

    public function nuevoParte($id)
    {
        $cliente = $this->consultarClientes($id);
       // dd($cliente);
        return view('parteFormulario', ['clientes' => $cliente[0]]);
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

        // $sql = <<<EOT
        //        SELECT CardCode, CardName, LicTradNum, Phone1, Phone2, E_Mail, MailAddres, City, Country
        //        FROM OPENQUERY(HANA, '
        //            SELECT "CardCode", "CardName", "LicTradNum", "Phone1", "Phone2", "E_Mail", "MailAddres", "City", "Country"
        //            FROM "SBO_TEST_PREFACIERRE"."OCRD"
        //            WHERE "CardCode" LIKE ''%$busqueda%''
        //               OR "CardName" LIKE ''%$busqueda%''
        //               OR "Phone1" LIKE ''%$busqueda%''
        //         ')
        //    EOT;
        // return DB::select($sql);
        $accion = "consultar_BusinessPartners";
        $data = array(
            "select" => "CardCode,CardName,Phone1",//Asunto
            "where" => "substringof('$busqueda', CardCode) or substringof('$busqueda', CardName) or substringof('$busqueda', Phone1)",//codigo de interlocutor comercial
        );

        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => 'dani',
                'datos' => $data
            ])
        ]);

        $body = json_decode($response->body(), true);
        Log::info('Datos recibidos: ', ['datos' => $body['value']]);

        return ($body['value'] ?? []);

    }

    private function consultarPartes($customer)
    {
        //        return DB::select(<<<EOT
//           SELECT callID, customer, custmrName, contctCode, internalSN, itemCode, itemName, itemGroup, status, assignee, descrption, origin, technician,
//            resolution, DocNum, BPType, BPContact, BPPhone1, BPPhone2, BPCellular, BPFax, BPE_Mail, BPShipCode, BPShipAddr, BPBillCode, BPBillAddr, Telephone,
//            U_H8_SerieEurowin, U_H8_Clientecontado, U_H8_Nombre, U_H8_NIF, U_H8_Telefono, U_H8_RMA, U_H8_MOTIVO
//            FROM OPENQUERY(HANA, '
//                SELECT * FROM "SBO_TEST_PREFACIERRE".OSCL
//            ')
//            WHERE customer = ?
//        EOT, [$customer]);

        $accion = "consultar_ServiceCalls";
        $data = array(
            //  "select" => "ServiceCallID,Subject,CustomerCode,",//Asunto
            "where" => "CustomerCode eq '$customer'",//codigo de interlocutor comercial
            "order" => "ServiceCallID",//Nombre interlocutor comercial

        );

        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => 'dani',
                'datos' => $data
            ])
        ]);

        $body = json_decode($response->body(), true);
        Log::info('Respuesta de SAP', ['accion' => $accion, 'respuesta' => $body]);

        return ($body['value'] ?? []);

    }

    public function showParte($callID)
    {

        //    $parte = DB::select(<<<EOT
        //    SELECT callID, customer, custmrName, contctCode, internalSN, itemCode, itemName, itemGroup, status, assignee, descrption, origin, technician,
        //        resolution, DocNum, BPType, BPContact, BPPhone1, BPPhone2, BPCellular, BPFax, BPE_Mail, BPShipCode, BPShipAddr, BPBillCode, BPBillAddr, Telephone,
        //        U_H8_SerieEurowin, U_H8_Clientecontado, U_H8_Nombre, U_H8_NIF, U_H8_Telefono, U_H8_RMA, U_H8_MOTIVO
        //        FROM OPENQUERY(HANA, '
        //            SELECT * FROM "SBO_TEST_PREFACIERRE".OSCL
        //        ')
        //        WHERE callID = ?
        //    EOT, [$callID]);
        //    if (empty($parte)) {
        //        return redirect()->back()->with('error', 'Parte no encontrado');
        //    }

        //    $parte = $parte[0]; // Aquí lo convertimos en objeto único
        //    $cliente = $this->consultarClientes($parte->customer);

        //    return view('parteFormulario', ['cliente' => $cliente[0], 'parte' => $parte]);
        $accion = "consultar_ServiceCalls";
        $data = array(
            //"select" => "ServiceCallID,Subject,CustomerCode,",//Asunto
            "where" => "ServiceCallID eq $callID",//codigo de parte
            "order" => "ServiceCallID",//Nombre interlocutor comercial
        );
        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => 'dani',
                'datos' => $data
            ])
        ]);
        $response = json_decode($response->body(), true);
        $parte = $response['value'][0];
        $cliente = $this->consultarClientes($parte['CustomerCode']);

        return view('parteFormulario', ['cliente' => $cliente[0], 'parte' => $parte]);

    }

    private function consultarCallID($DocNum)
    {
        return DB::select(<<<EOT
            SELECT callID
            FROM OPENQUERY(HANA, '
                SELECT * FROM "SBO_TEST_PREFACIERRE".OSCL
            ')
            WHERE DocNum = ?
        EOT, [$DocNum]);
    }



    public function crear(Request $request)
    {
        $datos = $request->all();
        $accion = (empty($datos['callID']) && empty($datos['DocNum']))
            ? 'crear_ServiceCalls'
            : 'modificar_ServiceCalls';

        try {
            Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $datos]);
            $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
                'json' => json_encode([
                    'accion' => $accion,
                    'usuario' => 'dani',
                    'datos' => $datos
                ])
            ]);

            $body = $response->body();
            Log::info('Respuesta de SAP', ['body' => $body]);
            if ($response->successful() && !str_contains($body, '"error"')) {
                return back()->with([
                    'success' => $accion == 'crear_ServiceCalls' ? 'Parte creado correctamente.' : 'Parte modificado correctamente.',
                    'parte' => $datos,
                    'resultado' => $body
                ]);
            }

            Log::error('Error de SAP', ['body' => $body]);
            return back()->withErrors([
                'api_error' => 'Error de SAP: ' . $this->extraerMensajeErrorSAP($body)
            ]);
        } catch (\Exception $e) {
            Log::error('Excepción al enviar a SAP', ['exception' => $e->getMessage()]);
            return back()->withErrors([
                'exception' => 'Excepción: ' . $e->getMessage()
            ]);
        }
    }
    private function extraerMensajeErrorSAP($body)
    {
        $decoded = json_decode($body, true);
        // dd($decoded['resultado']);

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
