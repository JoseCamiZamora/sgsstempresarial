<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transport_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('departure_tolerance_minutes')->default(10)->after('arrival_tolerance_minutes');
            $table->boolean('requires_arrival_signature')->default(false)->after('upcoming_service_hours');
            $table->boolean('requires_departure_odometer')->default(false)->after('requires_arrival_signature');
            $table->boolean('requires_arrival_odometer')->default(false)->after('requires_departure_odometer');
        });

        Schema::table('transport_services', function (Blueprint $table) {
            $table->foreignId('actual_vehicle_id')->nullable()->after('planned_monitor_id')->constrained('transport_vehicles')->nullOnDelete();
            $table->foreignId('actual_driver_id')->nullable()->after('actual_vehicle_id')->constrained('transport_personnel')->nullOnDelete();
            $table->foreignId('actual_monitor_id')->nullable()->after('actual_driver_id')->constrained('transport_personnel')->nullOnDelete();
            $table->dateTime('actual_start_at')->nullable()->after('scheduled_arrival_at');
            $table->dateTime('actual_arrival_at')->nullable()->after('actual_start_at');
            $table->decimal('departure_odometer', 12, 2)->nullable();
            $table->decimal('arrival_odometer', 12, 2)->nullable();
            $table->unsignedBigInteger('departure_registered_by')->nullable();
            $table->unsignedBigInteger('arrival_registered_by')->nullable();
            $table->foreignId('arrival_receiver_employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('arrival_receiver_name')->nullable();
            $table->text('arrival_observation')->nullable();
            $table->text('operational_route_notes')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->dateTime('interrupted_at')->nullable();
            $table->unsignedBigInteger('interrupted_by')->nullable();
            $table->text('interruption_reason')->nullable();
        });

        Schema::table('transport_service_passengers', function (Blueprint $table) {
            $table->boolean('added_during_operation')->default(false)->after('added_manually');
            $table->dateTime('status_recorded_at')->nullable();
            $table->unsignedBigInteger('status_recorded_by')->nullable();
        });

        Schema::create('transport_checklist_templates', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('name'); $table->boolean('blocks_on_critical_failure')->default(true); $table->string('status',20)->default('active'); $table->timestamps();
        });
        Schema::create('transport_checklist_items', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('transport_checklist_template_id'); $table->foreign('transport_checklist_template_id','tr_check_item_template_fk')->references('id')->on('transport_checklist_templates')->cascadeOnDelete();
            $table->string('label'); $table->unsignedSmallInteger('sort_order')->default(0); $table->boolean('is_critical')->default(false); $table->boolean('is_required')->default(true); $table->timestamps();
        });
        Schema::create('transport_preoperational_checks', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->unsignedBigInteger('transport_service_id')->unique(); $table->foreign('transport_service_id','tr_preop_service_fk')->references('id')->on('transport_services')->cascadeOnDelete();
            $table->unsignedBigInteger('transport_checklist_template_id')->nullable(); $table->foreign('transport_checklist_template_id','tr_preop_template_fk')->references('id')->on('transport_checklist_templates')->nullOnDelete();
            $table->string('status',20)->default('in_progress'); $table->dateTime('completed_at')->nullable(); $table->unsignedBigInteger('completed_by')->nullable();
            $table->text('override_reason')->nullable(); $table->unsignedBigInteger('override_by')->nullable(); $table->timestamps();
        });
        Schema::create('transport_preoperational_results', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('transport_preoperational_check_id'); $table->foreign('transport_preoperational_check_id','tr_preop_result_check_fk')->references('id')->on('transport_preoperational_checks')->cascadeOnDelete();
            $table->unsignedBigInteger('transport_checklist_item_id'); $table->foreign('transport_checklist_item_id','tr_preop_result_item_fk')->references('id')->on('transport_checklist_items')->restrictOnDelete(); $table->string('result',20); $table->text('observation')->nullable(); $table->timestamps();
            $table->unique(['transport_preoperational_check_id','transport_checklist_item_id'],'transport_preop_result_unique');
        });
        Schema::create('transport_service_issues', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete(); $table->unsignedBigInteger('transport_service_id'); $table->foreign('transport_service_id','tr_issue_service_fk')->references('id')->on('transport_services')->cascadeOnDelete();
            $table->string('issue_type',40); $table->string('severity',20)->default('medium'); $table->dateTime('occurred_at'); $table->text('description'); $table->text('action_taken')->nullable();
            $table->string('status',20)->default('open'); $table->unsignedBigInteger('reported_by'); $table->dateTime('resolved_at')->nullable(); $table->unsignedBigInteger('resolved_by')->nullable(); $table->timestamps();
            $table->index(['company_id','status','issue_type']);
        });
        Schema::create('transport_issue_evidence', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('transport_service_issue_id'); $table->foreign('transport_service_issue_id','tr_issue_evidence_issue_fk')->references('id')->on('transport_service_issues')->cascadeOnDelete(); $table->string('original_name'); $table->string('mime_type',100); $table->unsignedBigInteger('size'); $table->string('file_path'); $table->string('file_hash',64); $table->unsignedBigInteger('uploaded_by'); $table->timestamps();
        });
        Schema::create('transport_arrival_signatures', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete(); $table->unsignedBigInteger('transport_service_id')->unique(); $table->foreign('transport_service_id','tr_arrival_signature_service_fk')->references('id')->on('transport_services')->cascadeOnDelete();
            $table->string('file_path'); $table->string('file_hash',64); $table->string('evidence_hash',64); $table->unsignedSmallInteger('evidence_version')->default(1); $table->dateTime('signed_at'); $table->unsignedBigInteger('captured_by'); $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['transport_arrival_signatures','transport_issue_evidence','transport_service_issues','transport_preoperational_results','transport_preoperational_checks','transport_checklist_items','transport_checklist_templates'] as $table) Schema::dropIfExists($table);
        Schema::table('transport_service_passengers', fn (Blueprint $t) => $t->dropColumn(['added_during_operation','status_recorded_at','status_recorded_by']));
        Schema::table('transport_services', function (Blueprint $t) { $t->dropConstrainedForeignId('actual_vehicle_id'); $t->dropConstrainedForeignId('actual_driver_id'); $t->dropConstrainedForeignId('actual_monitor_id'); $t->dropConstrainedForeignId('arrival_receiver_employee_id'); $t->dropColumn(['actual_start_at','actual_arrival_at','departure_odometer','arrival_odometer','departure_registered_by','arrival_registered_by','arrival_receiver_name','arrival_observation','operational_route_notes','closed_at','closed_by','interrupted_at','interrupted_by','interruption_reason']); });
        Schema::table('transport_settings', fn (Blueprint $t) => $t->dropColumn(['departure_tolerance_minutes','requires_arrival_signature','requires_departure_odometer','requires_arrival_odometer']));
    }
};
