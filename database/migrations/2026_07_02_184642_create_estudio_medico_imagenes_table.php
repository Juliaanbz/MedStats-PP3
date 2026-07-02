<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudio_medico_imagenes', function (Blueprint $table) {
            $table->id();
            // Se conecta con tu tabla estudio_medicos. Si se borra el estudio, se borran sus fotos automáticamente
            $table->foreignId('estudio_medico_id')->constrained('estudio_medicos')->onDelete('cascade');
            $table->text('ruta'); // Guarda el link web o el nombre del archivo local
            $table->enum('tipo', ['local', 'url'])->default('local'); // Para diferenciar cómo mostrarla
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudio_medico_imagenes');
    }
};