<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'product_price',
        'product_description',
        'product_image'
    ];

    protected $casts = [
        'product_image' => 'array'
    ];
}
