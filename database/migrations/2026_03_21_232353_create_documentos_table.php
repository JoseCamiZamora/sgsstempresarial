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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            
            $table->string('titulo');
            $table->text('descripcion')->nullable(); // Opcional
            
            // Categoría para organizar los archivos
            $table->enum('categoria', [
                'Políticas y Objetivos', 
                'Manuales y Procedimientos', 
                'Formatos y Registros', 
                'Capacitaciones',
                'Otros'
            ])->default('Otros');
            
            // Aquí guardaremos la ruta donde Laravel guarde el archivo físico (ej: /storage/documentos/archivo.pdf)
            $table->string('archivo_ruta'); 
            $table->string('extension_archivo'); // Para saber si es PDF, DOCX, XLSX, etc.
            
            // Relación para saber qué administrador lo subió
            $table->unsignedInteger('subido_por');
            $table->foreign('subido_por')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
