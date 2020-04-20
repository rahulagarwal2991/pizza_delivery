<?php

namespace App\Services;

use App\Events\OrderMade;
use App\Events\OrderReady;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\ProductsRepository;

class OrderService
{
    /**
     * @var \App\Repositories\OrderRepository
     */
    private $repository;

    /**
     * @param \App\Repositories\OrderRepository $repository
     */
    public function __construct(OrderRepository $repository, ProductsRepository $productsRepository)
    {
        $this->repository = $repository;
        $this->productsRepository = $productsRepository;
    }

    /**
     * @param \App\Http\Requests\OrderRequest $request
     *
     * @return \App\Order
     */
    public function createFromRequest(OrderRequest $request): Order
    {
        $data = $request->except('products');

        /** @var \App\Order */
        $order = $this->repository->create($data);

        $this->attachProductsToOrder($order, $request->products);

        event(new OrderMade($order));

        return $order;
    }

    /**
     * @param \App\Order $order
     * @param array      $products
     */
    private function attachProductsToOrder(Order $order, array $products)
    {
        $price = 0;

        foreach ($products as $product) {
            $productModel = $this->productsRepository->getById($product['id']);
            $totalPrice = $product['amount'] * $productModel->price;

            $order->products()->attach($productModel->id, [
                'total_price' => $totalPrice,
                'unitary_price' => $productModel->price,
                'amount' => $product['amount'],
            ]);

            $price += $totalPrice;
        }
        $this->repository->updateById(['total_price' => $price], $order->id);
    }
}
