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
        Schema::create('estudio_medicos', function (Blueprint $table) {
            $table->id();

            // Claves foráneas (Relaciones)
            // cascadeOnDelete() evita errores si se borra un registro raíz, o puedes usar nullable() si lo deseas
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_solicitante_id')->constrained('empleados')->cascadeOnDelete();

            // Campos de datos del estudio
            $table->string('tipo_estudio', 150);
            $table->date('fecha');
            $table->text('resultado')->nullable(); // nullable porque puede que se cargue el estudio antes del resultado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudio_medicos');
    }
};
