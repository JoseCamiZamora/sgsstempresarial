<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epp extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'categoria', 'vida_util_meses'];
}
