<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entrega_epps', function (Blueprint $table) {
            $table->string('signature_status', 20)->default('pending')->after('observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('entrega_epps', function (Blueprint $table) {
            $table->dropColumn('signature_status');
        });
    }
};
