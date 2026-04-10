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
    Schema::create('empleados', function (Blueprint $table) {
        $table->id();
        
        // En Schema::create, el orden depende de la posición de la línea
        $table->unsignedBigInteger('user_id')->nullable(); 

        // IDENTIFICACIÓN BÁSICA
        $table->string('nombre_completo');
        $table->string('cedula')->unique();
        $table->string('email_personal')->nullable();
        $table->string('telefono')->nullable();
        
        // INFORMACIÓN LABORAL
        $table->string('cargo');
        $table->string('area_departamento')->nullable();
        $table->string('tipo_contrato')->nullable();
        $table->date('fecha_ingreso')->nullable();
        $table->date('fecha_retiro')->nullable();
        $table->decimal('salario', 12, 2)->nullable();

        // SEGURIDAD SOCIAL
        $table->string('eps')->nullable();
        $table->string('afp')->nullable();
        $table->string('arl')->nullable();
        $table->string('caja_compensacion')->nullable();

        // PERFIL SOCIODEMOGRÁFICO
        $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable();
        $table->string('rh', 5)->nullable();
        $table->date('fecha_nacimiento')->nullable();
        $table->string('contacto_emergencia_nombre')->nullable();
        $table->string('contacto_emergencia_telefono')->nullable();

        // DOTACIÓN
        $table->string('talla_camisa', 10)->nullable();
        $table->string('talla_pantalon', 10)->nullable();
        $table->string('talla_calzado', 10)->nullable();

        $table->timestamps();

        // Relación con la tabla users
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
