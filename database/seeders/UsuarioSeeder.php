<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Cuentas de demostración. La contraseña inicial es igual al código.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador Demo',
                'codigo' => 'ADMIN001',
                'role' => User::ROL_ADMIN,
                'es_invidente' => false,
            ],
            [
                'name' => 'Conductor Demo',
                'codigo' => 'COND001',
                'role' => User::ROL_CONDUCTOR,
                'es_invidente' => false,
            ],
            [
                'name' => 'Estudiante Demo',
                'codigo' => '20240001',
                'role' => User::ROL_PASAJERO,
                'es_invidente' => false,
            ],
            [
                'name' => 'Estudiante Invidente Demo',
                'codigo' => '20240002',
                'role' => User::ROL_PASAJERO,
                'es_invidente' => true,
            ],
        ];

        foreach ($usuarios as $usuario) {
            $usuario['email'] ??= strtolower($usuario['codigo']).'@unmsm.edu.pe';

            User::query()->updateOrCreate(
                ['codigo' => $usuario['codigo']],
                $usuario + [
                    'password' => Hash::make($usuario['codigo']),
                    'activo' => true,
                ],
            );
        }
    }
}
