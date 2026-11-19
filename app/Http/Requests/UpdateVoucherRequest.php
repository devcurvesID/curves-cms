<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
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

        if (!empty( $input['voucher_out'])):
            $rules = [];
        else:
            $rules = [
                'from_date' => 'required|date',
                'thru_date' => 'required|date',
                'voucher_base' => 'required|in:value,percent',
            ];

            if ($this->request->get('voucher_base') == 'value'):
                $rules['new_value'] = 'required|numeric';
                $rules['new_percent'] = 'sometimes';
                $rules['new_limit'] = 'sometimes';
            elseif ($this->request->get('voucher_base') == 'percent'):
                $rules['new_percent'] = 'required|between:0,100.00';
                $rules['new_limit'] = 'required|numeric';
                $rules['new_value'] = 'sometimes';
            endif;
        endif;


        foreach ($input['voucher'] as $key => $value):
            if (!empty($value)):
                $rules['voucher.'.$key] = 'required';
            endif;
        endforeach;

        return $rules;
    }
}
