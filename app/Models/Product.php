<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'original_price', // Add this line
        'stock_quantity',
        'sku',
        'is_active',
        'category_id',
        'brand_id',
        'is_featured',
        'is_active',
        'is_sale',
        'is_new',
        'is_best_seller',
        'is_top_rated',
        'is_on_sale',
        'images',   
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_sale' => 'boolean',
        'is_new' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_top_rated' => 'boolean',
        'is_on_sale' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'product_promotions');
    }

    // You might want to add this accessor
    public function getOriginalPriceAttribute($value)
    {
        return $value ?? $this->price;
    }

    public function getImagesUrlAttribute()
    {
        if (is_array($this->images)) {
            return array_map(function ($image) {
                return Storage::url($image);
            }, $this->images);
        }
        return $this->images ? Storage::url($this->images) : null;
    }
}
