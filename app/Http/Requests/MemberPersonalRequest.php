<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberPersonalRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->get('attribute'),
            'birth' => 'required|date',
            'address' => 'required',
            'postal' => 'sometimes',
            'phone' => 'sometimes',
            'cellphone' => 'required',
            'office' => 'sometimes',
            'nik' => 'sometimes',
            'photo' => 'sometimes|image|mimes:jpeg,jpg,bmp,png',
            'key_tag_id' => 'required|string|unique:member_personals,key_tag_id,'.$this->get('attribute').',user_id',
            'family_name' => 'required|string|max:50',
            'relation' => 'required|string|max:25',
            'emergency_contact_number' => 'required|max:20',
            'doctor_name' => 'sometimes',
            'doctor_contact_number' => 'sometimes',
            'health_comment' => 'sometimes',
            'occupation' => 'required',
            'marriage_status' => 'required|in:Single,Married',
            'tshirt_size' => 'required',
            'wa_number' => 'sometimes',
            'ignore_wa' => 'sometimes',
        ];
    }
}
