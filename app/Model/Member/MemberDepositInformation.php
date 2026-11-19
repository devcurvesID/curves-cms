<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberDepositInformation extends Model
{
    protected $table = 'member_deposit_informations';

    protected $primaryKey = 'member_deposit_id';

    protected $fillable = [
        'member_deposit_id',
        'information',
    ];

    public function information_memberdeposit() {
        return $this->belongsTo('App\Model\Member\MemberDeposit', 'member_deposit_id');
    }
}
