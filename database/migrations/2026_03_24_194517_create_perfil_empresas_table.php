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
        Schema::create('perfil_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa');
            $table->string('nit')->unique();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo_contacto')->nullable();
            $table->string('representante_legal')->nullable();
            $table->string('licencia_sst')->nullable(); // Clave para software de SST
            $table->string('logo_path')->nullable();    // Ruta de la imagen del logo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_empresas');
    }
};
