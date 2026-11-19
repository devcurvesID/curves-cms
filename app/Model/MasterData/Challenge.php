<?php

namespace App\Model\MasterData;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $table = 'challenges';

    protected $fillable = [
        'challenge',
        'type',
        'variable',
        'sequencable',
        'sequence',
        'variable_target',
        'status',
        'picture',
    ];

    public function challenge_rewardhistory() {
        return $this->hasMany('App\Model\Log\RewardHistory', 'challenge_id');
    }
}
