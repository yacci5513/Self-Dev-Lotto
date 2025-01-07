<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Draw extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'draw_number',
        'draw_date',
        'number1',
        'number2',
        'number3',
        'number4',
        'number5',
        'number6',
        'bonus_number',
    ];

    protected $casts = [
        'draw_date' => 'date',
    ];

    public function winningStat()
    {
        return $this->hasOne(WinningStat::class);
    }
} 