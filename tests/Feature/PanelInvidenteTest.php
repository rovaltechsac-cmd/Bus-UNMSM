<?php

namespace Tests\Feature;

use App\Models\Paradero;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PanelInvidenteTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_de_invidente_envia_directamente_al_panel_accesible(): void
    {
        $invidente = User::factory()
            ->invidente()
            ->conClavePorDefecto('20240002')
            ->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => '20240002',
            'password' => '20240002',
        ]);

        $respuesta->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($invidente);
    }

    public function test_panel_accesible_avisa_automaticamente_sin_botones(): void
    {
        $invidente = User::factory()->invidente()->create();

        $this->actingAs($invidente)
            ->get(route('pasajero.invidente.index'))
            ->assertOk()
            ->assertSee('Aviso automático al conductor')
            ->assertSee('Avisos de audio')
            ->assertDontSee('Avisar que estoy en el paradero')
            ->assertDontSee('Soy persona invidente')
            ->assertDontSee('Modo accesible</h2>', false);
    }

    public function test_pasajero_no_invidente_no_puede_abrir_el_panel_accesible(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('pasajero.invidente.index'))
            ->assertForbidden();
    }

    public function test_panel_normal_no_ofrece_la_opcion_de_declararse_invidente(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('pasajero.index'))
            ->assertOk()
            ->assertSee('Avisos de audio')
            ->assertDontSee('Soy persona invidente')
            ->assertDontSee('Avisar que estoy en el paradero')
            ->assertDontSee('Modo accesible</h2>', false);
    }

    public function test_pasajero_ve_solo_paraderos_activos_en_mi_bus(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Puerta Principal']);
        Paradero::factory()->create(['nombre' => 'Paradero Cerrado', 'activo' => false]);

        $this->actingAs(User::factory()->create())
            ->get(route('pasajero.index'))
            ->assertOk()
            ->assertSee('Mapa del campus')
            ->assertSee($paradero->nombre)
            ->assertDontSee('Paradero Cerrado');
    }

    public function test_conductor_ve_el_mapa_con_los_paraderos_activos(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Puerta Principal']);
        Paradero::factory()->create(['nombre' => 'Paradero Cerrado', 'activo' => false]);

        $this->actingAs(User::factory()->conductor()->create())
            ->get(route('conductor.index'))
            ->assertOk()
            ->assertSee('Mapa del recorrido')
            ->assertSee('id="mapa"', false)
            ->assertSee('configuracionConductor')
            ->assertSee($paradero->nombre)
            ->assertDontSee('Paradero Cerrado');
    }
}
