<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class superadmin_tool_flags extends Model
{
    use HasFactory;
    
    protected $fillable = ['clientgroupid','networkid','branchid', 'terminalno', 'meta_data'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    
}
