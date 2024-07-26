<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'order_details';
    protected $fillable = ['book_id', 'order_id', 'quantity', 'unit_price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Thiết lập quan hệ với Book
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'id');
    }
}
