<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestDetailRequest extends FormRequest
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
            /*'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'birthday' => 'required',
            'email' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'emergency_name' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:255',*/
            'fullname' => 'required|string|max:255',
            'birth' => 'required|date',
            'email' => 'sometimes',
            'phone' => 'required',
            'occupation' => 'required|string|max:30',
            'status' => 'required|in:Single,Married',
            'no_sale_reason' => 'required',
            'weight' => 'sometimes',
            'body_fat' => 'sometimes',
            'visceral' => 'sometimes',
        ];
    }
}
