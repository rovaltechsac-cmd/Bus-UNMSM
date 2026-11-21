<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecuperacionClaveTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_muestra_el_enlace_olvide_mi_clave(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('clave.olvide'));
    }

    public function test_solicitud_de_recuperacion_prepara_un_enlace_para_el_correo_institucional(): void
    {
        User::factory()->create([
            'codigo' => '20240006',
            'email' => '20240006@unmsm.edu.pe',
        ]);

        $respuesta = $this->post(route('clave.olvide.store'), [
            'codigo' => '20240006',
        ]);

        $respuesta->assertRedirect()
            ->assertSessionHas('estado')
            ->assertSessionHas('enlace_recuperacion');

        $this->assertStringContainsString(
            '/restablecer-clave/',
            $respuesta->getSession()->get('enlace_recuperacion'),
        );
    }

    public function test_restablecer_clave_actualiza_la_clave_y_marca_el_cambio(): void
    {
        $usuario = User::factory()->create([
            'codigo' => '20240007',
            'email' => '20240007@unmsm.edu.pe',
        ]);

        $respuesta = $this->post(route('clave.olvide.store'), [
            'codigo' => '20240007',
        ]);

        $token = Str::of($respuesta->getSession()->get('enlace_recuperacion'))
            ->after('/restablecer-clave/')
            ->before('?')
            ->toString();

        $this->post(route('clave.restablecer.store'), [
            'token' => $token,
            'email' => '20240007@unmsm.edu.pe',
            'password' => 'clave-nueva-123',
            'password_confirmation' => 'clave-nueva-123',
        ])->assertRedirect(route('login'));

        $usuario->refresh();

        $this->assertTrue(Hash::check('clave-nueva-123', $usuario->password));
        $this->assertNotNull($usuario->clave_cambiada_at);
    }

    public function test_un_enlace_invalido_no_cambia_la_clave(): void
    {
        $usuario = User::factory()->create([
            'codigo' => '20240008',
            'email' => '20240008@unmsm.edu.pe',
        ]);

        $claveOriginal = $usuario->password;

        $this->post(route('clave.restablecer.store'), [
            'token' => 'token-invalido',
            'email' => '20240008@unmsm.edu.pe',
            'password' => 'clave-nueva-123',
            'password_confirmation' => 'clave-nueva-123',
        ])->assertSessionHasErrors('email');

        $this->assertSame($claveOriginal, $usuario->fresh()->password);
        $this->assertNull($usuario->fresh()->clave_cambiada_at);
    }
}
