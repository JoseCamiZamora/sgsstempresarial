<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportIssueEvidence extends Model { protected $table='transport_issue_evidence'; protected $fillable=['transport_service_issue_id','original_name','mime_type','size','file_path','file_hash','uploaded_by']; }
