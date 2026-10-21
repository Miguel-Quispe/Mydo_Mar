<?php

namespace App\Services;

use App\Mail\CredencialesEmpleadoMailable;
use App\Models\EmpresaRol;
use App\Models\EmpresaUser;
use App\Models\UserCuenta;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmpleadoBatchService
{
    /**
     * Genera un lote masivo de empleados corporativos dentro de una transacción atómica.
     *
     * @param int $idEmpresa
     * @param int $idEmpresaRol
     * @param string $prefijoUsuario
     * @param int $cantidad
     * @param string $dominioEmail
     * @param int|null $idUsuarioModifica
     * @return array Resumen con credenciales en texto plano para visualización única del gerente
     */
    public function generarLote(
        int $idEmpresa,
        int $idEmpresaRol,
        string $prefijoUsuario,
        int $cantidad = 5,
        string $dominioEmail = '@madeart.com',
        ?int $idUsuarioModifica = null
    ): array {
        return DB::transaction(function () use (
            $idEmpresa,
            $idEmpresaRol,
            $prefijoUsuario,
            $cantidad,
            $dominioEmail,
            $idUsuarioModifica
        ) {
            $empresa = EmpresaUser::findOrFail($idEmpresa);
            $subrol = EmpresaRol::where('id_empresa_rol', $idEmpresaRol)
                ->where('id_empresa_users', $idEmpresa)
                ->firstOrFail();

            $smartKey = DimTiempoService::obtenerOCrearSmartKey();
            $prefijoLimpio = Str::slug($prefijoUsuario, '_');
            
            // Buscar correlativo inicial más alto para no colisionar nombres
            $usuariosExistentes = UserCuenta::where('nombre_usuario_users', 'LIKE', "{$prefijoLimpio}_%")
                ->pluck('nombre_usuario_users');

            $maxIndex = 0;
            foreach ($usuariosExistentes as $u) {
                if (preg_match('/_(\d+)$/', $u, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $maxIndex) {
                        $maxIndex = $num;
                    }
                }
            }

            $cuentasCreadas = [];

            for ($i = 1; $i <= $cantidad; $i++) {
                $consecutivo = str_pad($maxIndex + $i, 2, '0', STR_PAD_LEFT);
                $username = "{$prefijoLimpio}_{$consecutivo}";
                $email = "{$username}{$dominioEmail}";
                
                // Generar contraseña aleatoria segura y legible
                $plainPassword = ucfirst(Str::random(6)) . rand(10, 99) . '!';
                $passwordHash = Hash::make($plainPassword);

                $cuenta = UserCuenta::create([
                    'id_empresa_users' => $empresa->id_empresa_users,
                    'id_rol_users' => 3, // Empleado
                    'id_empresa_rol' => $subrol->id_empresa_rol,
                    'id_dim_tiempo_registro' => $smartKey,
                    'nombre_usuario_users' => $username,
                    'email_usuario_users' => $email,
                    'password_hash_users' => $passwordHash,
                    'cuenta_generada_por_empresa' => true,
                ]);

                // Registro OBLIGATORIO en auditoría forense inmutable (Append-Only)
                AuditoriaService::registrarEvento(
                    $cuenta->id_usuario_users,
                    $idUsuarioModifica,
                    'CREACION_LOTE_EMPLEADO',
                    null,
                    $passwordHash
                );

                // Notificaciones Automatizadas (Punto 4: Email y WhatsApp)
                try {
                    // 1. Enviar Correo Electrónico
                    if (!empty($cuenta->email_usuario_users)) {
                        Mail::to($cuenta->email_usuario_users)->send(new CredencialesEmpleadoMailable($cuenta, $plainPassword));
                    }

                    // 2. Enviar WhatsApp (al teléfono corporativo / WhatsApp registrado)
                    $telefonoDestino = $empresa->telefono_whatsapp_empresa_users ?? null;
                    if (!empty($telefonoDestino)) {
                        (new WhatsAppService())->enviarCredenciales($telefonoDestino, $cuenta->nombre_usuario_users, $plainPassword);
                    }
                } catch (\Throwable $e) {
                    Log::error("Error al enviar notificaciones al empleado {$cuenta->nombre_usuario_users}: " . $e->getMessage());
                }

                $cuentasCreadas[] = [
                    'id' => $cuenta->id_usuario_users,
                    'username' => $username,
                    'email' => $email,
                    'plain_password' => $plainPassword,
                    'subrol' => $subrol->nombre_rol_empresa,
                    'fecha_hora' => now()->format('Y-m-d H:i:s'),
                ];
            }

            return [
                'success' => true,
                'total_creados' => count($cuentasCreadas),
                'empresa' => $empresa->razon_social_empresa_users,
                'subrol' => $subrol->nombre_rol_empresa,
                'smart_key_tiempo' => $smartKey,
                'cuentas' => $cuentasCreadas,
            ];
        });
    }

    /**
     * Resetea la contraseña de un empleado y registra el evento forense.
     */
    public function resetearPassword(int $idUsuario, ?int $idUsuarioModifica = null): array
    {
        return DB::transaction(function () use ($idUsuario, $idUsuarioModifica) {
            $cuenta = UserCuenta::findOrFail($idUsuario);
            $hashAnterior = $cuenta->password_hash_users;

            $nuevaPasswordPlana = ucfirst(Str::random(6)) . rand(10, 99) . '$';
            $nuevoHash = Hash::make($nuevaPasswordPlana);

            $cuenta->password_hash_users = $nuevoHash;
            $cuenta->save();

            // Registro inmutable
            AuditoriaService::registrarEvento(
                $cuenta->id_usuario_users,
                $idUsuarioModifica,
                'RESET_PASSWORD',
                $hashAnterior,
                $nuevoHash
            );

            // Notificación automatizada del nuevo acceso
            try {
                if (!empty($cuenta->email_usuario_users)) {
                    Mail::to($cuenta->email_usuario_users)->send(new CredencialesEmpleadoMailable($cuenta, $nuevaPasswordPlana));
                }
                $telefonoDestino = $cuenta->empresa?->telefono_whatsapp_empresa_users ?? null;
                if (!empty($telefonoDestino)) {
                    (new WhatsAppService())->enviarCredenciales($telefonoDestino, $cuenta->nombre_usuario_users, $nuevaPasswordPlana);
                }
            } catch (\Throwable $e) {
                Log::error("Error al notificar reseteo de password al empleado {$cuenta->nombre_usuario_users}: " . $e->getMessage());
            }

            return [
                'success' => true,
                'username' => $cuenta->nombre_usuario_users,
                'nueva_password' => $nuevaPasswordPlana,
            ];
        });
    }

    /**
     * Actualiza el subrol operativo de un empleado y registra auditoría.
     */
    public function cambiarSubrol(int $idUsuario, int $nuevoSubrolId, ?int $idUsuarioModifica = null): void
    {
        DB::transaction(function () use ($idUsuario, $nuevoSubrolId, $idUsuarioModifica) {
            $cuenta = UserCuenta::findOrFail($idUsuario);
            $subrolAnterior = $cuenta->rolOperativo ? $cuenta->rolOperativo->nombre_rol_empresa : 'Sin Subrol';
            
            $cuenta->id_empresa_rol = $nuevoSubrolId;
            $cuenta->save();
            $cuenta->load('rolOperativo');

            $nuevoSubrolNombre = $cuenta->rolOperativo ? $cuenta->rolOperativo->nombre_rol_empresa : 'Sin Subrol';

            // Registro inmutable
            AuditoriaService::registrarEvento(
                $cuenta->id_usuario_users,
                $idUsuarioModifica,
                "CAMBIO_SUBROL: {$subrolAnterior} -> {$nuevoSubrolNombre}",
                null,
                null
            );
        });
    }
}
