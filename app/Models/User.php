<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'codigo', 'email', 'password', 'role', 'es_invidente', 'activo', 'telefono', 'clave_cambiada_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_PASAJERO = 'pasajero';

    public const ROL_CONDUCTOR = 'conductor';

    public const ROL_ADMIN = 'admin';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'es_invidente' => 'boolean',
            'activo' => 'boolean',
            'clave_cambiada_at' => 'datetime',
        ];
    }

    public function esConductor(): bool
    {
        return $this->role === self::ROL_CONDUCTOR;
    }

    public function esAdministrador(): bool
    {
        return $this->role === self::ROL_ADMIN;
    }

    public function esInvidente(): bool
    {
        return $this->es_invidente;
    }

    public function puedeCambiarClave(): bool
    {
        return $this->clave_cambiada_at === null;
    }

    public function correoInstitucional(): string
    {
        return strtolower($this->codigo).'@unmsm.edu.pe';
    }

    public function ubicacionBus(): HasOne
    {
        return $this->hasOne(UbicacionBus::class, 'conductor_id');
    }

    public function alertasAsistencia(): HasMany
    {
        return $this->hasMany(AlertaAsistencia::class, 'pasajero_id');
    }
}
