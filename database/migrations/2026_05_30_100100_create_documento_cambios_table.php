<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documento_cambios', function (Blueprint $table) {
            $table->id();
            
            // Relación con documento
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            
            // Datos del control de cambio
            $table->string('version', 20);
            $table->date('fecha_vigencia_inicio');
            $table->date('fecha_vigencia_fin')->nullable();
            $table->enum('tipo_cambio', ['Nuevo', 'Modificacion']);
            $table->text('observaciones')->nullable();
            
            // Quién registró el cambio
            $table->unsignedInteger('registrado_por');
            $table->foreign('registrado_por')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_cambios');
    }
};