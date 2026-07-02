<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudio_medicos', function (Blueprint $table) {
            // Usamos text por si los links externos son muy largos (ej. tokens de autenticación)
            // nullable() permite que el campo sea opcional
            $table->text('link_imagen')->nullable()->after('resultado'); 
        });
    }

    public function down(): void
    {
        Schema::table('estudio_medicos', function (Blueprint $table) {
            $table->dropColumn('link_imagen');
        });
    }
};
