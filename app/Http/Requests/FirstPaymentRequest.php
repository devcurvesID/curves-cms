<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirstPaymentRequest extends FormRequest
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
            'payment_number' => 'required|string|max:255',
            'pay_date' => 'required|date_format:Y-m-d|before:tomorrow',
            'payment_method' => 'required',
            'sf_amount' => 'required|numeric|digits_between:1,10',
            'mf_amount' => 'required|numeric|digits_between:1,10',
            'discount' => 'required|numeric|digits_between:1,10',
            'voucher_code' => 'sometimes',
            'voucher_value' => 'sometimes',
//            'tax_type' => 'sometimes',
//            'tax_percentage' => 'required|numeric',
//            'tax_amount' => 'required|numeric|digits_between:1,10',
            'total' => 'required|numeric|digits_between:1,10',
            'total_bill' => 'required|numeric|digits_between:1,10',
            'total_pay' => 'required|numeric|digits_between:1,10',
            'rest_of_bill' => 'required|numeric|digits_between:1,10',
        ];

        $input = $this->request->all();

        if (!empty($input['use_vip'])):
            $rules['vip_option'] = 'required';

            if ($input['vip_option'] == 'day'):
                $rules['vip_duration'] = 'required|numeric|max:365';
            elseif ($input['vip_option'] == 'month'):
                $rules['vip_duration'] = 'required|numeric|max:18';
            elseif ($input['vip_option'] == 'year'):
                $rules['vip_duration'] = 'required|numeric|max:2';
            endif;
//            $rules['vip_duration'] = 'required|numeric';
        endif;

        return $rules;

//        return [
//            'payment_number' => 'required|string|max:255',
//            'pay_date' => 'required|date_format:Y-m-d|before:tomorrow',
//            'payment_method' => 'required',
//            'sf_amount' => 'required|numeric|digits_between:1,10',
//            'mf_amount' => 'required|numeric|digits_between:1,10',
//            'discount' => 'required|numeric|digits_between:1,10',
//            'voucher_code' => 'sometimes',
//            'voucher_value' => 'sometimes',
////            'tax_type' => 'sometimes',
////            'tax_percentage' => 'required|numeric',
////            'tax_amount' => 'required|numeric|digits_between:1,10',
//            'total' => 'required|numeric|digits_between:1,10',
//            'total_bill' => 'required|numeric|digits_between:1,10',
//            'total_pay' => 'required|numeric|digits_between:1,10',
//            'rest_of_bill' => 'required|numeric|digits_between:1,10',
//        ];
    }
}
