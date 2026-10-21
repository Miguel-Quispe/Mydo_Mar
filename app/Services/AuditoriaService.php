<?php

namespace App\Services;

use App\Models\UserCredencialAuditoria;
use App\Models\UserCuenta;

class AuditoriaService
{
    /**
     * Registra un evento inmutable en users_credenciales_auditoria (Patrón Append-Only).
     *
     * @param int $idUsuarioAfectado
     * @param int|null $idUsuarioModifica
     * @param string $tipoCambio
     * @param string|null $valorAnteriorHash
     * @param string|null $valorNuevoHash
     * @return UserCredencialAuditoria
     */
    public static function registrarEvento(
        int $idUsuarioAfectado,
        ?int $idUsuarioModifica,
        string $tipoCambio,
        ?string $valorAnteriorHash = null,
        ?string $valorNuevoHash = null
    ): UserCredencialAuditoria {
        $idDimTiempo = DimTiempoService::obtenerOCrearSmartKey();

        // Si no se especifica el modificador, buscar el gerente de la empresa o el mismo usuario
        if (!$idUsuarioModifica) {
            $afectado = UserCuenta::find($idUsuarioAfectado);
            if ($afectado && $afectado->id_empresa_users) {
                $gerente = UserCuenta::where('id_empresa_users', $afectado->id_empresa_users)
                    ->where('id_rol_users', 2) // Gerente Empresa
                    ->first();
                $idUsuarioModifica = $gerente ? $gerente->id_usuario_users : $idUsuarioAfectado;
            } else {
                $idUsuarioModifica = $idUsuarioAfectado;
            }
        }

        return UserCredencialAuditoria::create([
            'id_usuario_users' => $idUsuarioAfectado,
            'id_usuario_modifica_users' => $idUsuarioModifica,
            'id_dim_tiempo_evento' => $idDimTiempo,
            'tipo_cambio' => $tipoCambio,
            'valor_anterior_hash' => $valorAnteriorHash,
            'valor_nuevo_hash' => $valorNuevoHash,
        ]);
    }
}
