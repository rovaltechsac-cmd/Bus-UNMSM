<?php

namespace Tests\Feature;

use App\Models\Paradero;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PanelAdministradorTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_puede_abrir_el_panel_y_ver_los_paraderos(): void
    {
        $paradero = Paradero::factory()->create([
            'nombre' => 'Puerta Principal',
            'latitud' => -12.0582,
            'longitud' => -77.084,
        ]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Agregar paraderos')
            ->assertSee('Usar mi ubicación actual')
            ->assertSee('Puerta Principal')
            ->assertSee('-12.0582000')
            ->assertSee('-77.0840000');
    }

    public function test_visitante_es_redirigido_al_login(): void
    {
        $this->get(route('admin.index'))
            ->assertRedirect(route('login'));
    }

    public function test_pasajero_no_puede_abrir_el_panel_de_administracion(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_conductor_no_puede_agregar_paraderos(): void
    {
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)
            ->post(route('admin.paraderos.store'), [
                'nombre' => 'Paradero no autorizado',
                'latitud' => -12.0582,
                'longitud' => -77.084,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('paraderos', ['nombre' => 'Paradero no autorizado']);
    }

    public function test_crear_paradero_requiere_nombre_y_coordenadas(): void
    {
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->post(route('admin.paraderos.store'), [])
            ->assertSessionHasErrors([
                'nombre' => 'Escribe el nombre del paradero.',
                'latitud' => 'Indica la latitud del paradero.',
                'longitud' => 'Indica la longitud del paradero.',
            ]);
    }

    public function test_las_coordenadas_deben_estar_en_un_rango_valido(): void
    {
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->post(route('admin.paraderos.store'), [
                'nombre' => 'Paradero fuera de rango',
                'latitud' => 91,
                'longitud' => -181,
            ])
            ->assertSessionHasErrors([
                'latitud' => 'La latitud debe estar entre -90 y 90.',
                'longitud' => 'La longitud debe estar entre -180 y 180.',
            ]);
    }

    public function test_no_se_puede_repetir_el_nombre_de_un_paradero(): void
    {
        Paradero::factory()->create(['nombre' => 'Biblioteca Central']);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->post(route('admin.paraderos.store'), [
                'nombre' => 'Biblioteca Central',
                'latitud' => -12.0585,
                'longitud' => -77.0826,
            ])
            ->assertSessionHasErrors([
                'nombre' => 'Ya existe un paradero con ese nombre.',
            ]);
    }

    public function test_admin_crea_un_paradero_y_calcula_su_orden(): void
    {
        Paradero::factory()->create(['orden' => 5]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->post(route('admin.paraderos.store'), [
                'nombre' => 'Nuevo Paradero',
                'descripcion' => 'Ingreso norte',
                'latitud' => -12.0582,
                'longitud' => -77.084,
            ])
            ->assertRedirect(route('admin.index'))
            ->assertSessionHas('estado', 'Paradero agregado correctamente.');

        $paradero = Paradero::query()->where('nombre', 'Nuevo Paradero')->firstOrFail();

        $this->assertSame('Ingreso norte', $paradero->descripcion);
        $this->assertSame(6, $paradero->orden);
        $this->assertTrue($paradero->activo);
        $this->assertEqualsWithDelta(-12.0582, (float) $paradero->latitud, 0.0000001);
        $this->assertEqualsWithDelta(-77.084, (float) $paradero->longitud, 0.0000001);
    }

    public function test_admin_puede_abrir_el_formulario_de_edicion(): void
    {
        $paradero = Paradero::factory()->create([
            'nombre' => 'Puerta Principal',
            'latitud' => -12.0582,
            'longitud' => -77.084,
        ]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->get(route('admin.paraderos.edit', $paradero))
            ->assertOk()
            ->assertSee('Editar paradero')
            ->assertSee('Usar mi ubicación actual')
            ->assertSee('Paradero activo')
            ->assertSee('Puerta Principal')
            ->assertSee('-12.0582000')
            ->assertSee('-77.0840000');
    }

    public function test_admin_actualiza_un_paradero_y_puede_desactivarlo(): void
    {
        $paradero = Paradero::factory()->create(['activo' => true]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->put(route('admin.paraderos.update', $paradero), [
                'nombre' => 'Paradero actualizado',
                'descripcion' => 'Nueva descripción',
                'latitud' => -12.059,
                'longitud' => -77.085,
            ])
            ->assertRedirect(route('admin.index'))
            ->assertSessionHas('estado', 'Paradero actualizado correctamente.');

        $paradero->refresh();

        $this->assertSame('Paradero actualizado', $paradero->nombre);
        $this->assertSame('Nueva descripción', $paradero->descripcion);
        $this->assertEqualsWithDelta(-12.059, (float) $paradero->latitud, 0.0000001);
        $this->assertEqualsWithDelta(-77.085, (float) $paradero->longitud, 0.0000001);
        $this->assertFalse($paradero->activo);
    }

    public function test_admin_puede_mantener_el_nombre_al_editar_un_paradero(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Biblioteca Central']);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->put(route('admin.paraderos.update', $paradero), [
                'nombre' => 'Biblioteca Central',
                'latitud' => $paradero->latitud,
                'longitud' => $paradero->longitud,
                'activo' => 1,
            ])
            ->assertRedirect(route('admin.index'))
            ->assertSessionHasNoErrors();
    }

    public function test_no_se_puede_repetir_el_nombre_de_otro_paradero_al_editar(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Puerta Principal']);
        Paradero::factory()->create(['nombre' => 'Biblioteca Central']);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->put(route('admin.paraderos.update', $paradero), [
                'nombre' => 'Biblioteca Central',
                'latitud' => -12.0582,
                'longitud' => -77.084,
            ])
            ->assertSessionHasErrors([
                'nombre' => 'Ya existe un paradero con ese nombre.',
            ]);
    }

    public function test_admin_activa_un_paradero_desde_la_tabla(): void
    {
        $paradero = Paradero::factory()->create(['activo' => false]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->patch(route('admin.paraderos.estado', $paradero), ['activo' => 1])
            ->assertRedirect(route('admin.index'))
            ->assertSessionHas('estado', 'Paradero activado correctamente.');

        $this->assertTrue($paradero->fresh()->activo);
    }

    public function test_admin_desactiva_un_paradero_desde_la_tabla(): void
    {
        $paradero = Paradero::factory()->create(['activo' => true]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->patch(route('admin.paraderos.estado', $paradero), ['activo' => 0])
            ->assertRedirect(route('admin.index'))
            ->assertSessionHas('estado', 'Paradero desactivado correctamente.');

        $this->assertFalse($paradero->fresh()->activo);
    }

    public function test_el_estado_del_paradero_requiere_un_valor_valido(): void
    {
        $paradero = Paradero::factory()->create(['activo' => true]);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->patch(route('admin.paraderos.estado', $paradero), [])
            ->assertSessionHasErrors('activo');

        $this->assertTrue($paradero->fresh()->activo);
    }

    public function test_pasajero_no_puede_cambiar_el_estado_de_un_paradero(): void
    {
        $paradero = Paradero::factory()->create(['activo' => true]);
        $pasajero = User::factory()->create();

        $this->actingAs($pasajero)
            ->patch(route('admin.paraderos.estado', $paradero), ['activo' => 0])
            ->assertForbidden();

        $this->assertTrue($paradero->fresh()->activo);
    }

    public function test_admin_elimina_un_paradero(): void
    {
        $paradero = Paradero::factory()->create(['nombre' => 'Paradero a eliminar']);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->delete(route('admin.paraderos.destroy', $paradero))
            ->assertRedirect(route('admin.index'))
            ->assertSessionHas('estado', 'Paradero eliminado correctamente.');

        $this->assertDatabaseMissing('paraderos', ['id' => $paradero->id]);
    }

    public function test_pasajero_no_puede_editar_ni_eliminar_paraderos(): void
    {
        $paradero = Paradero::factory()->create();
        $pasajero = User::factory()->create();

        $this->actingAs($pasajero)
            ->get(route('admin.paraderos.edit', $paradero))
            ->assertForbidden();

        $this->actingAs($pasajero)
            ->put(route('admin.paraderos.update', $paradero), [
                'nombre' => 'Cambio no autorizado',
                'latitud' => -12.0582,
                'longitud' => -77.084,
            ])
            ->assertForbidden();

        $this->actingAs($pasajero)
            ->delete(route('admin.paraderos.destroy', $paradero))
            ->assertForbidden();

        $this->assertDatabaseHas('paraderos', ['id' => $paradero->id]);
    }

    public function test_conductor_no_puede_editar_paraderos(): void
    {
        $paradero = Paradero::factory()->create();
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)
            ->get(route('admin.paraderos.edit', $paradero))
            ->assertForbidden();
    }
}
