<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syslog extends Model
{
    use HasFactory;
    protected $table = 'syslogs';
    protected $primaryKey = 'id_syslog';
    protected $guarded = ['created_at', 'updated_at'];
}
