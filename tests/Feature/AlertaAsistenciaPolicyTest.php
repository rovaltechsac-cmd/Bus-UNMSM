<?php

namespace Tests\Feature;

use App\Models\AlertaAsistencia;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AlertaAsistenciaPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_solo_el_conductor_puede_atender_avisos_pendientes_o_en_atencion(): void
    {
        $alertaPendiente = AlertaAsistencia::factory()->create();
        $alertaEnAtencion = AlertaAsistencia::factory()->enAtencion()->create();
        $alertaAtendida = AlertaAsistencia::factory()->atendida()->create();
        $conductor = User::factory()->conductor()->create();
        $pasajero = User::factory()->create();

        $this->assertTrue(Gate::forUser($conductor)->allows('atender', $alertaPendiente));
        $this->assertTrue(Gate::forUser($conductor)->allows('atender', $alertaEnAtencion));
        $this->assertFalse(Gate::forUser($conductor)->allows('atender', $alertaAtendida));
        $this->assertFalse(Gate::forUser($pasajero)->allows('atender', $alertaPendiente));
    }

    public function test_solo_el_conductor_asignado_puede_finalizar_un_aviso_en_atencion(): void
    {
        $conductorAsignado = User::factory()->conductor()->create();
        $otroConductor = User::factory()->conductor()->create();
        $alerta = AlertaAsistencia::factory()->enAtencion()->create(['conductor_id' => $conductorAsignado->id]);
        $pendiente = AlertaAsistencia::factory()->create();

        $this->assertTrue(Gate::forUser($conductorAsignado)->allows('finalizar', $alerta));
        $this->assertFalse(Gate::forUser($otroConductor)->allows('finalizar', $alerta));
        $this->assertFalse(Gate::forUser(User::factory()->create())->allows('finalizar', $alerta));
        $this->assertFalse(Gate::forUser($conductorAsignado)->allows('finalizar', $pendiente));
    }

    public function test_solo_el_pasajero_propietario_puede_cancelar_un_aviso_activo(): void
    {
        $propietario = User::factory()->create();
        $otroPasajero = User::factory()->create();
        $alerta = AlertaAsistencia::factory()->create(['pasajero_id' => $propietario->id]);
        $cancelada = AlertaAsistencia::factory()->cancelada()->create(['pasajero_id' => $propietario->id]);

        $this->assertTrue(Gate::forUser($propietario)->allows('cancelar', $alerta));
        $this->assertFalse(Gate::forUser($otroPasajero)->allows('cancelar', $alerta));
        $this->assertFalse(Gate::forUser(User::factory()->conductor()->create())->allows('cancelar', $alerta));
        $this->assertFalse(Gate::forUser($propietario)->allows('cancelar', $cancelada));
    }
}
