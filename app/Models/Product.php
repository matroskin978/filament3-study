<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{

    protected $fillable = ['category_id', 'brand_id', 'sku', 'title', 'slug', 'excerpt', 'description', 'quantity', 'price', 'old_price', 'is_visible', 'is_featured', 'is_hit', 'is_sale', 'photo', 'photos'];

    protected $casts = [
        'photos' => 'array',
    ];

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'filter_product', 'product_id', 'filter_id');
    }

}
