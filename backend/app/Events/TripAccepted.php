<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Trip;
use App\Models\User;

class TripAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;
    private $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Trip $trip, User $user)
    {
        $this->trip = $trip;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            //we will not use a private channel coz it will involve a lot of authentication on the frontend to
            //connect to it. Rather, we will simply broadcast an event
            //new PrivateChannel('channel-name'),
            //the below channel associated with a unique user is apt because a trip is ultimately started by one user. 
            new Channel('passenger_'.$this->user->id)
        ];
    }
}
