<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkProductInventoryCorrectionRequest extends FormRequest
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
        $cordates = $input['cordate'];
        $notes = $input['notes'];
        $staff = $input['staff'];

        $rules = [];

        if(count($input['product_inv']) != 0){

            foreach ($input['product_inv'] as $key => $value):
                if (!empty($value)):
                    $rules['qty.'.$key] = 'required|numeric';
                    $rules['cordate.'.$key] = 'required|date';
                    $rules['notes.'.$key] = 'sometimes';
                    $rules['staff'] = 'required';
                endif;
            endforeach;
        }

        return $rules;
    }
}
