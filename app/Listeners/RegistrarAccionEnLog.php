<?php

namespace App\Listeners;

use App\Events\AccionUsuarioRegistrada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RegistrarAccionEnLog
{
    public function handle(AccionUsuarioRegistrada $event): void
    {
        $usuario = $event->usuario;
        $accion = $event->accion;
        $contexto = $event->contexto ? json_encode($event->contexto) : '';

        $mensaje = "Usuario: {$usuario->name} (ID: {$usuario->id}) | Acción: {$accion} | Contexto: {$contexto}";

        Log::channel('audit')->info($mensaje);
    }
}