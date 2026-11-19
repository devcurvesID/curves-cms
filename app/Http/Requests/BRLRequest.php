<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BRLRequest extends FormRequest
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
        if ($this->has('add-lead')) {
            return [
                'fullname' => 'required|string|max:100',
                'phone' => 'required|numeric|digits_between:7,16',
                'source_id' => 'required',
            ];
        } elseif ($this->has('add-br')) {
            return [
                'fullname' => 'required|string|max:100',
                'phone' => 'required|numeric|digits_between:7,16',
                'member_id' => 'required',
            ];
        }
    }
}
