<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Código automático (formato: SST-F-01)
            $table->string('codigo', 20)->unique()->after('id');
            
            // Vigencia del documento
            $table->date('fecha_vigencia_inicio')->nullable()->after('descripcion');
            $table->date('fecha_vigencia_fin')->nullable()->after('fecha_vigencia_inicio');
            
            // Versión del documento
            $table->string('version', 20)->default('1.0')->after('archivo_ruta');
            
            // Tipo de acción (nuevo cargue o modificación)
            $table->enum('tipo_accion', ['Nuevo', 'Modificacion'])->default('Nuevo')->after('version');
            
            // Nombre del archivo original
            $table->string('nombre_archivo')->nullable()->after('extension_archivo');
        });
    }

    public function down()
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn([
                'codigo',
                'fecha_vigencia_inicio', 
                'fecha_vigencia_fin',
                'version',
                'tipo_accion',
                'nombre_archivo'
            ]);
        });
    }
};