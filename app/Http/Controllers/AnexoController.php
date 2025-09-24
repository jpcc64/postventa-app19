<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnexoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $parte_id
     * @return \Illuminate\Http\Response
     */
    public function index($parte_id)
    {
        $directory = 'anexos/' . $parte_id;
        $files = Storage::disk('public')->files($directory);

        $attachments = array_map(function ($file) {
            return [
                'name' => basename($file),
                'url' => Storage::disk('public')->url($file)
            ];
        }, $files);

        return response()->json($attachments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $parte_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $parte_id)
    {
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $paths = [];

            foreach ($files as $file) {
                $path = $file->store('anexos/' . $parte_id, 'public');
                $paths[] = $path;
            }

            return response()->json([
                'success' => 'Files uploaded successfully.',
                'paths' => $paths
            ]);
        }

        return response()->json(['error' => 'No files to upload.'], 400);
    }
}
