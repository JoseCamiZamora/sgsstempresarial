<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('code', 60);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('indicator_type', 20);
            $table->text('formula_description');
            $table->string('frequency', 30)->default('monthly');
            $table->decimal('target', 7, 2)->nullable();
            $table->decimal('warning_threshold', 7, 2)->nullable();
            $table->decimal('critical_threshold', 7, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'code']);
        });

        Schema::create('training_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('alert_key', 191);
            $table->string('category', 30);
            $table->string('type', 60);
            $table->string('severity', 20)->default('warning');
            $table->string('title');
            $table->text('message');
            $table->nullableMorphs('subject');
            $table->foreignId('employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->dateTime('due_at')->nullable();
            $table->string('status', 20)->default('open');
            $table->dateTime('last_detected_at');
            $table->dateTime('last_notified_at')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'alert_key']);
            $table->index(['company_id', 'status', 'severity']);
            $table->index(['company_id', 'employee_id', 'status']);
        });

        Schema::create('training_gap_need', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('gap_key', 191);
            $table->foreignId('training_need_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->unique(['company_id', 'gap_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_gap_need');
        Schema::dropIfExists('training_alerts');
        Schema::dropIfExists('training_indicators');
    }
};
