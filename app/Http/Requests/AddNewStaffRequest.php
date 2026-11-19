<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddNewStaffRequest extends FormRequest
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
            'name' => 'required',
            'role' => 'required',
            'phone' => 'required|numeric|digits_between:6,20',
            'email_personal' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|min:6|unique:users',
            'password' => 'required|confirmed|min:6',
            'staff_code'=>'required|string|max:3|unique:staff_codes,code',
//            'key_tag_id' => 'sometimes|required|nullable|numeric|digits:9',
        ];
        $input = $this->request->all();
        if (!empty($input['key_tag_id'])):
            $rules['key_tag_id'] = 'required|numeric|digits:9|unique:member_personals,key_tag_id';
        else:
            $rules['key_tag_id'] = 'sometimes';
        endif;

        return $rules;
    }
}
