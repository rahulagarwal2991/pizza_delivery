<?php

namespace App\Repositories;

use App\Models\Product;

//use Prettus\Repository\Eloquent\BaseRepository;

class ProductsRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    /**
     * @param int $limit
     *
     * @return mixed
     */
    public function getMenu()
    {
        return  $this->getModel()->get();
        
    }
    
    public function getById($id)
    {
        return  $this->getModel()->find($id);
        
    }
}
