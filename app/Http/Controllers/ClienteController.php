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
        //dd($partes);
        $cliente = $this->parteController->consultarClientes($partes[0]['CustomerCode']);
        $origenes = $this->parteController->consultarOrigen();
        $tecnico = $this->parteController->nombreTecnico($partes[0]['TechnicianCode']);

        if (count($partes) > 1) {
            return view('parteFormulario', [
                'cliente' => $cliente[0] ?? null,
                'partes' => $partes,
                'origenes' => $origenes,
                'tecnico' => $tecnico
            ])->with('success', 'Se encontraron varios partes. Por favor, selecciona uno.');
        }

        return view('parteFormulario', [
            'parte' => $partes[0],
            'cliente' => $cliente[0] ?? null,
            'origenes' => $origenes,
            'tecnico' => $tecnico
        ])->with('success', 'Parte encontrado correctamente.');
    }


    private function consultarPartes(array $input): array
    {
        $accion = "consultar_ServiceCalls";
        $col = array_key_first($input);
        $val = trim($input[$col]);
       //dd($val , $col);
        $condicional = match ($col) {
            'CustomerName', 'U_H8_Nombre' => "substringof('$val', $col)",
            'Telephone', 'U_H8_Telefono' => "$col eq '$val'",
            default => "$col eq $val",
        };
        
        $data = [
            "where" => $condicional,
            "order" => "ServiceCallID desc"
        ];

        try {
            Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
            $response = Http::asForm()->post(env('API_SAP_URL'), [
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
           if (!isset($body['value']) || !is_array($body['value'])) {
                Log::warning('La respuesta de SAP no contenía una lista de valores válida.', ['respuesta' => $body]);
                return []; // Return empty array if 'value' is not present or not an array
            }

            return $body['value'] ;

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
            'numero' => '34' . $telefono, //cambiar por numero de telefono
            'mensaje' => 'Asunto: Producto listo para ser retirado' . PHP_EOL .
                'Estimado/a ' . trim($nombre) . ':' . PHP_EOL . PHP_EOL .
                'Nos complace informarle que su producto ya se encuentra disponible para ser retirado en nuestras instalaciones.' . PHP_EOL . PHP_EOL .
                'Detalles del producto:' . PHP_EOL .
                'Número de parte: ' . $parte[0]['DocNum'] . PHP_EOL .
                'Producto: ' . $parte[0]['ItemDescription'] . PHP_EOL .
                'Fecha de disponibilidad: ' . date(format: 'd/m/Y') . PHP_EOL . PHP_EOL .
                'Puede pasar a retirarlo en el siguiente horario:' . PHP_EOL .
                'Lunes a Sábado de 9:00 a 21:00' . PHP_EOL .
                'Dirección: C. el Henequen, 43 ' . PHP_EOL . PHP_EOL .
                'Por favor, recuerde presentar una copia de su comprobante de compra y un documento de identidad al momento del retiro.' . PHP_EOL . PHP_EOL .
                'Si tiene alguna consulta adicional, no dude en comunicarse con nosotros al 928 85 01 40.' . PHP_EOL . PHP_EOL .
                'Gracias por confiar en nosotros.'
        ];

        try {
            $response = Http::asForm()->post($url, $data);
            Log::info('Respuesta del servicio de WhatsApp', ['respuesta' => $response->body()]);
            $respuesta = $response->body();
        //    dd($respuesta);
            if ($respuesta != "false") {
                return back()->with('success', 'Mensaje enviado correctamente a ' . $nombre);
            } else {
                return back()->with('error', 'Error al enviar el mensaje de WhatsApp.');
            }
        } catch (ConnectionException $e) {
            Log::error('Error de conexión con el servicio de WhatsApp', ['exception' => $e->getMessage()]);
            return back()->with('error', 'No se pudo conectar con el servicio de WhatsApp.');
        }
    }
}
