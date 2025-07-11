<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'client_branch_id'];
    protected $table = 'user_branch';
    public $timestamps = false;
}
