<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'status'];


    protected $casts = [
        'status' => Status::class,
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name when creating or updating
        static::saving(function ($brand) {
            $brand->slug = Str::slug($brand->name);
        });
    }
}
