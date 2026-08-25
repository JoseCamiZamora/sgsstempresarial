<?php

namespace Tests\Feature;

use App\Models\TransportPerson;
use App\Models\TransportRoute;
use App\Models\TransportService;
use App\Models\TransportVehicle;
use App\Services\TransportPassengerResolverService;
use App\Services\TransportServiceActionService;
use App\Services\TransportServicePreparationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransportServicePreparationFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_scheduled_service_explains_and_completes_passenger_preparation(): void
    {
        $companyId = (int) DB::table('perfil_empresas')->value('id');
        $vehicle = TransportVehicle::create([
            'company_id' => $companyId,
            'plate' => 'PF'.Str::upper(Str::random(5)),
            'vehicle_type' => 'van',
            'capacity' => 8,
            'owner_type' => 'company',
            'status' => 'active',
            'created_by' => 1,
        ]);
        $driver = TransportPerson::create([
            'company_id' => $companyId,
            'person_type' => 'external',
            'name' => 'Conductor flujo preparación',
            'document_number' => 'PFD'.Str::random(8),
            'is_driver' => true,
            'is_monitor' => false,
            'status' => 'active',
            'created_by' => 1,
        ]);
        $route = TransportRoute::create([
            'company_id' => $companyId,
            'code' => 'PF'.Str::random(6),
            'name' => 'Ruta flujo preparación',
            'route_type' => 'pickup',
            'origin' => 'A',
            'destination' => 'B',
            'status' => 'active',
            'created_by' => 1,
        ]);
        $service = TransportService::create([
            'company_id' => $companyId,
            'uuid' => (string) Str::uuid(),
            'transport_route_id' => $route->id,
            'service_date' => '2027-01-20',
            'service_type' => 'pickup',
            'shift' => 'morning',
            'scheduled_start_at' => '2027-01-20 06:00:00',
            'scheduled_arrival_at' => '2027-01-20 07:00:00',
            'planned_vehicle_id' => $vehicle->id,
            'planned_driver_id' => $driver->id,
            'route_name_snapshot' => $route->name,
            'origin_snapshot' => 'A',
            'destination_snapshot' => 'B',
            'status' => 'scheduled',
            'passenger_snapshot_status' => 'pending',
            'generation_source' => 'manual',
            'created_by' => 1,
        ]);

        $actions = app(TransportServiceActionService::class)->describe($service);
        $this->assertFalse($actions['can_prepare']);
        $this->assertSame('Resolver pasajeros del servicio.', $actions['next_action']);

        app(TransportPassengerResolverService::class)->resolve($service, 1);
        $this->assertSame('resolved', $service->fresh()->passenger_snapshot_status);

        app(TransportPassengerResolverService::class)->confirm($service->fresh(), 1);
        $this->assertSame('frozen', $service->fresh()->passenger_snapshot_status);

        app(TransportServicePreparationService::class)->prepare($service->fresh(), 1);
        $this->assertSame('ready', $service->fresh()->status);
        $this->assertSame('Completar el control preoperacional.', app(TransportServiceActionService::class)->describe($service->fresh())['next_action']);
    }
}
