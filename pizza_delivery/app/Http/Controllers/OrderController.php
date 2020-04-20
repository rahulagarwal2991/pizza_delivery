<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var \App\Services\OrderService
     */
    private $service;

    /**
     * @var \App\Repositories\OrderRepository
     */
    private $repository;

    /**
     * @param \App\Services\OrderService        $service
     * @param \App\Repositories\OrderRepository $repository
     */
    public function __construct(OrderService $service, OrderRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param \App\Http\Requests\OrderRequest $request
     *
     * @return mixed
     */
    public function create(OrderRequest $request)
    {
        $order = $this->service->createFromRequest($request);

        return response()->json($order);
    }

    public function all(Request $request)
    {
        $orders = $this->repository->last($request->status);

        return response()->json($orders);
    }

    public function read(Order $order)
    {
        $data = $order->toArray();
        $data['products'] = $order->products->toArray();

        return response()->json($data);
    }
}
