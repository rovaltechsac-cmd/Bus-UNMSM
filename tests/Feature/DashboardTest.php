<?php

namespace Tests\Feature;

use App\Models\AlertaAsistencia;
use App\Models\Paradero;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_administrador_ve_el_resumen_y_los_accesos_de_gestion(): void
    {
        $administrador = User::factory()->administrador()->create();
        Paradero::factory()->count(2)->create();
        $alerta = AlertaAsistencia::factory()->create();

        $this->actingAs($administrador)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Resumen general')
            ->assertSee('Paraderos activos')
            ->assertSee('Avisos activos')
            ->assertSee('Administrar paraderos')
            ->assertSee('Vista del pasajero')
            ->assertSee($alerta->pasajero->name);
    }

    public function test_conductor_ve_sus_indicadores_y_no_los_accesos_de_administrador(): void
    {
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Paraderos en ruta')
            ->assertSee('Atendidos hoy')
            ->assertSee('Mi viaje')
            ->assertDontSee('Administrar paraderos');
    }

    public function test_pasajero_ve_su_resumen_y_acceso_a_mi_bus(): void
    {
        $pasajero = User::factory()->create();
        $alerta = AlertaAsistencia::factory()->create(['pasajero_id' => $pasajero->id]);

        $this->actingAs($pasajero)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Llegada estimada')
            ->assertSee('Mis avisos activos')
            ->assertSee('Mi bus')
            ->assertSee($alerta->paradero->name)
            ->assertDontSee('Administrar paraderos')
            ->assertDontSee('Mi viaje');
    }

    public function test_pasajero_invidente_tiene_acceso_al_panel_accesible(): void
    {
        $invidente = User::factory()->invidente()->create();

        $this->actingAs($invidente)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Panel accesible');
    }

    public function test_visitante_es_redirigido_al_login(): void
    {
        $this->get(route('dashboard.index'))
            ->assertRedirect(route('login'));
    }
}
