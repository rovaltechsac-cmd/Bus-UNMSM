<?php

namespace App\Providers;

use App\Models\AlertaAsistencia;
use App\Models\User;
use App\Policies\AlertaAsistenciaPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Gate::policy(AlertaAsistencia::class, AlertaAsistenciaPolicy::class);

        Gate::define('ver-panel-invidente', fn (User $usuario): bool => $usuario->esInvidente());

        Gate::define('crear-alerta-invidente', fn (User $usuario): bool => $usuario->esInvidente()
            && in_array($usuario->role, [User::ROL_PASAJERO, User::ROL_ADMIN], true));

        RateLimiter::for('login', function (Request $request): Limit {
            $identificador = Str::lower($request->string('codigo')->toString()).'|'.$request->ip();

            return Limit::perMinute(5)->by($identificador);
        });

        RateLimiter::for('ubicacion', function (Request $request): Limit {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('alertas', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
