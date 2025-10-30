<?php
// Ensuring this file is part of the patch.

namespace App\Listeners;

use App\Events\AccionUsuarioRegistrada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Log;

class RegistrarAccionEnLog
{
    public function handle(AccionUsuarioRegistrada $event): void
    {
        Log::create([
            'user_id' => $event->usuario->id,
            'accion' => $event->accion,
            'contexto' => $event->contexto ? json_encode($event->contexto) : null,
        ]);
    }
}
