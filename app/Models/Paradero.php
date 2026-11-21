<?php

namespace App\Models;

use Database\Factories\ParaderoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'descripcion', 'latitud', 'longitud', 'orden', 'activo'])]
class Paradero extends Model
{
    /** @use HasFactory<ParaderoFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'activo' => 'boolean',
        ];
    }

    public function alertasAsistencia(): HasMany
    {
        return $this->hasMany(AlertaAsistencia::class);
    }
}
