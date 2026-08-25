<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empleado_portal_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->unique()->constrained('empleados')->cascadeOnDelete();
            $table->char('code_hash', 64);
            $table->dateTime('code_generated_at');
            $table->unsignedInteger('generated_by');
            $table->unsignedInteger('failed_attempts')->default(0);
            $table->dateTime('locked_until')->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();
            $table->foreign('generated_by')->references('id')->on('users')->restrictOnDelete();
        });
        Schema::create('empleado_portal_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->string('event', 60);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['empleado_id', 'event'], 'employee_portal_audit_empleado_event_idx');
        });
    }

    public function down(): void
    {
        foreach (['empleado_portal_audits', 'empleado_portal_credentials'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
