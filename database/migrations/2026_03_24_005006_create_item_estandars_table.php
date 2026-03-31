<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_estandares', function (Blueprint $table) {
            $table->id();
            
            // Clasificación del SG-SST (Recomendado para reportes)
            $table->string('ciclo')->nullable()->comment('Planear, Hacer, Verificar, Actuar');
            $table->string('numeral')->nullable()->comment('Ej: 1.1.1, 1.1.2...');
            
            // Datos Principales
            $table->text('nombre')->comment('Descripción del estándar a evaluar');
            $table->text('modo_verificacion')->nullable()->comment('Cómo el auditor verifica este ítem');
            
            // Valoración y Puntuación
            $table->decimal('porcentaje', 5, 2)->comment('Peso porcentual máximo del ítem');
            
            // 🚨 La variable "Filtro" para la Resolución 0312
            $table->integer('tipo_plantilla')->default(60)->comment('Identifica si pertenece al grupo de 7, 21 o 60');
            
            // Control de estado
            $table->boolean('activo')->default(1)->comment('1: Activo, 0: Inactivo');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_estandares');
    }
};