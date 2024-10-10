<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_promotions');
    }

    
}
