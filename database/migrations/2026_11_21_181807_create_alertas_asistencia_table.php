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
        Schema::create('alertas_asistencia', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pasajero_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paradero_id')->constrained('paraderos')->cascadeOnDelete();
            $table->foreignId('conductor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->default('invidente');
            $table->string('estado')->default('pendiente');
            $table->string('nota')->nullable();
            $table->timestamp('atendido_at')->nullable();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
            $table->index(['pasajero_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas_asistencia');
    }
};
