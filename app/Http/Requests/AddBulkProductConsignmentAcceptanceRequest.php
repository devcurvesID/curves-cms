<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkProductConsignmentAcceptanceRequest extends FormRequest
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

        $qtys = $input['amount_receive'];

        $rules = [];

        foreach ($input['id'] as $key => $value):
            if (!empty($value)):
                $rules['amount_receive.'.$key] = 'required|numeric';
            endif;
        endforeach;

        return $rules;
    }
}
