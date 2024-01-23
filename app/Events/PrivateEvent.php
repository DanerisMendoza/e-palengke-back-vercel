<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



class PrivateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function broadcastWith(){
        return['result' => "user {$this->userId} is ping"];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // new Channel('channel-PrivateEvent4'),
            new Channel('channel-PrivateEvent' . $this->userId),
        ];
    }
}
