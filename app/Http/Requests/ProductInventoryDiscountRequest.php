<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductInventoryDiscountRequest extends FormRequest
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
            'discount_category' => 'required|in:Retail,Member',
            'discount_percent' => 'required|between:0,100.00',
            'discount_value' => 'required|numeric',
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
        ];
    }
}
