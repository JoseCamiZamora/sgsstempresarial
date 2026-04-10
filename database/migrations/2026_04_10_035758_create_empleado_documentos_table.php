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
    Schema::create('empleado_documentos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
        $table->string('nombre_archivo'); // Ej: Cedula_Juan_Perez.pdf
        $table->string('tipo_documento'); // Ej: Contrato, Salud, Identidad
        $table->string('ruta_archivo');   // El path en el storage
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_documentos');
    }
};
