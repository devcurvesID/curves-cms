<?php

namespace App\Model\WM;

use Illuminate\Database\Eloquent\Model;

class WMRank extends Model
{
    protected $table = 'wm_ranks';

    protected $fillable = [
    	'user_id',
    	'wm_one',
    	'wm_two',
    	'body_fat_diff',
    	'weigh_diff',
    	'size_diff',
    	'day_diff',
    	'wo_count',
    	'type'
    ];

    public function wmrank_user(){
    	return $this->belongsTo('App\User','user_id');
    }
}
