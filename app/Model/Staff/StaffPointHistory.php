<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class StaffPointHistory extends Model
{
    protected $table = 'staff_points_histories';

    protected $fillable = [
        'user_id',
        'from_date',
        'thru_date',
        'total_point',
        'point_id',
        'category',
    ];

    // belongsTo
    public function staffpointhis_point() {
        return $this->belongsTo('App\Model\MasterData\Point', 'point_id');
    }
}
