<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WinningStat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'draw_id',
        'rank1_winners',
        'rank1_prize_amount',
        'rank2_winners',
        'rank2_prize_amount',
        'rank3_winners',
        'rank3_prize_amount',
        'rank4_winners',
        'rank4_prize_amount',
        'rank5_winners',
        'rank5_prize_amount',
        'total_prize_amount',
    ];

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
} 