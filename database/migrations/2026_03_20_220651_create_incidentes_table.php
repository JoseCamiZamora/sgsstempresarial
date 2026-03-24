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
        Schema::create('incidentes', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario que reporta o el empleado afectado
            // 1. Creamos la columna del tamaño exacto
            $table->unsignedInteger('usuario_id');
            // 2. Hacemos la conexión manual
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            
            // Datos del evento
            $table->date('fecha_incidente');
            $table->time('hora_incidente');
            $table->enum('tipo_evento', ['Incidente', 'Accidente de Trabajo']); // Incidente = Casi accidente
            $table->string('lugar_evento');
            
            // Detalles
            $table->text('descripcion');
            
            // Gestión por parte del Administrador SST
            $table->enum('estado', ['Pendiente', 'En Investigación', 'Cerrado'])->default('Pendiente');
            $table->text('observaciones_sst')->nullable(); // Notas del prevencionista
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
