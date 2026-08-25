<?php
namespace App\Enums;
enum CommitteeStatus:string {
 case DRAFT='draft';case CONFIGURED='configured';case CALL_OPEN='call_open';case REGISTRATION_OPEN='registration_open';case REGISTRATION_CLOSED='registration_closed';case ELECTION_SCHEDULED='election_scheduled';case READY_FOR_ELECTION='ready_for_election';case READY_FOR_FORMATION='ready_for_formation';case FORMING='forming';case FORMATION_PENDING='formation_pending';case ACTIVE='active';case EXPIRED='expired';case CANCELLED='cancelled';
 public function label():string{return match($this){self::DRAFT=>'Borrador',self::CONFIGURED=>'Configurado',self::CALL_OPEN=>'Convocatoria abierta',self::REGISTRATION_OPEN=>'Inscripción abierta',self::REGISTRATION_CLOSED=>'Inscripción cerrada',self::ELECTION_SCHEDULED=>'Elección programada',self::READY_FOR_ELECTION=>'Listo para elección',self::READY_FOR_FORMATION=>'Listo para conformación',self::FORMING=>'En conformación',self::FORMATION_PENDING=>'Conformación pendiente',self::ACTIVE=>'Activo',self::EXPIRED=>'Vencido',self::CANCELLED=>'Cancelado'};}
}
