<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPaymentRequest extends FormRequest
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
        $rules = [
            'payment_method' => 'required',
            'payment_number' => 'required',
            'subtotal' => 'required|numeric',
//            'tax' => 'sometimes',
//            'tax_value' => 'required|numeric',
            'voucher_code' => 'sometimes',
            'voucher_value' => 'required|numeric',
            'billing-total' => 'required|numeric',
            'pay' => 'required|numeric',
            'member' => 'sometimes',
            'payment_date' => 'required|date',
            'invoice_discount' => 'required|numeric',
        ];
        $input = $this->request->all();
        if (!empty($input['reward'])):
            $rules['challenge'] = 'required';
        endif;

        return $rules;
    }
}
