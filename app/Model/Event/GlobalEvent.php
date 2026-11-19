<?php

namespace App\Model\Event;

use Illuminate\Database\Eloquent\Model;

class GlobalEvent extends Model
{
    protected $table = 'global_events';

    protected $fillable = [
        'title',
        'description',
        'max_taken',
        'point',
        'from_date',
        'thru_date',
    ];

    public function globalevent_club() {
        return $this->belongsToMany('App\Model\Club\ClubInfo', 'club_global_event', 'global_event_id', 'club_id')->withTimestamps();
    }
}
