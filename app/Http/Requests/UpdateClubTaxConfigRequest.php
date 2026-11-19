<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClubTaxConfigRequest extends FormRequest
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
        $rules = [];
        $input = $this->request->all();

        foreach ( $input['tax_config'] as $key => $value):
            if (!empty($value)):
                $rules['rate.'.$key] = 'required|between:0,100.00';
                $rules['from_date.'.$key] = 'required|date';
                $rules['inactive.'.$key] = 'sometimes';
            endif;
        endforeach;

        return $rules;
    }
}
