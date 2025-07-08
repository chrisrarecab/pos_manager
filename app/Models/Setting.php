<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\SettingOption;
use App\Models\TerminalSettingIssue;

class Setting extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }    
    public function options(){
        return $this->hasMany(SettingOption::class, 'setting_id');
    }
    public function terminalSetting() { 
        return $this->hasOne(TerminalSetting::class, 'setting_id')
                    ->where('client_terminal_id', request('client_terminal_id')); 
    }

    public function issues()
    {
        return $this->hasManyThrough(
            Issue::class, 
            TerminalSettingIssue::class, 
            'setting_id',   // Foreign key in terminal_setting_issues
            'id',           // Primary key in issues
            'id',           // Primary key in settings
            'issue_id'      // Foreign key in terminal_setting_issues pointing to issues
        );
    }
}
