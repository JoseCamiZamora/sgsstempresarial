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
        Schema::table('perfil_empresas', function (Blueprint $table) {
            // Agregamos trabajadores y riesgo. Usamos integer para el riesgo (1, 2, 3, 4, 5) 
            // porque nos facilitará hacer la matemática de "riesgo <= 3" más adelante.
            $table->integer('numero_trabajadores')->nullable()->after('id');
            $table->integer('nivel_riesgo')->nullable()->comment('Valores del 1 al 5')->after('numero_trabajadores');
        });
    }

    public function down()
    {
        Schema::table('perfil_empresas', function (Blueprint $table) {
            $table->dropColumn(['numero_trabajadores', 'nivel_riesgo']);
        });
    }
};
