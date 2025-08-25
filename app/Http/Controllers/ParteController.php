<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProductoController;

class ParteController extends Controller
{
    private $productoController;
    private $origen;

    public function __construct(ProductoController $productoController)
    {
        $this->productoController = $productoController;
        $this->origen = $this->consultarOrigen();
    }
    
    public function index()
    {
        return view('parteFormulario', ['origenes' => $this->origen]);
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
        $partes = $this->consultarPartes($clientes[0]['CardCode'],'CustomerCode');
        $tecnico = $this->nombreTecnico($partes[0]['TechnicianCode'] ?? '');
        if (count($partes) >= 2) {
            // si tiene 2 o mas partes saltará el model para elegir que parte usa
            return view('parteFormulario', ['cliente' => $clientes[0], 'partes' => $partes, 'origenes' => $this->origen, 'tecnico' => $tecnico])
                ->with('success', 'Parte encontrado para el cliente.');
        }

        return view('parteFormulario', ['cliente' => $clientes[0], 'parte' => $partes[0] ?? null, 'origenes' => $this->origen, 'tecnico' => $tecnico])
            ->with('success', 'Parte encontrado para el cliente.');
    }

    public function buscarRMA(Request $request){
        $id = $request->input('busquedaRMA');
        if ($id == null) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }
        $busqueda = strtoupper(trim($id));
        $rmas = $this->consultarPartes($busqueda,'U_H8_RMA');
        $cliente = $this->consultarClientes($rmas[0]['CustomerCode'] ?? '');
        $tecnico = $this->nombreTecnico($rmas[0]['TechnicianCode']);


        if (empty($rmas)) {
            return back()->with('error', 'No se encontró ningún RMA.');
        }
        return view('parteFormulario', ['parte' => $rmas[0], 'cliente' => $cliente[0] ?? null, 'origenes' => $this->origen, 'tecnico' => $tecnico])
            ->with('success', 'Parte encontrado.');
    }

    public function nuevoParte($id)
    {
        $cliente = $this->consultarClientes($id);

        // dd($cliente);
        return view('parteFormulario', ['cliente' => $cliente[0],  'origenes' => $this->origen]);
    }

    public function sugerencias(Request $request)
    {
        $term = strtoupper(trim($request->get('term')));
        $term = str_replace("'", "''", $term);

        $accion = "consultar_BusinessPartners";
        $data = array(
            "select" => "CardCode,CardName,Phone1",//Asunto
            "where" => "substringof('$term', CardCode) or substringof('$term', CardName) or substringof('$term', Phone1)",//codigo de interlocutor comercial
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


    private function consultarClientes($busqueda)
    {
        $busqueda = str_replace("'", "''", $busqueda);

        $accion = "consultar_BusinessPartners";
        $data = array(
            "select" => "CardCode,CardName,Phone1,FederalTaxID",//Asunto
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

    private function consultarPartes($customer,$col)
    {
        $accion = "consultar_ServiceCalls";
        $data = array(
            //  "select" => "ServiceCallID,Subject,CustomerCode,",//Asunto
            "where" => "$col eq '$customer'",//codigo de interlocutor comercial
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
        $tecnico = $this->nombreTecnico($parte['TechnicianCode']);

        return view('parteFormulario', ['cliente' => $cliente[0], 'parte' => $parte, 'origenes' => $this->origen, 'tecnico' => $tecnico]);

    }

    public function crear(Request $request)
    {
     //   $this->validarCamposSAP($request);

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
                    'resultado' => $body,
                    'origenes' => $this->consultarOrigen()
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

    private function consultarOrigen()
    {
        $accion = "consulta_ServiceCallsOrigins";
           $data = [
            'select' => "OriginID,Name",
            'where' => "OriginID gt 0",
            'order' => "Name"
           ];

        try {
            Log::info('Consultando origenes desde SAP', ['accion' => $accion, 'datos' => $data]);

            $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
                'json' => json_encode([
                    'accion' => $accion,
                    'usuario' => 'dani',
                    'datos' => $data
                ])
            ]);

            $result = $response->json();

            // Devolvemos la lista de técnicos si existe, o un array vacío si no.
            return $result['value'] ?? [];

        } catch (\Exception $e) {
            Log::error('Excepción al consultar la lista de origenes', ['exception' => $e->getMessage()]);
            return [];
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

    private function validarCamposSAP(Request $request)
    {
        return $request->validate([
            'CustomerCode' => 'required|string',
            'CustomerName' => 'required|string',
            'Telephone' => 'required|string',
            'ItemCode' => 'required|string',
            'ServiceBPType' => 'required|string',
            'Subject' => 'required|string'
        ], [
            'CustomerCode.required' => 'El código del cliente es obligatorio.',
            'CustomerName.required' => 'El nombre del cliente es obligatorio.',
            'Telephone.required' => 'El teléfono es obligatorio.',
            'ItemCode.required' => 'El código del artículo es obligatorio.',
            'ServiceBPType.required' => 'El tipo de interlocutor comercial es obligatorio.',
            'Subject.required' => 'El asunto es obligatorio.'
        ]);
    }
    public function nombreTecnico($id)
    {
        $accion = "consulta_EmployeesInfo";
        $data = array(
            "select" => "FirstName",
            "where" => "EmployeeID eq $id",
        );

        try {
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


        } catch (\Exception $e) {
            Log::error('Excepción al consultar productos en SAP', ['exception' => $e->getMessage()]);
            // En caso de error, devolvemos un JSON vacío con un código de error de servidor.
            return response()->json([], 500);
        }
    }
    public function vistaImprimir($customerCode)
    {
        $parte = $this->consultarPartes($customerCode,'CustomerCode');
        $cliente = $this->consultarClientes($customerCode);
        $tecnico = $this->nombreTecnico($parte[0]['TechnicianCode']);
        //  dd($parte);
        return view('partes.vista_imprimir', [
            'parte' => $parte[0],
            'cliente' => $cliente[0],
            'tecnico' => $tecnico
        ]);
    }
}
