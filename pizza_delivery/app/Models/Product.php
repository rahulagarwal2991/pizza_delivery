<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'picture', 'price', 'quantity_in_stock', 'ordered'];
}
