<?php

namespace App\Model\Amortisasi;

use Illuminate\Database\Eloquent\Model;

class Amortisasi extends Model
{
    protected $table = 'amortisasi_histories';

    protected $fillable = [
        'period',
        'debit_balance',
        'credit_balance',
        'club_id',
        'status',
    ];

    public function amortisasi_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
