<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    // Normaliza la descripción: cada palabra con inicial mayúscula y resto minúsculas.
    protected function description(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                $str = trim((string) $value);
                // Colapsa espacios múltiples
                $str = preg_replace('/\s+/u', ' ', $str);
                // Pasa todo a minúsculas y luego título por palabra (UTF-8 seguro)
                return mb_convert_case(mb_strtolower($str, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
            },
        );
    }

    public function movements()
    {
        return $this->hasMany(\App\Models\Movement::class);
    }
}
