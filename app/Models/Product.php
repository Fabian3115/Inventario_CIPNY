<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'description',
        'stock',
        'categories',
        'extent',
        'warehouse',
        'date_products',
    ];

    protected $casts = [
        'date_products' => 'date', // Y-m-d
    ];

    public function movements()
    {
        return $this->hasMany(\App\Models\Movement::class);
    }
}
