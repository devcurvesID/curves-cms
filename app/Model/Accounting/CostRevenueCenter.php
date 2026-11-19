<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class CostRevenueCenter extends Model
{
    protected $table = 'cost_revenue_centers';

    protected $primaryKey = 'club_id';

    protected $fillable = [
        'club_id',
        'cr_code',
        'name',
    ];

    //belongsTo
    public function costrevenuecenter_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
