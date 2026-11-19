<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDetailRequest extends FormRequest
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
    public function rules()
    {
        return [
            'item_name' => 'required|string|max:255|unique:mst_products,item_name,'.$this->get('attribute'),
            'default_price' => 'required|numeric',
            'product_type' => 'required',
            'is_taxable' => 'required',
            'accessibility' => 'required',
            'status' => 'required',
            'product_image' => 'sometimes|image|mimes:jpeg,jpg,bmp,png',
            'notes' => 'sometimes',
            'consignment_price' => 'required|numeric',
        ];
    }
}
