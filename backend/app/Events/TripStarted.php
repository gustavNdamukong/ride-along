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

class TripStarted
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
            //new PrivateChannel('channel-name'),
            //new Channel('passenger_'.$this->user->id) //lets use sth more generic channel eg 'drivers' that all 
            //currently available passengers will listen to 
            new Channel('passenger_'.$this->user->id)
            //broadcast on any channel (give it any name of your choice) so drivers will (subcribe to) & listen on 
            //for events & respond. Her we call the channel 'drivers'. Subscribers (in this case drivers) will be listening for
            //this event (TripStarted) on the 'drivers' channel
        ];
    }
}
