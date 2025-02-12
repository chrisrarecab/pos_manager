<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_settings extends Model
{
    use HasFactory;
    protected $fillable = ['uuid','source','key', 'value', 'client_visible', 'setting_type', 'datemodified'];

    public $timestamps = false; 
}
