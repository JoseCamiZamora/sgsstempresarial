<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('committee_members', function (Blueprint $table) {
            $table->string('source_type', 30)->nullable()->after('member_type');
            $table->foreignId('election_id')->nullable()->after('source_type')->constrained('committee_elections')->restrictOnDelete();
            $table->foreignId('election_candidate_id')->nullable()->after('election_id')->constrained('committee_election_candidates')->restrictOnDelete();
            $table->unsignedInteger('elected_votes')->nullable()->after('election_candidate_id');
            $table->unsignedInteger('elected_rank')->nullable()->after('elected_votes');
        });

        Schema::create('committee_formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->restrictOnDelete();
            $table->foreignId('committee_period_id')->constrained()->restrictOnDelete();
            $table->foreignId('election_id')->constrained('committee_elections')->restrictOnDelete();
            $table->date('formation_date');
            $table->date('effective_from');
            $table->date('effective_to');
            $table->date('communication_date')->nullable();
            $table->string('communication_reference')->nullable();
            $table->text('communication_notes')->nullable();
            $table->unsignedInteger('workers_count_snapshot');
            $table->text('regulation_reference');
            $table->json('regulation_snapshot');
            $table->json('electoral_snapshot');
            $table->string('status', 30)->default('draft');
            $table->string('act_number')->unique();
            $table->string('act_status', 20)->default('draft');
            $table->string('act_path')->nullable();
            $table->char('act_hash', 64)->nullable();
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->nullOnDelete();
            $table->unsignedInteger('formed_by');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('formed_by')->references('id')->on('users')->restrictOnDelete();
            $table->unique('committee_period_id');
            $table->unique('election_id');
        });

        Schema::create('committee_member_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('committee_member_id')->constrained()->restrictOnDelete();
            $table->string('role', 20);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('designation_method', 50);
            $table->date('designation_date');
            $table->text('observations')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->index(['committee_period_id', 'role', 'ends_at']);
        });

        Schema::create('committee_formation_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_formation_id')->constrained('committee_formations')->cascadeOnDelete();
            $table->string('event', 60);
            $table->unsignedInteger('user_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_formation_audits');
        Schema::dropIfExists('committee_member_roles');
        Schema::dropIfExists('committee_formations');
        Schema::table('committee_members', function (Blueprint $table) {
            $table->dropForeign(['election_id']);
            $table->dropForeign(['election_candidate_id']);
            $table->dropColumn(['source_type', 'election_id', 'election_candidate_id', 'elected_votes', 'elected_rank']);
        });
    }
};
