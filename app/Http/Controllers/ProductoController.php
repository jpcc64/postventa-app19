<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\AccionUsuarioRegistrada;
use Illuminate\Support\Facades\Auth;
class ProductoController extends Controller
{
    public function consultarProductos(Request $request)
    {
        AccionUsuarioRegistrada::dispatch(Auth::user(), 'Búsqueda de productos', ['termino' => $request->get('term')]);
        $term = strtoupper(trim($request->get('term')));
        // Sanitizamos la entrada para evitar problemas en el filtro OData
        $term = str_replace("'", "''", $term);

        $accion = "consultar_Items";
        $data = [
            // CORRECCIÓN: Usamos contains() que es más estándar en OData que substringof()
            "select" => "ItemCode,ItemName",
            "where" => "contains(ItemCode, '$term') or contains(ItemName, '$term')",
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

            $result = $response->json(); // Usamos el helper de Laravel para decodificar

            // Verificamos si la respuesta contiene la clave 'value' y es un array
            if (isset($result['value']) && is_array($result['value'])) {
                Log::info('Resultados de la busqueda: ', ['datos' => $result['value']]);

                // CORRECCIÓN CLAVE: Devolvemos una respuesta JSON con la lista de productos.
                return response()->json($result['value']);
            }

            // Si no hay resultados o hay un error, devolvemos un array JSON vacío.
            Log::warning('La respuesta de SAP no contenía una lista de valores válida.', ['respuesta' => $result]);
            return response()->json([]);

        } catch (\Exception $e) {
            Log::error('Excepción al consultar productos en SAP', ['exception' => $e->getMessage()]);
            // En caso de error, devolvemos un JSON vacío con un código de error de servidor.
            return response()->json([], 500);
        }
    }


    public function consultaTecnico(Request $request)
    {
        AccionUsuarioRegistrada::dispatch(Auth::user(), 'Búsqueda de técnico', ['termino' => $request->get('term')]);
        $term = ucfirst(trim($request->get('term')));
        // Sanitizamos la entrada para evitar problemas en el filtro OData
        $term = str_replace("'", "''", $term);

        $accion = "consulta_EmployeesInfo";
        $data = array(
            "select" => "EmployeeID,FirstName",
            "where" => "substringof('$term',FirstName)",
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

            $result = $response->json();
            // Verificamos si la respuesta contiene la clave 'value' y es un array
            if (isset($result['value']) && is_array($result['value'])) {
                Log::info('Resultados de la busqueda: ', ['datos' => $result['value']]);

                // CORRECCIÓN CLAVE: Devolvemos una respuesta JSON con la lista de productos.
                return response()->json($result['value']);
            }

            // Si no hay resultados o hay un error, devolvemos un array JSON vacío.
            Log::warning('La respuesta de SAP no contenía una lista de valores válida.', ['respuesta' => $result]);
            return response()->json([]);

        } catch (\Exception $e) {
            Log::error('Excepción al consultar productos en SAP', ['exception' => $e->getMessage()]);
            // En caso de error, devolvemos un JSON vacío con un código de error de servidor.
            return response()->json([], 500);
        }
    }
}