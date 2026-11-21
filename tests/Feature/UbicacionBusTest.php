<?php

namespace Tests\Feature;

use App\Models\Paradero;
use App\Models\UbicacionBus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UbicacionBusTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_conductor_registra_su_ubicacion_y_reemplaza_la_anterior(): void
    {
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)->postJson(route('api.conductor.ubicacion.store'), [
            'latitud' => -12.0582,
            'longitud' => -77.084,
            'rumbo' => 90,
            'exactitud_m' => 12,
        ])->assertCreated();

        $this->actingAs($conductor)->postJson(route('api.conductor.ubicacion.store'), [
            'latitud' => -12.0576,
            'longitud' => -77.0833,
        ])->assertCreated();

        $this->assertSame(1, UbicacionBus::query()->whereBelongsTo($conductor, 'conductor')->count());
        $ubicacion = UbicacionBus::query()->whereBelongsTo($conductor, 'conductor')->firstOrFail();
        $this->assertSame(-12.0576, (float) $ubicacion->latitud);
        $this->assertSame(-77.0833, (float) $ubicacion->longitud);
    }

    public function test_pasajero_no_puede_registrar_la_ubicacion_del_bus(): void
    {
        $pasajero = User::factory()->create();

        $this->actingAs($pasajero)->postJson(route('api.conductor.ubicacion.store'), [
            'latitud' => -12.0582,
            'longitud' => -77.084,
        ])->assertForbidden();

        $this->assertSame(0, UbicacionBus::query()->count());
    }

    public function test_coordenadas_fuera_de_lima_son_rechazadas(): void
    {
        $conductor = User::factory()->conductor()->create();

        $this->actingAs($conductor)->postJson(route('api.conductor.ubicacion.store'), [
            'latitud' => 10.1234567,
            'longitud' => -77.084,
        ])->assertUnprocessable()->assertJsonValidationErrors('latitud');
    }

    public function test_pasajero_ve_la_ubicacion_y_el_tiempo_de_llegada(): void
    {
        $paradero = Paradero::factory()->create([
            'latitud' => -12.0582,
            'longitud' => -77.084,
        ]);
        $conductor = User::factory()->conductor()->create(['name' => 'Conductor Prueba']);
        UbicacionBus::factory()->create([
            'conductor_id' => $conductor->id,
            'latitud' => -12.0582,
            'longitud' => -77.084,
            'registrado_at' => now(),
        ]);

        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson(route('api.bus.show', ['paradero_id' => $paradero->id]))
            ->assertOk();

        $respuesta->assertJsonPath('activo', true)
            ->assertJsonPath('bus.conductor', 'Conductor Prueba')
            ->assertJsonPath('eta.minutos', 0)
            ->assertJsonPath('eta.texto', 'El bus está en el paradero')
            ->assertJsonPath('eta.llega_pronto', true);
    }

    public function test_ubicacion_antigua_se_muestra_inactiva_sin_eta(): void
    {
        $paradero = Paradero::factory()->create();
        UbicacionBus::factory()->create(['registrado_at' => now()->subMinutes(10)]);

        $respuesta = $this->actingAs(User::factory()->create())
            ->getJson(route('api.bus.show', ['paradero_id' => $paradero->id]))
            ->assertOk();

        $respuesta->assertJsonPath('activo', false)
            ->assertJsonPath('eta', null);
    }

    public function test_consulta_sin_autenticacion_devuelve_401(): void
    {
        $this->getJson(route('api.bus.show'))->assertUnauthorized();
    }
}
