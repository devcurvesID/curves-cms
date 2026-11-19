<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkProductRequest extends FormRequest
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
            'product_type' => 'required',
        ];
        $input = $this->request->all();

        $default_prices = $input['default_price'];
        $is_taxables = $input['is_taxable'];
        $accessibilities = $input['accessibility'];
        $status = $input['status'];

        foreach ($input['item_name') as $key => $value):
            if (!empty($value) && !empty($default_prices[$key])):
                $rules['item_name.'.$key] = 'required';
                $rules['default_price.'.$key] = 'required|numeric';
                $rules['is_taxable.'.$key] = 'required|in:Yes,No';
                $rules['accessibility.'.$key] = 'required|in:Retail,Member,Reward';
                $rules['status.'.$key] = 'required|in:Active,Inactive';
            elseif (empty($value) && !empty($default_prices[$key])):
                $rules['item_name.'.$key] = 'required';
                $rules['is_taxable.'.$key] = 'required|in:Yes,No';
                $rules['accessibility.'.$key] = 'required|in:Retail,Member,Reward';
                $rules['status.'.$key] = 'required|in:Active,Inactive';
            elseif (!empty($value) && empty($default_prices[$key])):
                $rules['default_price.'.$key] = 'required|numeric';
                $rules['is_taxable.'.$key] = 'required|in:Yes,No';
                $rules['accessibility.'.$key] = 'required|in:Retail,Member,Reward';
                $rules['status.'.$key] = 'required|in:Active,Inactive';
            endif;
        endforeach;

        return $rules;
        /*return [
            //
        ];*/
    }
}
