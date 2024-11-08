<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userlist extends Model
{
    use HasFactory;

    protected $fillable = ['client_network_id','client_group_id', 'full_name', 'username', 'status', 'created_by', 'last_modified_date', 'last_modified_by', 'is_deleted', 'source_project_id', 'remember_token'];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $table = 'users';
    public $timestamps = false;


}
