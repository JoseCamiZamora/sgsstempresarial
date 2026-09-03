<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('g', function (Blueprint $table) {
            $table->string('status_observation', 500)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transport_service_passengers', function (Blueprint $table) {
            $table->dropColumn('status_observation');
        });
    }
};
