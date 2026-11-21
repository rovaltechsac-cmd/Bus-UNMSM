<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ubicaciones_bus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conductor_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('rumbo', 5, 2)->nullable();
            $table->unsignedSmallInteger('exactitud_m')->nullable();
            $table->timestamp('registrado_at');
            $table->timestamps();

            $table->index('registrado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicaciones_bus');
    }
};
