<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkVoucherRequest extends FormRequest
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
            'type_id' => 'required',
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
            'voucher_base' => 'required|in:value,percent',
        ];
        $input = $this->request->all();

        if ($input['voucher_base'] == 'value'):
            $rules['value'] = 'required|numeric';
        elseif ($input['voucher_base'] == 'percent'):
            $rules['percent'] = 'required|between:0,100.00';
            $rules['limit'] = 'required|numeric';
        endif;

        foreach ($input['voucher_code'] as $key => $value):
            if (!empty($value)):
                $rules['voucher_code.'.$key] = 'required|unique:vouchers,identity_number';
            endif;
        endforeach;

        return $rules;
    }
}
