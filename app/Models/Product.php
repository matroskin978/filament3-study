<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = ['category_id', 'brand_id', 'sku', 'title', 'slug', 'excerpt', 'description', 'quantity', 'price', 'old_price', 'is_visible', 'is_featured', 'is_hit', 'is_sale', 'photo', 'photos'];

    protected $casts = [
        'photos' => 'array',
    ];

}
