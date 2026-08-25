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
        Schema::create('planes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('anio', 4); // Ej: 2026
            $table->text('objetivo_general')->nullable();
            $table->decimal('presupuesto_asignado', 15, 2)->nullable();
            $table->enum('estado', ['Borrador', 'Aprobado', 'Cerrado'])->default('Borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_trabajo');
    }
};
