<?php
namespace App\Console\Commands;use App\Models\PerfilEmpresa;use App\Services\{TransportAlertService,TransportDocumentControlService};use Illuminate\Console\Command;
class CheckTransportControl extends Command {protected$signature='transport:check-control';protected$description='Evalúa alertas operacionales y documentales de transporte';public function handle(TransportAlertService$a,TransportDocumentControlService$d):int{PerfilEmpresa::query()->pluck('id')->each(function($c)use($a,$d){$a->scan($c);$d->scan($c);});return self::SUCCESS;}}
