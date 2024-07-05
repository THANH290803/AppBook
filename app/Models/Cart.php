<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'carts';
    protected $fillable = ['member_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
