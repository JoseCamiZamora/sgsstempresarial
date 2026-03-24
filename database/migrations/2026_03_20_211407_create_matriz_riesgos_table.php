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
        Schema::create('matriz_riesgos', function (Blueprint $table) {
            $table->id();
            
            // Ubicación y Actividad
            $table->string('proceso');
            $table->string('zona_lugar');
            $table->string('actividad');
            $table->boolean('es_rutinaria')->default(true);
            
            // Peligro
            $table->string('clasificacion_peligro'); // Ej: Biomecánico, Físico, Psicosocial
            $table->text('descripcion_peligro');
            $table->text('efectos_posibles');
            
            // Valoración del Riesgo (Simplificada por ahora)
            $table->string('nivel_riesgo'); // Ej: Bajo, Medio, Alto, Extremo
            
            // Auditoría
            // 1. Creamos la columna del tamaño exacto
            $table->unsignedInteger('registrado_por');
            // 2. Hacemos la conexión manual
            $table->foreign('registrado_por')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriz_riesgos');
    }
};
