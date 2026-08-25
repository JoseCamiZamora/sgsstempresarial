<?php
namespace Tests\Unit;
use App\Models\{AttendanceEvent,CommitteeMeeting};use App\Services\AttendanceAccessService;use Carbon\Carbon;use Illuminate\Database\Eloquent\Relations\Relation;use Illuminate\Validation\ValidationException;use Tests\TestCase;
class AttendanceSecurityTest extends TestCase{
 protected function tearDown():void{Carbon::setTestNow();parent::tearDown();}
 private function event(string $token='secure-token'):AttendanceEvent{$event=new AttendanceEvent(['status'=>'open','public_access_enabled'=>true,'attendance_opens_at'=>'2026-08-17 08:00:00','attendance_closes_at'=>'2026-08-17 10:00:00','access_token_expires_at'=>'2026-08-17 10:00:00','access_token_hash'=>hash('sha256',$token)]);return$event;}
 public function test_attendance_uses_stable_morph_alias():void{$this->assertSame(CommitteeMeeting::class,Relation::getMorphedModel('committee_meeting'));}
 public function test_event_is_closed_one_second_before_opening():void{Carbon::setTestNow('2026-08-17 07:59:59');$this->assertFalse($this->event()->isOpen());}
 public function test_event_is_open_exactly_at_opening():void{Carbon::setTestNow('2026-08-17 08:00:00');$this->assertTrue($this->event()->isOpen());}
 public function test_event_is_closed_exactly_at_closing():void{Carbon::setTestNow('2026-08-17 10:00:00');$this->assertFalse($this->event()->isOpen());}
 public function test_rotated_or_wrong_token_is_rejected():void{Carbon::setTestNow('2026-08-17 09:00:00');$this->expectException(ValidationException::class);app(AttendanceAccessService::class)->validateToken($this->event('new-token'),'old-token');}
 public function test_signature_disk_is_not_the_public_disk():void{$this->assertSame('local',config('attendance.disk'));$this->assertNotSame(config('filesystems.disks.public.root'),config('filesystems.disks.'.config('attendance.disk').'.root'));}
 public function test_timezone_is_bogota():void{$this->assertSame('America/Bogota',config('app.timezone'));}
}
