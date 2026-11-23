<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_con_codigo_y_clave_por_defecto_envia_al_pasajero_a_su_panel(): void
    {
        $pasajero = User::factory()->conClavePorDefecto('20240001')->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => '20240001',
            'password' => '20240001',
        ]);

        $respuesta->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($pasajero);
    }

    public function test_login_del_administrador_envia_a_su_panel(): void
    {
        $administrador = User::factory()->administrador()->conClavePorDefecto('ADMIN001')->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => 'ADMIN001',
            'password' => 'ADMIN001',
        ]);

        $respuesta->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($administrador);
    }

    public function test_login_del_conductor_envia_a_su_panel(): void
    {
        $conductor = User::factory()->conductor()->conClavePorDefecto('COND001')->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => 'COND001',
            'password' => 'COND001',
        ]);

        $respuesta->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($conductor);
    }

    public function test_login_con_clave_incorrecta_muestra_error_y_no_autentica(): void
    {
        User::factory()->conClavePorDefecto('20240003')->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => '20240003',
            'password' => 'clave-erronea',
        ]);

        $respuesta->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_ingresar(): void
    {
        User::factory()->inactivo()->conClavePorDefecto('20240004')->create();

        $respuesta = $this->post(route('login.store'), [
            'codigo' => '20240004',
            'password' => '20240004',
        ]);

        $respuesta->assertSessionHasErrors('codigo');
        $this->assertGuest();
    }

    public function test_el_enlace_para_cambiar_clave_desaparece_despues_de_usarlo(): void
    {
        $pasajero = User::factory()->conClavePorDefecto('20240005')->create();

        $this->actingAs($pasajero)
            ->get(route('pasajero.index'))
            ->assertOk()
            ->assertSee('Cambiar clave');

        $this->actingAs($pasajero)
            ->put(route('clave.update'), [
                'current_password' => '20240005',
                'password' => 'nueva-clave-123',
                'password_confirmation' => 'nueva-clave-123',
            ])
            ->assertRedirect(route('dashboard.index'));

        $this->assertNotNull($pasajero->fresh()->clave_cambiada_at);

        $this->actingAs($pasajero->fresh())
            ->get(route('pasajero.index'))
            ->assertOk()
            ->assertDontSee('Cambiar clave');
    }

    public function test_enlace_directo_de_cambiar_clave_redirige_despues_de_cambiarla(): void
    {
        $pasajero = User::factory()->claveYaCambiada()->create();

        $this->actingAs($pasajero)
            ->get(route('clave.edit'))
            ->assertRedirect(route('dashboard.index'))
            ->assertSessionHas('estado');
    }

    public function test_visitante_es_redirigido_al_login_al_abrir_el_panel(): void
    {
        $this->get(route('pasajero.index'))->assertRedirect(route('login'));
    }

    public function test_conductor_no_puede_abrir_el_panel_del_pasajero(): void
    {
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)
            ->get(route('pasajero.index'))
            ->assertForbidden();
    }
}
