<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_empresa_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['perfil_empresa_id', 'type']);
        });

        Schema::create('committee_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 40)->default('draft');
            $table->unsignedInteger('workers_count_snapshot');
            $table->text('regulation_reference');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['committee_id', 'start_date', 'end_date'], 'committee_period_unique');
        });

        Schema::create('committee_formation_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('committee_period_id')->constrained()->cascadeOnDelete();
            $table->string('status', 40)->default('configured');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('call_start_date');
            $table->date('call_end_date');
            $table->dateTime('candidate_registration_start');
            $table->dateTime('candidate_registration_end');
            $table->dateTime('election_start_at');
            $table->dateTime('election_end_at');
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('workers_count');
            $table->unsignedTinyInteger('required_employer_principals');
            $table->unsignedTinyInteger('required_employer_substitutes');
            $table->unsignedTinyInteger('required_worker_principals');
            $table->unsignedTinyInteger('required_worker_substitutes');
            $table->string('obligation_mode', 30);
            $table->text('regulation_reference');
            $table->json('regulation_snapshot');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['committee_id', 'status']);
        });

        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('empleados')->restrictOnDelete();
            $table->string('representation_type', 20);
            $table->string('member_type', 20);
            $table->string('position')->nullable();
            $table->date('designation_date');
            $table->string('status', 30)->default('designated');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('eligibility_confirmed')->default(false);
            $table->unsignedInteger('eligibility_confirmed_by')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('eligibility_confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['committee_period_id', 'employee_id'], 'committee_member_employee_unique');
        });

        Schema::create('committee_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_process_id')->constrained('committee_formation_processes')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('empleados')->restrictOnDelete();
            $table->string('photo_path');
            $table->text('short_profile')->nullable();
            $table->text('proposal')->nullable();
            $table->dateTime('registration_date');
            $table->string('status', 30)->default('registered');
            $table->text('observations')->nullable();
            $table->boolean('eligibility_confirmed')->default(false);
            $table->unsignedInteger('eligibility_confirmed_by')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('eligibility_confirmed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['formation_process_id', 'employee_id'], 'committee_candidate_employee_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_candidates');
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('committee_formation_processes');
        Schema::dropIfExists('committee_periods');
        Schema::dropIfExists('committees');
    }
};
