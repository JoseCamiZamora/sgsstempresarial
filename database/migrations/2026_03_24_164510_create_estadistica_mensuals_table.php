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
        Schema::create('estadistica_mensuales', function (Blueprint $table) {
            $table->id();
            $table->integer('mes');           // 1 al 12
            $table->integer('anio');          // Ej: 2024
            $table->integer('num_trabajadores'); // Denominador para Frecuencia
            $table->integer('horas_trabajadas'); // Denominador para Severidad (opcional según fórmula)
            $table->integer('dias_programados'); // Para Ausentismo
            $table->timestamps();
            
            // Evitamos que se repita el mismo mes y año
            $table->unique(['mes', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estadistica_mensuals');
    }
};
