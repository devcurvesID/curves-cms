<?php

namespace App\Model\Member;

use Illuminate\Database\Eloquent\Model;

class MemberDeposit extends Model
{
    protected $table = 'member_deposits';

    protected $fillable = [
        'payment_number',
        'user_id',
        'debet',
        'kredit',
        'saldo',
        'status',
        'payment_type',
        'payment_date',
    ];

    public function memberdeposit_information() {
        return $this->hasOne('App\Model\Member\MemberDepositInformation', 'member_deposit_id');
    }

    public function memberdeposit_user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
