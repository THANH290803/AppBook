<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $table = 'books';
    protected $fillable = ['isbn', 'name', 'amount', 'price', 'author', 'img', 'description', 'publish_year', 'created_at', 'category_id', 'publisher_id'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'book_id', 'id');
    }
}
