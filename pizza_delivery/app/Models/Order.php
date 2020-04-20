<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_MADE = 0;
    public const STATUS_READY = 1;
    public const STATUS_DELIVERED = 2;

    protected $fillable = ['client_name', 'total_price', 'status', 'observation', 'payment_method'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('amount', 'total_price', 'unitary_price')
        ;
    }
}
