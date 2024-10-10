<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cms extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms';

    protected $fillable = [
        'section',
        'title',
        'content',
        'image_url',
        'order',
        'is_active',
    ];
}
