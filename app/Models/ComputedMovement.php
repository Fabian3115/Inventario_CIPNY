<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputedMovement extends Model
{
    protected $fillable = [
        'computation_id',
        'movement_date',
        'type',
        'amount',
        'delivered_to',
        'area',
        'taken_by',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}
