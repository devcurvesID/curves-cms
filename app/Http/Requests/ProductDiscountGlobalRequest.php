<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDiscountGlobalRequest extends FormRequest
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
        $input = $this->request->all();
        if ($input['discount_base'] == 'percent'):
            $discount_percent = 'required|between:0,100.00';
            $discount_value = 'sometimes';
        elseif ($input['discount_base'] == 'value'):
            $discount_percent = 'sometimes';
            $discount_value = 'required|numeric';
        endif;

        return [
            'discount_category' => 'required|in:Retail,Member',
            'discount_base' => 'required|in:percent,value',
            'discount_percent' => $discount_percent,
            'discount_value' => $discount_value,
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
        ];
    }
}
