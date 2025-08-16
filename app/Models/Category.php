<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{

    protected $fillable = ['title', 'slug', 'parent_id', 'description', 'photo'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

}
