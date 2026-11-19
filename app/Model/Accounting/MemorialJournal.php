<?php

namespace App\Model\Accounting;

use Illuminate\Database\Eloquent\Model;

class MemorialJournal extends Model
{
    protected $table = 'memorial_journals';

    protected $fillable = [
    	'coa_id',
    	'trans_date',
    	'debit',
    	'credit',
    	'information',
    	'club_id',
        'journal_number',
    ];

    //hasOne

    //hasMany

    //belongsToOne
    public function memorialjournal_chartofaccount(){
    	return $this->belongsTo('App\Model\Accounting\ChartOfAccount','coa_id');
    }

    public function memorialjournal_club(){
    	return $this->belongsTo('App\Model\Club\ClubInfo','club_id');
    }

    //belongsToMany 
}
