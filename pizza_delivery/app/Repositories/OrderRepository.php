<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function last(?int $status = null, int $limit = 5)
    {
        $orders = $this->getModel()->orderBy('created_at', 'desc')->get();
        
//        return $orders;
//
//        $orders = null !== $status ? $orders->where('status', ''.$status)->get() : $orders->all();

        return $orders->map(function (Order $order) use ($status) {
            $data = $order->toArray();
            $data['products'] = $order->products->toArray();

            return $data;
        });
    }
    
    public function updateById($data,$id) {
        return $this->getModel()->where('id', $id)->update($data);
    }
}
