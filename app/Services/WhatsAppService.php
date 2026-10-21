<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía las credenciales de acceso de un nuevo empleado por WhatsApp.
     */
    public function enviarCredenciales(string $telefono, string $usuario, string $password): bool
    {
        $mensaje = "🔐 *MADEART B2B - Credenciales de Acceso*\n\n"
                 . "Hola, se ha creado tu cuenta en el sistema de planta.\n\n"
                 . "👤 *Usuario:* `{$usuario}`\n"
                 . "🔑 *Contraseña:* `{$password}`\n\n"
                 . "Ingresa a la plataforma para comenzar tus labores de ensamblaje.";

        return $this->enviarMensaje($telefono, $mensaje);
    }

    /**
     * Notifica a supervisión sobre cambios de estado o hitos de armado en taller.
     */
    public function notificarCambioEstadoTaller(string $telefono, int $ordenId, string $estado): bool
    {
        $mensaje = "🏭 *Actualización de Taller - Orden #{$ordenId}*\n\n"
                 . "El estado actual del proyecto ha cambiado a: *{$estado}*.\n"
                 . "Smart Key HTAP registrado en el sistema.";

        return $this->enviarMensaje($telefono, $mensaje);
    }

    /**
     * Envía el mensaje mediante Meta Cloud API o simula en logs si no hay credenciales configuradas.
     */
    public function enviarMensaje(string $telefono, string $mensaje): bool
    {
        $token = config('services.whatsapp.token');
        $phoneId = config('services.whatsapp.phone_id');

        // Si no hay credenciales en config/services.php o .env, simulación transparente por log
        if (empty($token) || empty($phoneId)) {
            Log::info("WHATSAPP SIMULADO [Destino: {$telefono}]: \n" . $mensaje);
            return true;
        }

        try {
            $response = Http::withToken($token)->post("https://graph.facebook.com/v17.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $telefono,
                'type' => 'text',
                'text' => ['body' => $mensaje]
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("Error al conectar con Meta Cloud API para WhatsApp: " . $e->getMessage());
            return false;
        }
    }
}
