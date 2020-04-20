<?php

namespace App\Http\Requests;

use App\Rules\ProductAmountRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(ProductAmountRule $productAmountRule)
    {
        return [
            'client_name' => 'required|string',
            'products' => 'required|array|min:1',
            //'products.*.id' => 'exists:products,id',
           // 'products.*.amount' => [$productAmountRule],
        ];
    }
}
