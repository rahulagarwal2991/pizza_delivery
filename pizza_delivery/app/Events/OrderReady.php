<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderReady extends AbstractOrderEvent implements ShouldBroadcast
{
    use SerializesModels;

    public function broadcastAs()
    {
        return 'order.ready';
    }
}
