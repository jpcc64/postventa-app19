<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SapService
{
    private string $baseUri;
    private array $config;

    public function __construct()
    {
        $this->config = config('services.sap');
        $this->baseUri = $this->config['host'] . '/b1s/v1/';
    }

    /**
     * Obtiene la cookie de sesión de SAP de forma segura usando el caché de Laravel.
     * Esto reemplaza el problemático 'cookie.txt' y es seguro para concurrencia.
     */
    private function getSessionCookie(): string
    {
        // 1. Intenta obtener 'sap_session_cookie' del caché.
        // 2. Si existe y no ha expirado, lo devuelve.
        // 3. Si no existe, ejecuta la función anónima, guarda el resultado en el caché por 20 minutos y lo devuelve.
        return Cache::remember('sap_session_cookie', now()->addMinutes(20), function () {
            Log::info('No hay sesión de SAP en caché. Creando una nueva.');

            $response = Http::withoutVerifying()->post($this->baseUri . 'Login', [
                'CompanyDB' => $this->config['company_db'],
                'UserName' => $this->config['username'],
                'Password' => $this->config['password'],
            ]);

            if ($response->failed()) {
                Log::error('Fallo al iniciar sesión en SAP', ['response' => $response->body()]);
                throw new \Exception('No se pudo autenticar con SAP Service Layer.');
            }

            return $response->header('Set-Cookie');
        });
    }

    /**
     * Crea una nueva Llamada de Servicio.
     * @param array $sapData Los datos ya formateados para SAP.
     * @return array La respuesta JSON de SAP.
     */
    public function createServiceCall(array $sapData): array
    {
        $cookie = $this->getSessionCookie();

        $response = Http::withHeaders(['Cookie' => $cookie])
            ->withoutVerifying()
            ->post($this->baseUri . 'ServiceCalls', $sapData);

        return $response->json();
    }

    /**
     * Modifica una Llamada de Servicio existente.
     * @param int $callID El ID del parte a modificar.
     * @param array $sapData Los datos ya formateados para SAP.
     * @return array La respuesta JSON de SAP o un mensaje de éxito.
     */
    public function updateServiceCall(int $callID, array $sapData): array
    {
        $cookie = $this->getSessionCookie();

        $response = Http::withHeaders(['Cookie' => $cookie])
            ->withoutVerifying()
            ->patch($this->baseUri . "ServiceCalls({$callID})", $sapData);

        if ($response->noContent()) { 
            return ['success' => true];
        }

        if ($response->failed()) {
            Log::error('Fallo al actualizar la llamada de servicio en SAP', [
                'callID' => $callID,
                'response' => $response->body(),
            ]);
            throw new \Exception('No se pudo actualizar la llamada de servicio en SAP.');
        }

    }
}