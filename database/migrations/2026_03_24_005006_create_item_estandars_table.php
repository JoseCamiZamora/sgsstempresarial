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
        Schema::create('item_estandares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Capacitación en SST
            $table->decimal('porcentaje', 5, 2); // Ej: 2.00
            $table->boolean('activo')->default(true); // Para poder "desactivar" ítems viejos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_estandars');
    }
};
