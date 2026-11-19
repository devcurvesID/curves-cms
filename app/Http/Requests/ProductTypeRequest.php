<?php
/**
 * Created by PhpStorm.
 * User: Tjatur
 * Date: 15-Jan-18
 * Time: 15:26
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ProductTypeRequest extends FormRequest
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
            'type' => 'required|string|max:255|unique:mst_product_types',
        ];
    }
}