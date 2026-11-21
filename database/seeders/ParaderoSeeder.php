<?php

namespace Database\Seeders;

use App\Models\Paradero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParaderoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Coordenadas provisionales alrededor de la Ciudad Universitaria.
     * Reemplazar con las coordenadas oficiales cuando San Marcos las proporcione.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $paraderos = [
        [
            'nombre' => 'Puerta Principal',
            'descripcion' => 'Ingreso principal por Av. Carlos Germán Amezaga',
            'latitud' => -12.0582000,
            'longitud' => -77.0840000,
            'orden' => 1,
        ],
        [
            'nombre' => 'Estadio y Coliseo',
            'descripcion' => 'Frente al estadio de la UNMSM',
            'latitud' => -12.0576000,
            'longitud' => -77.0833000,
            'orden' => 2,
        ],
        [
            'nombre' => 'Facultad de Medicina',
            'descripcion' => 'Zona sur de la Ciudad Universitaria',
            'latitud' => -12.0590000,
            'longitud' => -77.0834000,
            'orden' => 3,
        ],
        [
            'nombre' => 'Biblioteca Central',
            'descripcion' => 'Área central de estudios',
            'latitud' => -12.0585000,
            'longitud' => -77.0826000,
            'orden' => 4,
        ],
        [
            'nombre' => 'Facultad de Derecho',
            'descripcion' => 'Zona este del campus',
            'latitud' => -12.0579000,
            'longitud' => -77.0823000,
            'orden' => 5,
        ],
        [
            'nombre' => 'Comedor Universitario',
            'descripcion' => 'Zona de comedor y servicios',
            'latitud' => -12.0594000,
            'longitud' => -77.0824000,
            'orden' => 6,
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->paraderos as $paradero) {
            Paradero::query()->updateOrCreate(
                ['nombre' => $paradero['nombre']],
                $paradero + ['activo' => true],
            );
        }
    }
}
