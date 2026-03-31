<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluacion_respuestas', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->onDelete('cascade');
            $table->foreignId('item_estandar_id')->constrained('item_estandares')->onDelete('cascade');
            
            // La respuesta del auditor
            $table->enum('calificacion', ['Cumple', 'No Cumple', 'No Aplica'])->nullable();
            
            // Si cumple o no aplica (con justificación), se gana el % del ítem
            $table->decimal('puntaje_obtenido', 5, 2)->default(0);
            
            // Evidencias o notas
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluacion_respuestas');
    }
};