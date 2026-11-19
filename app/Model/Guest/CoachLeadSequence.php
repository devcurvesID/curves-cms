<?php

namespace App\Model\Guest;

use Illuminate\Database\Eloquent\Model;

class CoachLeadSequence extends Model
{
    protected $table = 'coach_lead_sequences';

    protected $primaryKey = 'coach_lead_id';

    protected $fillable = [
        'coach_lead_id',
        'club_id',
        'sequence',
    ];

    public function coachleadsequence_user(){
        return $this->belongsTo('App\User', 'coach_lead_id');
    }

    public function coachleadsequence_club(){
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
