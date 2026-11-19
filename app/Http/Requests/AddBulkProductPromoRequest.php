<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkProductPromoRequest extends FormRequest
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
        $reules = [
            'promo_name' => 'required|unique:mst_products,item_name',
        ];
        $input = $this->request->all();

        foreach ($input['item'] as $key => $value) {
            $reules['qty.'.$key] = 'required';
        }

        return $reules;
    }
}
