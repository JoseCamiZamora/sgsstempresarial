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
    Schema::create('entrega_epps', function (Blueprint $table) {
        $table->id();
        $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
        $table->foreignId('epp_id')->constrained('epps');
        $table->date('fecha_entrega');
        $table->string('motivo'); // Inicial, Reposición, Deterioro
        $table->integer('cantidad')->default(1);
        $table->string('talla_entregada'); // La que se le dio físicamente
        $table->text('observaciones')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_epps');
    }
};
