<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->restrictOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('attendable_type', 50);
            $table->unsignedBigInteger('attendable_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type', 50);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('attendance_opens_at');
            $table->dateTime('attendance_closes_at');
            $table->string('status', 20)->default('draft');
            $table->string('access_mode', 30)->default('shared_qr_personal_code');
            $table->string('verification_method', 30)->default('personal_code');
            $table->boolean('requires_signature')->default(true);
            $table->boolean('allows_external_attendees')->default(false);
            $table->boolean('public_access_enabled')->default(true);
            $table->char('access_token_hash', 64)->nullable();
            $table->dateTime('access_token_expires_at')->nullable();
            $table->dateTime('token_rotated_at')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['attendable_type', 'attendable_id'], 'attendance_attendable_unique');
            $table->index(['company_id', 'status'], 'attendance_company_status_idx');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
        Schema::create('attendance_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('participant_type', 20);
            $table->foreignId('employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('name_snapshot');
            $table->string('role_snapshot')->nullable();
            $table->string('department_snapshot')->nullable();
            $table->string('organization')->nullable();
            $table->string('email')->nullable();
            $table->boolean('expected')->default(true);
            $table->char('credential_hash', 64)->nullable();
            $table->dateTime('credential_expires_at')->nullable();
            $table->timestamps();
            $table->unique(['attendance_event_id', 'employee_id'], 'attendance_event_employee_unique');
        });
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('attendance_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_participant_id')->constrained()->restrictOnDelete();
            $table->string('status', 30)->default('pending');
            $table->dateTime('checked_in_at')->nullable();
            $table->string('verification_method', 30);
            $table->string('verification_level', 20)->default('standard');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            $table->string('consent_text_version', 20)->nullable();
            $table->string('verification_code', 64)->unique();
            $table->char('evidence_hash', 64)->nullable();
            $table->unsignedSmallInteger('integrity_version')->default(1);
            $table->dateTime('voided_at')->nullable();
            $table->unsignedInteger('voided_by')->nullable();
            $table->text('void_reason')->nullable();
            $table->text('manual_reason')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            $table->unique(['attendance_event_id', 'attendance_participant_id'], 'attendance_record_unique');
            $table->foreign('voided_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
        Schema::create('attendance_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('signature_method', 40)->default('drawn_signature');
            $table->string('file_path');
            $table->char('file_hash', 64);
            $table->dateTime('signed_at');
            $table->string('verification_method', 30);
            $table->string('consent_text_version', 20);
            $table->timestamps();
        });
        Schema::create('attendance_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->constrained()->cascadeOnDelete();
            $table->string('event', 60);
            $table->string('subject_type', 40)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
        Schema::create('attendance_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('file_path');
            $table->char('file_hash', 64);
            $table->dateTime('generated_at');
            $table->unsignedInteger('generated_by');
            $table->timestamps();
            $table->unique(['attendance_event_id', 'version']);
            $table->foreign('generated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }
    public function down(): void
    {
        foreach (['attendance_evidences','attendance_audits','attendance_signatures','attendance_records','attendance_participants','attendance_events'] as $table) Schema::dropIfExists($table);
    }
};
