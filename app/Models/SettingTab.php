<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTab extends Model
{
    use HasFactory;
    protected $fillable = ['name','tip','order', 'created_by', 'created_date', 'last_modified_by', 'last_modified_date', 'is_deleted'];
    public $timestamps = false; 
}