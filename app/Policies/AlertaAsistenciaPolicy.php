<?php

namespace App\Policies;

use App\Models\AlertaAsistencia;
use App\Models\User;

class AlertaAsistenciaPolicy
{
    public function atender(User $usuario, AlertaAsistencia $alerta): bool
    {
        return $usuario->esConductor()
            && in_array($alerta->estado, [
                AlertaAsistencia::ESTADO_PENDIENTE,
                AlertaAsistencia::ESTADO_EN_ATENCION,
            ], true);
    }

    public function finalizar(User $usuario, AlertaAsistencia $alerta): bool
    {
        return $usuario->esConductor()
            && $alerta->conductor_id === $usuario->id
            && $alerta->estado === AlertaAsistencia::ESTADO_EN_ATENCION;
    }

    public function cancelar(User $usuario, AlertaAsistencia $alerta): bool
    {
        return ! $usuario->esConductor()
            && $alerta->pasajero_id === $usuario->id
            && in_array($alerta->estado, [
                AlertaAsistencia::ESTADO_PENDIENTE,
                AlertaAsistencia::ESTADO_EN_ATENCION,
            ], true);
    }
}
