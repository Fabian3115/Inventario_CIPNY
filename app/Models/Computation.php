<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Computation extends Model
{

    protected $fillable = [
        'requisition',
        'brand',
        'serial_s/n',
    ];

    public function movements()
    {
        return $this->hasMany(ComputedMovement::class);
    }
}
