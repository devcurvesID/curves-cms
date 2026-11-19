<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberDebtPaymentRequest extends FormRequest
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
            'payment_number' => 'required',
            'payment_method' => 'required',
            'total_bill' => 'required|numeric',
            'total_pay' => 'required|numeric',
            'rest_of_bill' => 'required|numeric'
        ];
    }
}
