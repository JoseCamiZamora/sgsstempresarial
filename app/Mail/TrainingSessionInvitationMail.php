<?php
namespace App\Mail; use App\Models\TrainingSession; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels;
class TrainingSessionInvitationMail extends Mailable { use Queueable,SerializesModels; public function __construct(public TrainingSession$session,public bool$reminder=false){} public function build(){return$this->subject(($this->reminder?'Recordatorio: ':'Convocatoria: ').$this->session->title)->view('emails.training_session_invitation');} }
