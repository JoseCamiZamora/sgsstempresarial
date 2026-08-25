<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empleado_portal_reference_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->string('source', 20);
            $table->string('file_path');
            $table->char('file_hash', 64);
            $table->dateTime('captured_at');
            $table->dateTime('superseded_at')->nullable();
            $table->timestamps();
            $table->index(['empleado_id', 'superseded_at'], 'employee_portal_reference_signature_active_idx');
        });

        Schema::create('empleado_portal_signature_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('empleado_id')->constrained('empleados')->restrictOnDelete();
            $table->string('signable_type', 20);
            $table->unsignedBigInteger('signable_id');
            $table->string('reference_signature_source', 20)->nullable();
            $table->unsignedBigInteger('reference_signature_id')->nullable();
            $table->string('file_path')->nullable();
            $table->char('file_hash', 64)->nullable();
            $table->dateTime('signed_at');
            $table->string('document_version_snapshot', 20)->nullable();
            $table->char('evidence_hash', 64);
            $table->string('verification_code', 64)->unique();
            $table->string('signed_from_ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->unique(['signable_type', 'signable_id', 'empleado_id'], 'employee_portal_signature_unique');
            $table->foreign('reference_signature_id', 'employee_portal_signature_ref_fk')
                ->references('id')->on('empleado_portal_reference_signatures')->nullOnDelete();
        });
    }

    public function down(): void
    {
        foreach (['empleado_portal_signature_events', 'empleado_portal_reference_signatures'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
