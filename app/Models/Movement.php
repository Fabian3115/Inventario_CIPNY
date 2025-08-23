<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = [
        'product_id',
        'date_products',
        'type',
        'amount',
        'delivered_to',
        'area',
        'taken_by',
    ];

    protected $casts = [
        'date_products' => 'date', // Y-m-d
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
