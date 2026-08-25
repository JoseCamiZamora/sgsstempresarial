<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DocumentoFirmaRequerimiento extends Model {protected $table='documento_firma_requerimientos';protected $fillable=['documento_id','version_requerida'];public function documento(){return$this->belongsTo(Documento::class);}}
