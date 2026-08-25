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
    Schema::create('evaluacion_detalles', function (Blueprint $table) {
        $table->id();
        
        // 1. Relación con evaluaciones (Asumiendo que 'evaluaciones' sí usa BigInteger)
        $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete();
        
        // 2. Relación con los estándares declarada manualmente para evitar el error 150
        // CAMBIA 'items_estandar' por el nombre EXACTO de tu tabla en la base de datos
        $table->unsignedBigInteger('item_estandar_id'); 
        $table->foreign('item_estandar_id')->references('id')->on('items_estandar');
        
        $table->enum('cumplimiento', ['Cumple Totalmente', 'No Cumple', 'No Aplica'])->default('No Cumple');
        $table->text('observaciones')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion_detalles');
    }
};
