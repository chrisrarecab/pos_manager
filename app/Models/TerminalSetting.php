<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalSetting extends Model
{
    use HasFactory;
    protected $table = 'terminal_settings';
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'client_terminal_id',
        'cimrs_client_terminal_id',
        'setting_id',
        'value',
        'last_modified_by',
        'last_modified_date'
    ];
}
