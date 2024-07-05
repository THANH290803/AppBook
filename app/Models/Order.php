<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'code_order', 'status', 'name_customer', 'phone_customer', 'address_customer', 'note',
        'shipping_code', 'customer_id', 'payment_method_id',
        'editor_id', 'approve_id', 'created_at', 'updated_at'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
