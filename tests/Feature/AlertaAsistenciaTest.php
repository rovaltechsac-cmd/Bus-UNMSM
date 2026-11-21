<?php

namespace Tests\Feature;

use App\Models\AlertaAsistencia;
use App\Models\Paradero;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AlertaAsistenciaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_pasajero_invidente_crea_aviso_y_el_conductor_lo_ve(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Biblioteca Central']);
        $pasajero = User::factory()->invidente()->create(['name' => 'Ana Invidente']);
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($pasajero)->postJson(route('api.alertas.store'), [
            'paradero_id' => $paradero->id,
        ])->assertCreated();

        $this->assertDatabaseHas('alertas_asistencia', [
            'pasajero_id' => $pasajero->id,
            'paradero_id' => $paradero->id,
            'tipo' => AlertaAsistencia::TIPO_INVIDENTE,
            'estado' => AlertaAsistencia::ESTADO_PENDIENTE,
        ]);

        $this->actingAs($conductor)
            ->getJson(route('api.conductor.alertas.index'))
            ->assertOk()
            ->assertJsonPath('alertas.0.paradero.nombre', 'Biblioteca Central')
            ->assertJsonPath('alertas.0.pasajero.name', 'Ana Invidente');
    }

    public function test_no_se_crea_un_aviso_duplicado_en_el_mismo_paradero(): void
    {
        $paradero = Paradero::factory()->create();
        $pasajero = User::factory()->invidente()->create();
        AlertaAsistencia::factory()->create([
            'pasajero_id' => $pasajero->id,
            'paradero_id' => $paradero->id,
        ]);

        $this->actingAs($pasajero)->postJson(route('api.alertas.store'), [
            'paradero_id' => $paradero->id,
        ])->assertOk()->assertJsonPath('mensaje', 'Ya tienes una solicitud activa en este paradero.');

        $this->assertSame(1, AlertaAsistencia::query()->count());
    }

    public function test_el_conductor_atiende_y_finaliza_el_aviso(): void
    {
        $alerta = AlertaAsistencia::factory()->create();
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)
            ->postJson(route('api.conductor.alertas.atender', $alerta))
            ->assertOk()
            ->assertJsonPath('alerta.estado', AlertaAsistencia::ESTADO_EN_ATENCION);

        $this->assertDatabaseHas('alertas_asistencia', [
            'id' => $alerta->id,
            'conductor_id' => $conductor->id,
            'estado' => AlertaAsistencia::ESTADO_EN_ATENCION,
        ]);

        $this->actingAs($conductor)
            ->postJson(route('api.conductor.alertas.finalizar', $alerta))
            ->assertOk()
            ->assertJsonPath('alerta.estado', AlertaAsistencia::ESTADO_ATENDIDA);
    }

    public function test_pasajero_no_puede_atender_un_aviso(): void
    {
        $alerta = AlertaAsistencia::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('api.conductor.alertas.atender', $alerta))
            ->assertForbidden();

        $this->assertSame(AlertaAsistencia::ESTADO_PENDIENTE, $alerta->fresh()->estado);
    }

    public function test_el_pasajero_cancela_su_propio_aviso(): void
    {
        $pasajero = User::factory()->create();
        $alerta = AlertaAsistencia::factory()->create(['pasajero_id' => $pasajero->id]);

        $this->actingAs($pasajero)
            ->postJson(route('api.alertas.cancelar', $alerta))
            ->assertOk()
            ->assertJsonPath('alerta.estado', AlertaAsistencia::ESTADO_CANCELADA);
    }

    public function test_pasajero_no_puede_cancelar_el_aviso_de_otro(): void
    {
        $alerta = AlertaAsistencia::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('api.alertas.cancelar', $alerta))
            ->assertForbidden();

        $this->assertSame(AlertaAsistencia::ESTADO_PENDIENTE, $alerta->fresh()->estado);
    }

    public function test_crear_aviso_requiere_un_paradero_valido(): void
    {
        $this->actingAs(User::factory()->invidente()->create())
            ->postJson(route('api.alertas.store'), ['paradero_id' => 999999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('paradero_id');
    }

    public function test_pasajero_no_invidente_no_puede_crear_avisos(): void
    {
        $paradero = Paradero::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson(route('api.alertas.store'), ['paradero_id' => $paradero->id])
            ->assertForbidden();

        $this->assertDatabaseCount('alertas_asistencia', 0);
    }

    public function test_conductor_no_puede_crear_avisos(): void
    {
        $paradero = Paradero::factory()->create();

        $this->actingAs(User::factory()->conductor()->create())
            ->postJson(route('api.alertas.store'), ['paradero_id' => $paradero->id])
            ->assertForbidden();
    }
}
