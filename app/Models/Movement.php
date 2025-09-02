<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
     protected $fillable = [
        'requisition', // <-- añadir
        'product_id',
        'date_products',
        'type',          // entrada | salida
        'amount',
        'delivered_to',
        'area',
        'taken_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
