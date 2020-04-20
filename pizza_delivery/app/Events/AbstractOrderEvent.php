<?php

namespace App\Events;

use App\Models\Order;

abstract class AbstractOrderEvent
{
    /**
     * @var \App\Order
     */
    protected $order;

    /**
     * @param \App\Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return \App\Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    public function broadcastOn()
    {
        return 'restaurante';
    }

    public function broadcastWith()
    {
        $order = $this->order->toArray();
        $order['products'] = $this->order->products->toArray();

        return $order;
    }
}
