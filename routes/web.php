<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AlertaAsistenciaController;
use App\Http\Controllers\Api\ParaderoController;
use App\Http\Controllers\Api\UbicacionBusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasajeroController;
use App\Http\Controllers\PasajeroInvidenteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? to_route('dashboard.index')
        : to_route('login');
})->name('inicio');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:login'])
    ->name('login.store');

Route::get('/olvide-mi-clave', [AuthController::class, 'showOlvideClave'])
    ->middleware('guest')
    ->name('clave.olvide');

Route::post('/olvide-mi-clave', [AuthController::class, 'enviarOlvideClave'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('clave.olvide.store');

Route::get('/restablecer-clave/{token}', [AuthController::class, 'showRestablecerClave'])
    ->middleware('guest')
    ->name('clave.restablecer');

Route::post('/restablecer-clave', [AuthController::class, 'restablecerClave'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('clave.restablecer.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/cambiar-clave', [AuthController::class, 'showCambiarClave'])->name('clave.edit');
    Route::put('/cambiar-clave', [AuthController::class, 'updateClave'])->name('clave.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/admin', [AdminController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.index');

    Route::post('/admin/paraderos', [AdminController::class, 'storeParadero'])
        ->middleware('role:admin')
        ->name('admin.paraderos.store');

    Route::get('/pasajero', [PasajeroController::class, 'index'])
        ->middleware('role:pasajero,admin')
        ->name('pasajero.index');

    Route::get('/pasajero/invidente', [PasajeroInvidenteController::class, 'index'])
        ->middleware('role:pasajero,admin')
        ->name('pasajero.invidente.index');

    Route::get('/conductor', [ConductorController::class, 'index'])
        ->middleware('role:conductor')
        ->name('conductor.index');

    Route::prefix('api')->group(function (): void {
        Route::get('/paraderos', [ParaderoController::class, 'index'])->name('api.paraderos.index');
        Route::get('/bus', [UbicacionBusController::class, 'show'])->name('api.bus.show');

        Route::post('/conductor/ubicacion', [UbicacionBusController::class, 'store'])
            ->middleware(['role:conductor', 'throttle:ubicacion'])
            ->name('api.conductor.ubicacion.store');

        Route::get('/conductor/alertas', [AlertaAsistenciaController::class, 'index'])
            ->middleware('role:conductor')
            ->name('api.conductor.alertas.index');

        Route::post('/conductor/alertas/{alerta}/atender', [AlertaAsistenciaController::class, 'atender'])
            ->middleware('role:conductor')
            ->name('api.conductor.alertas.atender');

        Route::post('/conductor/alertas/{alerta}/finalizar', [AlertaAsistenciaController::class, 'finalizar'])
            ->middleware('role:conductor')
            ->name('api.conductor.alertas.finalizar');

        Route::post('/alertas', [AlertaAsistenciaController::class, 'store'])
            ->middleware(['role:pasajero,admin', 'throttle:alertas'])
            ->name('api.alertas.store');

        Route::get('/mis-alertas', [AlertaAsistenciaController::class, 'mias'])
            ->middleware('role:pasajero,admin')
            ->name('api.alertas.mias');

        Route::post('/alertas/{alerta}/cancelar', [AlertaAsistenciaController::class, 'cancelar'])
            ->middleware('role:pasajero,admin')
            ->name('api.alertas.cancelar');
    });
});
