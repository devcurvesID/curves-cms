<?php

namespace App\Model\Staff;

use Illuminate\Database\Eloquent\Model;

class CoachSequence extends Model
{
    protected $table = 'coach_sequences';

    protected $primaryKey = 'coach_id';

    protected $fillable = [
        'coach_id',
        'club_id',
        'sequence',
    ];

    public function coachsequence_user() {
        return $this->belongsTo('App\User', 'coach_id');
    }

    public function coachsequence_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }
}
