<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->boolean('requiere_firma_empleados')->default(false)->after('tipo_accion');
        });

        Schema::create('documento_firma_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->string('version_requerida', 20);
            $table->timestamps();
            $table->index(['documento_id', 'id'], 'documento_firma_requerimientos_latest_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_firma_requerimientos');
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('requiere_firma_empleados');
        });
    }
};
