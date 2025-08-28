<?php

// app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ParteController;
use Illuminate\Http\Client\ConnectionException;

class ClienteController extends Controller
{
    protected ParteController $parteController;

    public function __construct(ParteController $parteController)
    {
        $this->parteController = $parteController;
    }

    public function index()
    {
        return view('index');
    }

    public function show($DocNum)
    {
        // Assuming the search is by DocNum
        $partes = $this->consultarPartes(['DocNum' => $DocNum]);
        $parte = $partes[0] ?? null; // Get the first result

        if (!$parte) {
            return redirect()->route('home')->with('error', 'No se encontró el parte con el DocNum especificado.');
        }

        // Pass the single 'parte' to the view
        return view('parteFormulario', compact('parte'));
    }


    public function buscar(Request $request)
    {
        $this->validarCamposBusqueda($request);

        $busqueda = array_filter($request->except(['_token', 'Status']));

        if (empty($busqueda)) {
            return back()->with('error', 'Debes rellenar al menos un campo para buscar.');
        }
        if (count($busqueda) > 1) {
            return back()->with('error', 'Solo se puede buscar por un campo a la vez.');
        }

        $partes = $this->consultarPartes($busqueda);

        if (empty($partes)) {
            return back()->with('error', 'No se encontraron resultados para tu búsqueda.');
        }

        $cliente = $this->parteController->consultarClientes($partes[0]['CustomerCode']);
        $origenes = $this->parteController->consultarOrigen();

        if (count($partes) > 1) {
            return view('parteFormulario', [
                'cliente' => $cliente[0] ?? null,
                'partes' => $partes,
                'origenes' => $origenes
            ])->with('success', 'Se encontraron varios partes. Por favor, selecciona uno.');
        }

        return view('parteFormulario', [
            'parte' => $partes[0],
            'cliente' => $cliente[0] ?? null,
            'origenes' => $origenes
        ])->with('success', 'Parte encontrado correctamente.');
    }


    private function consultarPartes(array $input): array
    {
        $accion = "consultar_ServiceCalls";
        $col = array_key_first($input);
        $val = strtoupper(trim($input[$col]));

        $data = [
            "where" => "substringof('$val', $col)",
            "order" => "ServiceCallID desc"
        ];

        try {
            Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
            $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
                'json' => json_encode([
                    'accion' => $accion,
                    'usuario' => 'dani',
                    'datos' => $data
                ])
            ]);

            $body = $response->json();

            if (isset($body['error'])) {
                Log::error('Error en la respuesta de SAP', ['error' => $body['error']]);
                return []; // Return empty array on API error
            }

            return $body['value'] ?? [];

        } catch (ConnectionException $e) {
            Log::error('Error de conexión con SAP', ['exception' => $e->getMessage()]);
            return []; // Return empty array on connection error
        } catch (\Exception $e) {
            Log::error('Error inesperado al consultar partes', ['exception' => $e->getMessage()]);
            return []; // Return empty array for any other exception
        }
    }

    private function validarCamposBusqueda(Request $request)
    {
        return $request->validate([
            'DocNum' => 'nullable|numeric',
            'ServiceCallID' => 'nullable|numeric',
            'U_H8_RMA' => 'nullable|string',
            'U_H8_MOTIVO' => 'nullable|string',
            'Status' => 'nullable|string',
            'Telephone' => 'nullable|numeric|digits_between:9,12',
            'U_H8_Telefono' => 'nullable|numeric|digits_between:9,12',
            'CardCode' => 'nullable|min:8',
            'CardName' => 'nullable|string'
        ], [
            'DocNum.numeric' => 'El número de pedido debe ser numérico.',
            'ServiceCallID.numeric' => 'El número de pedido debe ser numérico.',
            'Telephone.numeric' => 'El número de teléfono debe ser numérico.',
            'U_H8_Telefono.numeric' => 'El número de teléfono debe ser numérico.',
            'CardCode.min' => 'El código de cliente debe tener al menos 8 caracteres.'
        ]);
    }

    public function avisar($id, Request $request)
    {
        // This call assumes ParteController has a public consultarPartes method
        $parte = $this->parteController->consultarPartes($id, 'ServiceCallID');

        if (empty($parte)) {
            return redirect()->back()->with('error', 'Parte no encontrado para enviar el aviso.');
        }

        $telefono_alternativo = $request->input('telefono_alternativo');
        $telefono_original = $request->input('telefono_original');

        if ((isset($telefono_alternativo) && strlen($telefono_alternativo) != 9) || strlen($telefono_original) != 9) {
            return redirect()->back()->with('error', 'El número de teléfono no es válido.');
        }

        $nombre = $parte[0]['CustomerName'] ?? $parte[0]['U_H8_Nombre'];
        $telefono = $telefono_alternativo ?? $telefono_original;

        $url = 'http://192.168.9.7/whatsapp/send_what.php';
        $data = [
            'titulo' => 'Postventa',
            'numero' => '34' . $telefono,
            'mensaje' => 'Estimado/a ' . trim($nombre) . ': Su producto con Nº Pedido ' . $parte[0]['DocNum'] . ' está listo para ser retirado.' // Example of a shorter message
            // ... (full message content) ...
        ];

        try {
            $response = Http::asForm()->post($url, $data);
            Log::info('Respuesta del servicio de WhatsApp', ['respuesta' => $response->body()]);

            if ($response->successful()) {
                return redirect()->route('home')->with('success', 'Mensaje enviado correctamente a ' . $nombre);
            } else {
                return redirect()->route('home')->with('error', 'Error al enviar el mensaje de WhatsApp.');
            }
        } catch (ConnectionException $e) {
            Log::error('Error de conexión con el servicio de WhatsApp', ['exception' => $e->getMessage()]);
            return redirect()->route('home')->with('error', 'No se pudo conectar con el servicio de WhatsApp.');
        }
    }
}
