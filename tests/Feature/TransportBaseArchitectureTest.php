<?php
namespace Tests\Feature;
use Illuminate\Support\Facades\{Route,Schema};use Tests\TestCase;
class TransportBaseArchitectureTest extends TestCase {
 public function test_transport_base_is_normalized_and_company_scoped():void{
  foreach(['transport_settings','transport_vehicles','transport_personnel','transport_routes','transport_passengers','transport_audits'] as $table)$this->assertTrue(Schema::hasColumn($table,'company_id'),"{$table} debe tener company_id");
  $this->assertTrue(Schema::hasTable('transport_route_stops'));$this->assertTrue(Schema::hasTable('transport_route_passengers'));
  $this->assertFalse(Schema::hasColumn('transport_routes','monday_time'));
 }
 public function test_transport_has_no_public_passenger_routes():void{
  $public=collect(Route::getRoutes())->filter(fn($route)=>str_starts_with($route->uri(),'transporte'))->filter(fn($route)=>!in_array('auth',$route->gatherMiddleware()));
  $this->assertCount(0,$public);
 }
 public function test_guest_cannot_access_transport():void{$this->get('/transporte')->assertRedirect('/login');}
}
