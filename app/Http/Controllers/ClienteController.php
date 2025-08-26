<?php

// app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ClienteController extends Controller
{
    private function consultarPartes($docNum)
    {
        $accion = "consultar_ServiceCalls";
        $data = array(
            //  "select" => "ServiceCallID,Subject,CustomerCode,",//Asunto
            "where" => "ServiceCallID eq $docNum",//codigo de interlocutor comercial
           // "order" => "ServiceCallID",//Nombre interlocutor comercial
      //      "top" => '1'
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
    public function index()
    {
        return view('index');
    }
    public function show($DocNum)
    {
        $cliente = collect($this->consultarPartes($DocNum))->first();
        return view('parteFormulario', compact('cliente'));
    }

    public function buscar(Request $request)
    {
        $busqueda = strtoupper(trim($request->input('id')));
        if ($busqueda == '' || !is_numeric($busqueda) || strlen($busqueda) >= 12) {
            return redirect()->route('home')->with('error', 'El parte no es válido');
        }

        $clientes = collect($this->consultarPartes($busqueda))->first();


        if (!$clientes) {
            return redirect()->route('home')->with('error', 'No se encontraron resultados');
        }

        return view('index', ['clientes' => $clientes])->with('success', 'Cliente encontrado');
    }

    public function avisar($id, Request $request)
    {
        //BUSCAR AL CLIENTE POR EL CIF
        $parte = collect($this->consultarPartes($id))->first();
        $telefono_alternativo = $request->input('telefono_alternativo');
        $telefono_original = $request->input('telefono_original');
        if (!$parte) {
            return redirect()->back()->with('error', 'cliente no encontrado');
        }
        if(isset($telefono_alternativo) && strlen($telefono_alternativo) != 9 || strlen($telefono_original) != 9){
            return redirect()->back()->with('error', 'El número de teléfono no es válido');
        }
        $nombre = $parte['CustomerName'] ?? $parte['U_H8_Nombre'];
        $telefono = 662480928; //$telefono_alternativo ?? $telefono_original; //QUITAR NUMERO DE TELEFONO

        $url = 'http://192.168.9.7/whatsapp/send_what.php';
        $data = [
            'titulo' => 'Postventa',
            'numero' => '34' . $telefono, //cambiar por numero de telefono
            'mensaje' => 'Asunto: Producto listo para ser retirado' . PHP_EOL .
                'Estimado/a ' . trim($nombre) . ':' . PHP_EOL . PHP_EOL .
                'Nos complace informarle que su producto ya se encuentra disponible para ser retirado en nuestras instalaciones.' . PHP_EOL . PHP_EOL .
                'Detalles del producto:' . PHP_EOL .
                'Número de pedido: ' . $parte['DocNum'] . PHP_EOL .
                'Producto: ' . $parte['ItemDescription'] . PHP_EOL .
                'Fecha de disponibilidad: ' . date(format: 'd/m/Y') . PHP_EOL . PHP_EOL .
                'Puede pasar a retirarlo en el siguiente horario:' . PHP_EOL .
                'Lunes a Viernes de 9:00 a 20:00' . PHP_EOL .
                'Dirección: C. el Henequen, 43 ' . PHP_EOL . PHP_EOL .
                'Por favor, recuerde presentar una copia de su comprobante de compra y un documento de identidad al momento del retiro.' . PHP_EOL . PHP_EOL .
                'Si tiene alguna consulta adicional, no dude en comunicarse con nosotros al 928 85 01 40.' . PHP_EOL . PHP_EOL .
                'Gracias por confiar en nosotros.'
        ];

        $response = Http::asForm()->post($url, $data); 
        Log::info('Respuesta del servicio de WhatsApp', ['respuesta' => $response->body()]);

        if ($response->successful()) {
            return redirect()->route('home')->with('success', 'Mensaje enviado correctamente a ' . $nombre);
        } else {
            return redirect()->route('home')->with('error', 'Error al enviar el mensaje');
        }
    }
}
