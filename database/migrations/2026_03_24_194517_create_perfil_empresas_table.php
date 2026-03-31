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
        Schema::create('perfil_empresas', function (Blueprint $table) {
            $table->id();
            
            // Datos Legales Básicos
            $table->string('razon_social');
            $table->string('nit')->unique();
            $table->string('representante_legal')->nullable();
            $table->string('actividad_economica')->nullable();
            
            // 🚨 Variables Críticas para la Resolución 0312
            $table->integer('numero_trabajadores')->default(0)->comment('Total de empleados directos e indirectos');
            $table->integer('nivel_riesgo')->nullable()->comment('Valores del 1 al 5 (I, II, III, IV, V)');
            
            // Datos de Ubicación y Contacto
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo_contacto')->nullable();
            
            // Identidad Visual (El logo que debe subir Jose Camilo)
            $table->string('logo_path')->nullable();
            
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
        Schema::dropIfExists('perfil_empresas');
    }
};