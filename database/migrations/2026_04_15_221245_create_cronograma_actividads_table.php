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
        Schema::create('cronogramas_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_plan_id')->constrained('actividades_plan')->cascadeOnDelete();
            
            $table->integer('mes'); // 1 = Enero, 2 = Febrero ... 12 = Diciembre
            
            // Control de Estado (Equivalente a las celdas "P" y "E" de tu Excel)
            $table->boolean('programado')->default(false); 
            $table->boolean('ejecutado')->default(false);  
            
            // Cierre y Evidencias
            $table->date('fecha_ejecucion_real')->nullable();
            $table->string('evidencia_pdf')->nullable(); // Ruta donde guardaremos el PDF
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            
            // REGLA DE ORO: Evita que un usuario programe la misma actividad dos veces en el mismo mes
            $table->unique(['actividad_plan_id', 'mes']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronogramas_actividad');
    }
};
