<?php
// Ensuring this file is part of the patch.

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccionUsuarioRegistrada
{
    use Dispatchable, SerializesModels;

    public User $usuario;
    public string $accion;
    public ?array $contexto;

    /**
     * Create a new event instance.
     *
     * @param User $usuario El usuario que realiza la acción.
     * @param string $accion Descripción de la acción (ej: "Creó el parte #123").
     * @param array|null $contexto Datos adicionales relevantes (ej: el parte creado).
     */
    public function __construct(User $usuario, string $accion, ?array $contexto = null)
    {
        $this->usuario = $usuario;
        $this->accion = $accion;
        $this->contexto = $contexto;
    }
}