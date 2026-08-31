<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mapa histórico usado antes de que el prefijo fuera un campo del formulario,
     * para no dejar sin prefijo los documentos ya cargados.
     */
    private array $prefijoLegadoPorCategoria = [
        'Políticas y Objetivos' => 'PO',
        'Manuales y Procedimientos' => 'PR',
        'Formatos y Registros' => 'FT',
        'Capacitaciones' => 'CA',
        'Otros' => 'OT',
    ];

    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('prefijo', 10)->nullable()->after('categoria');
        });

        foreach ($this->prefijoLegadoPorCategoria as $categoria => $prefijo) {
            DB::table('documentos')
                ->where('categoria', $categoria)
                ->whereNull('prefijo')
                ->update(['prefijo' => $prefijo]);
        }

        DB::table('documentos')->whereNull('prefijo')->update(['prefijo' => 'OT']);
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('prefijo');
        });
    }
};
