<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model implements Authenticatable
{
    use HasFactory;
    use Notifiable;
    use \Illuminate\Auth\Authenticatable;
    public $timestamps = false;
    protected $fillable = ['username', 'phone_number', 'address', 'email','password', 'role'];
    protected $table = 'users';

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
