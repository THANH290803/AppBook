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
}
