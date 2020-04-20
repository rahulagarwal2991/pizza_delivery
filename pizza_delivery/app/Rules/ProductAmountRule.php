<?php

namespace App\Rules;

use App\Repositories\ProductsRepository;
use Illuminate\Contracts\Validation\Rule;

class ProductAmountRule implements Rule
{
    private $repository;

    /**
     * Create a new rule instance.
     *
     * @param \App\Repositories\ProductsRepository $repository
     */
    public function __construct(ProductsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $index = explode('.', $attribute)[1];
        $productId = request()->get('products')[$index]['id'];

        $product = $this->repository->find($productId);

        return $product->quantity_in_stock >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid quantity for product.';
    }
}
