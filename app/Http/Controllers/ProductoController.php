<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ProductoController extends Controller
{
    public function consultarProductos(Request $request)
    {
        $term = strtoupper(trim($request->get('term')));
        $term = str_replace("'", "''", $term);

        $accion = "consultar_Items";
        $data = array(
            "where" => "substringof('$term', ItemCode ) or substringof('$term', ItemName )",//codigo de interlocutor comercial
        );

        Log::info('Enviando datos a SAP', ['accion' => $accion, 'datos' => $data]);
        $response = Http::asForm()->post('http://192.168.9.7/api_sap/index.php', [
            'json' => json_encode([
                'accion' => $accion,
                'usuario' => 'dani',
                'datos' => $data
            ])
        ]);
        $result = json_decode($response->body(), true);
        Log::info('Resultados de la busqueda: ', ['datos' => $result['value']]);
        return $result['value'][0] ?? null; // Retorna el primer resultado o null si no hay resultados
    }
}