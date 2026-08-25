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
        Schema::create('actividades_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_trabajo_id')->constrained('planes_trabajo')->cascadeOnDelete();
            
            // Alineado con el menú que acabamos de ajustar
            $table->enum('fase_phva', ['Planear', 'Hacer', 'Verificar', 'Actuar']); 
            
            $table->text('objetivo_especifico')->nullable();
            $table->string('actividad');
            $table->string('recursos_necesarios')->nullable(); // Ej: Talento Humano, Financiero, Tecnológico
            
            // Declaramos la columna explícitamente como un entero sin signo (el estándar de tu tabla users)
            $table->unsignedInteger('responsable_id');
            // Creamos la relación manualmente
            $table->foreign('responsable_id')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades_plan');
    }
};
