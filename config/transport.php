<?php

return [
    'route_types' => ['pickup'=>'Recogida','dropoff'=>'Regreso','internal'=>'Interna','special'=>'Especial','other'=>'Otra'],
    'vehicle_types' => ['bus'=>'Bus','minibus'=>'Microbús','van'=>'Van','car'=>'Automóvil','other'=>'Otro'],
    'statuses' => ['active'=>'Activo','inactive'=>'Inactivo'],
    'person_types' => ['employee'=>'Empleado','external'=>'Externo'],
    'passenger_types' => ['student'=>'Estudiante','employee'=>'Empleado','beneficiary'=>'Beneficiario','other'=>'Otro'],
    'directions' => ['outbound'=>'Ida','return'=>'Regreso','both'=>'Ambas'],
    'owner_types' => ['company'=>'Propio','provider'=>'Proveedor','third_party'=>'Tercero'],
    'service_types' => ['pickup'=>'Recogida','dropoff'=>'Retorno','special'=>'Especial','internal'=>'Interno','other'=>'Otro'],
    'shifts' => ['morning'=>'Mañana','midday'=>'Mediodía','afternoon'=>'Tarde','evening'=>'Noche','custom'=>'Personalizada'],
    'service_statuses' => ['draft'=>'Borrador','scheduled'=>'Programado','ready'=>'Listo','preoperational'=>'Preoperacional completado','in_progress'=>'En curso','arrived'=>'Llegó','closed'=>'Cerrado','interrupted'=>'Interrumpido','cancelled'=>'Cancelado'],
    'passenger_statuses' => ['expected'=>'Esperado','boarded'=>'Abordó','absent'=>'Ausente','excluded'=>'Excluido','added'=>'Adicional','completed'=>'Completado'],
    'issue_types' => ['delay'=>'Demora','vehicle_failure'=>'Falla del vehículo','vehicle_change'=>'Cambio de vehículo','driver_change'=>'Cambio de conductor','monitor_change'=>'Cambio de monitor','route_change'=>'Cambio de ruta','passenger_issue'=>'Novedad con pasajero','traffic'=>'Tráfico','weather'=>'Clima','documentation'=>'Documentación','service_interruption'=>'Interrupción del servicio','accident_or_collision'=>'Accidente o colisión','other'=>'Otra'],
    'issue_severities' => ['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica'],
    'generation_sources' => ['manual'=>'Manual','recurring_schedule'=>'Programación recurrente','copied'=>'Copiado','exception'=>'Excepción'],
    'exception_types' => ['holiday'=>'Festivo','institutional'=>'Jornada institucional','maintenance'=>'Mantenimiento','vacation'=>'Vacaciones','suspension'=>'Suspensión','other'=>'Otra'],
    'weekdays' => [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'],
];
