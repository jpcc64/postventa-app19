<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function consultarProductos(Request $request)
    {
        $term = $request->input('codigoProducto', '');

        // CORREGIDO: Sintaxis de tabla estándar para SQL Server [dbo].[NYS_PRODUCT]
        $sql = <<<EOT
        SELECT CODIGO_PRODUCTO, NOMBRE_PRODUCTO, CODIGO_FAMILIA
        FROM [dbo].[NYS_PRODUCT]
        WHERE CODIGO_PRODUCTO LIKE :term 
           OR NOMBRE_PRODUCTO LIKE :term
           OR CODIGO_FAMILIA LIKE :term
        EOT;
        dd($sql);
        // Pasamos el término de búsqueda con los comodines '%'
        $productos = DB::select($sql, ['term' => '%' . $term . '%']);

        return response()->json($productos);
    }
}