<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('training_program_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('planned_sessions')->default(1)->after('planned_month');
            $table->decimal('target_coverage_percentage', 5, 2)->nullable()->after('planned_sessions');
        });

        Schema::create('training_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->restrictOnDelete();
            $table->string('name');
            $table->string('document_type', 20)->nullable();
            $table->string('document_number', 40)->nullable();
            $table->string('organization')->nullable();
            $table->string('profession')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiration_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'is_active']);
        });

        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->restrictOnDelete();
            $table->foreignId('training_program_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('training_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attendance_event_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('training_type', 40);
            $table->string('induction_scope', 30)->nullable();
            $table->string('reinduction_cause', 40)->nullable();
            $table->dateTime('scheduled_start_at');
            $table->dateTime('scheduled_end_at');
            $table->dateTime('actual_start_at')->nullable();
            $table->dateTime('actual_end_at')->nullable();
            $table->unsignedInteger('planned_duration_minutes');
            $table->unsignedInteger('actual_duration_minutes')->nullable();
            $table->string('modality', 30);
            $table->string('location')->nullable();
            $table->text('virtual_link')->nullable();
            $table->string('instructor_type', 30);
            $table->foreignId('instructor_employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->foreignId('external_instructor_id')->nullable()->constrained('training_instructors')->nullOnDelete();
            $table->string('status', 30)->default('draft');
            $table->string('audience_type', 40);
            $table->text('audience_description')->nullable();
            $table->json('specific_employee_ids')->nullable();
            $table->json('called_snapshot')->nullable();
            $table->boolean('requires_attendance')->default(true);
            $table->boolean('requires_signature')->default(true);
            $table->boolean('requires_material')->default(true);
            $table->boolean('requires_execution_report')->default(true);
            $table->text('extraordinary_reason')->nullable();
            $table->string('extraordinary_origin', 50)->nullable();
            $table->longText('content_delivered')->nullable();
            $table->longText('execution_notes')->nullable();
            $table->decimal('coverage_percentage', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('training_session_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->string('change_type', 30);
            $table->dateTime('old_start_at')->nullable();
            $table->dateTime('new_start_at')->nullable();
            $table->dateTime('old_end_at')->nullable();
            $table->dateTime('new_end_at')->nullable();
            $table->text('reason');
            $table->unsignedInteger('changed_by');
            $table->timestamp('changed_at');
            $table->foreign('changed_by')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('training_session_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->restrictOnDelete();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->string('evidence_type', 40);
            $table->string('title');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->char('file_hash', 64);
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('uploaded_by');
            $table->timestamps();
            $table->foreign('uploaded_by')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('training_session_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('minutes_before');
            $table->dateTime('scheduled_for');
            $table->dateTime('sent_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('error')->nullable();
            $table->timestamps();
            $table->unique(['training_session_id', 'minutes_before'], 'training_reminder_session_minutes_uq');
        });

        Schema::table('attendance_participants', function (Blueprint $table) {
            $table->string('invitation_status', 20)->default('pending')->after('expected');
            $table->dateTime('invited_at')->nullable()->after('invitation_status');
            $table->boolean('added_after_freeze')->default(false)->after('invited_at');
            $table->dateTime('excluded_at')->nullable()->after('added_after_freeze');
            $table->text('exclusion_reason')->nullable()->after('excluded_at');
            $table->unsignedInteger('excluded_by')->nullable()->after('exclusion_reason');
            $table->foreign('excluded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_participants', function (Blueprint $table) {
            $table->dropForeign(['excluded_by']);
            $table->dropColumn(['invitation_status', 'invited_at', 'added_after_freeze', 'excluded_at', 'exclusion_reason', 'excluded_by']);
        });
        Schema::dropIfExists('training_session_reminders');
        Schema::dropIfExists('training_session_evidences');
        Schema::dropIfExists('training_session_changes');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('training_instructors');
        Schema::table('training_program_items', fn (Blueprint $table) => $table->dropColumn(['planned_sessions', 'target_coverage_percentage']));
    }
};
