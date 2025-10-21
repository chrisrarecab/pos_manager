<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Setting;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'full_name',
        'username',
        'password',
        'created_by',
        'created_date',
        'last_modified_by',
        'last_modified_date'
    ];

    public $timestamps = false;
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    /**
     * Accessor
    */
    protected function isAdmin(): Attribute
    {
        return Attribute::get(
            fn () => $this->permissions->contains('code', '100')
        );
    }
}
