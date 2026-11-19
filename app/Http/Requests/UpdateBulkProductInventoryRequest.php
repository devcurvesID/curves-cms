<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkProductInventoryRequest extends FormRequest
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
        //dd($input);
        $qtys = $input['qty'];
        $notes = $input['notes'];
        $product_inv = $input['product_inv'];

        $rules = [];

        foreach ($product_inv as $key => $value):
            if (!empty($value)):
                $rules['qty.'.$key] = 'required|numeric';
                $rules['notes.'.$key] = 'sometimes';
                $rules['update_date'] = 'required|date';
            endif;
        endforeach;

        return $rules;
    }
}
