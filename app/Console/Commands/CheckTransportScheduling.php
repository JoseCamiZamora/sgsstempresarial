<?php
namespace App\Console\Commands;use App\Models\PerfilEmpresa;use App\Services\TransportAlertService;use Illuminate\Console\Command;
class CheckTransportScheduling extends Command{protected$signature='transport:check-scheduling';protected$description='Detecta alertas próximas de programación de transporte';public function handle(TransportAlertService$service):int{$count=0;PerfilEmpresa::query()->pluck('id')->each(function($id)use($service,&$count){$count+=$service->scan($id);});$this->info("Alertas activas detectadas: {$count}");return self::SUCCESS;}}
