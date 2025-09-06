<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{

    protected $fillable = ['title', 'slug', 'parent_id', 'description', 'photo'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            if ($model->photo && Storage::disk('public_uploads')->exists($model->photo)) {
                Storage::disk('public_uploads')->delete($model->photo);
            }
        });
    }

    public function filterGroups(): BelongsToMany
    {
        return $this->belongsToMany(Filtergroup::class);
//        return $this->belongsToMany(Filtergroup::class, 'category_filtergroup', 'category_id', 'filtergroup_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public static function getCategoriesTree($categories, $parentId = null, $depth = 0): array
    {
        $options = [];
        foreach ($categories->where('parent_id', $parentId) as $category) {
            $prefix = str_repeat('- ', $depth);
            $options[$category->id] = $prefix . $category->title;
            $children = self::getCategoriesTree($categories, $category->id, $depth + 1);
            $options += $children;
        }
        return $options;
    }

}
