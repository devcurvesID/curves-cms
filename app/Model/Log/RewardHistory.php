<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class RewardHistory extends Model
{
    protected $table = 'reward_histories';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'product_id',
    ];

    public function rewardhistory_user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function rewardhistory_challenge() {
        return $this->belongsTo('App\Model\MasterData\Challenge', 'challenge_id');
    }

    public function rewardhistory_product() {
        return $this->belongsTo('App\Model\Product\Product', 'product_id');
    }
}
