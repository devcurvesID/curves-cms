<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkQTYConsignmentOrderDetail extends FormRequest
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
        $qtys = $input['qty'];

        $rules = [];

        foreach ($input['id'] as $key => $value):
            if (!empty($value)):
                $rules['qty.'.$key] = 'required|numeric';
            endif;
        endforeach;

        return $rules;
    }
}
