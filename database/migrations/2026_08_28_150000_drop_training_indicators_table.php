<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * training_indicators nunca fue leída ni escrita por ninguna clase de la app
 * (TrainingIndicatorService, a pesar del nombre parecido, calcula los KPIs en
 * vivo desde otras tablas y nunca toca esta) — confirmado por búsqueda
 * exhaustiva antes de este cambio. Ver docs/ia o el plan de restructuración
 * del módulo de capacitaciones para el detalle de la verificación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('training_indicators');
    }

    public function down(): void
    {
        // Tabla histórica sin datos vivos; no se recrea al revertir.
    }
};
