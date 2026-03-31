<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            
            // Relación con la empresa
            $table->foreignId('perfil_empresa_id')->constrained('perfil_empresas')->onDelete('cascade');
            
            // Datos de la evaluación
            $table->date('fecha_evaluacion');
            $table->integer('tipo_plantilla_aplicada')->comment('7, 21 o 60 - Congela la regla que se usó');
            
            // Resultados
            $table->decimal('puntaje_final', 5, 2)->default(0);
            $table->string('nivel_madurez')->nullable()->comment('Crítico, Moderado, Aceptable');
            
            // Trazabilidad
            $table->string('evaluador')->nullable()->comment('Nombre de quien realizó la evaluación');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
};