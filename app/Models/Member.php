<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model implements Authenticatable
{
    use HasFactory;
    use \Illuminate\Auth\Authenticatable;
    public $timestamps = false;
    protected $fillable = ['username', 'phone_number', 'address', 'email','password', 'role'];
    protected $table = 'members';

}
