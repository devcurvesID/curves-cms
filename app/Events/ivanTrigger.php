<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ivanTrigger implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $member;
    public $club_id;
    public $photo;
    public $wo_time;

    public function __construct($member, $club_id, $photo, $wo_time)
    {
        $this->member = $member;
        $this->club_id = $club_id;
        $this->photo = $photo;
        $this->wo_time = $wo_time;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
//        return new Channel('channel-hadir-'.$this->club);
        return ['dengarkan-curhatku-'.$this->club_id];
    }
}
