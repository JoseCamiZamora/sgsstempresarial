<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        DB::table('empleado_portal_signature_events')->whereNull('document_version_snapshot')->update(['document_version_snapshot' => '']);

        Schema::table('empleado_portal_signature_events', function (Blueprint $table) {
            $table->dropUnique('employee_portal_signature_unique');
        });

        DB::statement('ALTER TABLE empleado_portal_signature_events MODIFY document_version_snapshot VARCHAR(20) NOT NULL DEFAULT \'\'');

        Schema::table('empleado_portal_signature_events', function (Blueprint $table) {
            $table->unique(['signable_type', 'signable_id', 'empleado_id', 'document_version_snapshot'], 'employee_portal_signature_version_unique');
        });
    }

    public function down(): void
    {
        Schema::table('empleado_portal_signature_events', function (Blueprint $table) {
            $table->dropUnique('employee_portal_signature_version_unique');
        });

        DB::statement('ALTER TABLE empleado_portal_signature_events MODIFY document_version_snapshot VARCHAR(20) NULL DEFAULT NULL');

        Schema::table('empleado_portal_signature_events', function (Blueprint $table) {
            $table->unique(['signable_type', 'signable_id', 'empleado_id'], 'employee_portal_signature_unique');
        });
    }
};
