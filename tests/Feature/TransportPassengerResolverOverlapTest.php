<?php

namespace Tests\Feature;

use App\Models\{TransportPassenger, TransportRoute, TransportRouteStop, TransportService, User};
use App\Services\TransportPassengerResolverService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransportPassengerResolverOverlapTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::role(['Super Admin', 'Administrador SGSST'])->whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario administrador para la prueba.');
        }

        return $user;
    }

    public function test_assigning_overlapping_dates_for_same_passenger_is_blocked(): void
    {
        $admin = $this->admin();
        $route = TransportRoute::create(['company_id' => $admin->company_id, 'code' => 'OV' . Str::random(6), 'name' => 'Ruta overlap', 'route_type' => 'pickup', 'origin' => 'A', 'destination' => 'B', 'status' => 'active', 'created_by' => $admin->id]);
        $stopOne = $route->stops()->create(['stop_order' => 1, 'name' => 'Parada 1']);
        $stopTwo = $route->stops()->create(['stop_order' => 2, 'name' => 'Parada 2']);
        $passenger = TransportPassenger::create(['company_id' => $admin->company_id, 'passenger_type' => 'student', 'name' => 'Estudiante Overlap', 'status' => 'active', 'created_by' => $admin->id]);

        $route->passengers()->attach($passenger->id, ['transport_route_stop_id' => $stopOne->id, 'direction' => 'both', 'valid_from' => '2026-09-01', 'valid_until' => null, 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('transport.routes.passengers.store', $route), [
            'transport_passenger_id' => $passenger->id,
            'transport_route_stop_id' => $stopTwo->id,
            'direction' => 'both',
            'valid_from' => '2026-09-05',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('transport_passenger_id');
        $this->assertSame(1, DB::table('transport_route_passengers')->where('transport_route_id', $route->id)->where('transport_passenger_id', $passenger->id)->count());
    }

    public function test_resolver_deduplicates_passenger_with_overlapping_stale_assignments(): void
    {
        $admin = $this->admin();
        $route = TransportRoute::create(['company_id' => $admin->company_id, 'code' => 'OV' . Str::random(6), 'name' => 'Ruta overlap 2', 'route_type' => 'pickup', 'origin' => 'A', 'destination' => 'B', 'status' => 'active', 'created_by' => $admin->id]);
        $stopOne = $route->stops()->create(['stop_order' => 1, 'name' => 'Parada 1']);
        $stopTwo = $route->stops()->create(['stop_order' => 2, 'name' => 'Parada 2']);
        $passenger = TransportPassenger::create(['company_id' => $admin->company_id, 'passenger_type' => 'student', 'name' => 'Estudiante Duplicado', 'status' => 'active', 'created_by' => $admin->id]);

        // Simula datos ya corruptos (dos asignaciones activas y vigentes al mismo tiempo),
        // insertados directo en la tabla pivote para no pasar por la validación que ya lo bloquea.
        DB::table('transport_route_passengers')->insert([
            ['transport_route_id' => $route->id, 'transport_passenger_id' => $passenger->id, 'transport_route_stop_id' => $stopOne->id, 'direction' => 'both', 'valid_from' => '2026-09-01', 'valid_until' => null, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['transport_route_id' => $route->id, 'transport_passenger_id' => $passenger->id, 'transport_route_stop_id' => $stopTwo->id, 'direction' => 'both', 'valid_from' => '2026-09-05', 'valid_until' => '2026-12-31', 'created_at' => now(), 'updated_at' => now(), 'status' => 'active'],
        ]);

        $service = TransportService::create(['company_id' => $admin->company_id, 'uuid' => (string) Str::uuid(), 'transport_route_id' => $route->id, 'service_date' => '2026-09-10', 'service_type' => 'dropoff', 'shift' => 'afternoon', 'scheduled_start_at' => '2026-09-10 14:00:00', 'scheduled_arrival_at' => '2026-09-10 15:00:00', 'route_name_snapshot' => $route->name, 'origin_snapshot' => 'A', 'destination_snapshot' => 'B', 'status' => 'ready', 'passenger_snapshot_status' => 'pending', 'generation_source' => 'manual', 'created_by' => $admin->id]);

        $count = app(TransportPassengerResolverService::class)->resolve($service, $admin->id);

        $this->assertSame(1, $count);
        $this->assertSame($stopTwo->id, $service->passengers()->first()->transport_route_stop_id);
    }
}
