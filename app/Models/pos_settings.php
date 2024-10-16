<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_settings extends Model
{
    use HasFactory;
    protected $fillable = ['clientterminalid','source','key', 'value', 'datemodified'];

    public $timestamps = false; 
}
