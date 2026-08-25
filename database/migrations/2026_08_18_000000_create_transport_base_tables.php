<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transport_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('service_name')->default('Gestión de Transporte');
            $table->string('site_name')->nullable();
            $table->time('workday_starts_at')->nullable();
            $table->time('workday_ends_at')->nullable();
            $table->unsignedSmallInteger('arrival_tolerance_minutes')->default(10);
            $table->json('active_weekdays')->nullable();
            $table->foreignId('responsible_employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('plate', 20);
            $table->string('internal_code', 50)->nullable();
            $table->string('vehicle_type', 40);
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('capacity');
            $table->string('owner_type', 20)->default('company');
            $table->string('owner_name')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'plate']);
            $table->unique(['company_id', 'internal_code']);
        });

        Schema::create('transport_personnel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('person_type', 20);
            $table->foreignId('employee_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('document_type', 20)->nullable();
            $table->string('document_number', 40)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('provider')->nullable();
            $table->boolean('is_driver')->default(false);
            $table->boolean('is_monitor')->default(false);
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'employee_id']);
            $table->unique(['company_id', 'document_number']);
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('route_type', 20);
            $table->string('origin');
            $table->string('destination');
            $table->time('estimated_start_time')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->foreignId('vehicle_id')->nullable()->constrained('transport_vehicles')->nullOnDelete();
            $table->foreignId('default_driver_id')->nullable()->constrained('transport_personnel')->nullOnDelete();
            $table->foreignId('default_monitor_id')->nullable()->constrained('transport_personnel')->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'code']);
        });

        Schema::create('transport_route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('stop_order');
            $table->string('name');
            $table->string('address_reference')->nullable();
            $table->time('planned_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['transport_route_id', 'stop_order']);
        });

        Schema::create('transport_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('passenger_type', 30)->default('student');
            $table->string('name');
            $table->string('identification', 40)->nullable();
            $table->string('grade_group', 80)->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_phone', 40)->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'identification']);
        });

        Schema::create('transport_route_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_passenger_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_route_stop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction', 20);
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['transport_route_id', 'transport_passenger_id', 'direction', 'valid_from'], 'transport_route_passenger_unique');
        });

        Schema::create('transport_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('perfil_empresas')->cascadeOnDelete();
            $table->string('event', 80);
            $table->string('subject_type', 50);
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['company_id', 'subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        foreach (['transport_audits','transport_route_passengers','transport_passengers','transport_route_stops','transport_routes','transport_personnel','transport_vehicles','transport_settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
