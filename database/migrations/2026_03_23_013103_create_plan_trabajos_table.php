<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('plan_trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('actividad'); // Ej: Capacitación en alturas, Simulacro
            $table->date('fecha_programada'); // Para saber cuándo mandar la alerta

            $table->unsignedInteger('responsable_id');
            // 2. Hacemos la conexión manual
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('cascade');
            
            // El estado de la actividad
            $table->enum('estado', ['Pendiente', 'En Ejecución', 'Ejecutada', 'Cancelada'])->default('Pendiente');
            
            // Espacio preparado para la Fase 2 (Subida de archivos)
            $table->string('evidencia_pdf')->nullable(); 
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_trabajos');
    }
};
