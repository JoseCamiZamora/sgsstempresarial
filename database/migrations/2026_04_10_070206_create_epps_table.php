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
    Schema::create('epps', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // Ej: Casco de seguridad tipo II
        $table->string('categoria'); // Cabeza, Manos, Pies, etc.
        $table->integer('vida_util_meses')->default(6); // Útil para alertas de reposición
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epps');
    }
};
