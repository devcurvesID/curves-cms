<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffDetailRequest extends FormRequest
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

        if ($this->get('attribute') == 'info') {
            $rules = [
                'name' => 'required|string|max:255',
                'role' => 'sometimes',
                'birth' => 'required|date',
                'email' => 'required|string|email|max:255|unique:users,email,'.$this->get('staff_attribute'),
                'email_personal' => 'sometimes',
                'joined' => 'required|date',
                'nik' => 'sometimes',
                'npwp' => 'sometimes',
                'address' => 'required|string|max:255',
                'postal' => 'sometimes',
                'phone' => 'sometimes',
                'cellphone' => 'required',
                'staff_code'=>'required|string|max:3|unique:staff_codes,code,'.$this->get('staff_attribute').',user_id',
            ];
        } elseif ($this->get('attribute') == 'working-hour') {
            foreach ($input['working_hour_id'] as $key => $value):
                $rules['from_day.'.$key] = 'required';
                $rules['thru_day.'.$key] = 'required';
                $rules['from_hour.'.$key] = 'required';
                $rules['thru_hour.'.$key] = 'required';
            endforeach;
        } elseif ($this->get('attribute') == 'sfr') {
            $rules = [];
        } else {
            $rules = [
                'name' => 'required|string|max:255',
                'role' => 'sometimes',
                'birth' => 'required|date',
                'email' => 'required|string|email|max:255|unique:users,email,'.$this->get('staff_attribute'),
                'email_personal' => 'sometimes',
                'joined' => 'required|date',
                'nik' => 'sometimes',
                'npwp' => 'sometimes',
                'address' => 'required|string|max:255',
                'postal' => 'sometimes',
                'phone' => 'sometimes',
                'cellphone' => 'required',
                'staff_code'=>'required|string|max:3|unique:staff_codes,code,'.$this->get('staff_attribute').',user_id',
            ];

            foreach ($input['working_hour_id'] as $key => $value):
                $rules['from_day.'.$key] = 'required';
                $rules['thru_day.'.$key] = 'required';
                $rules['from_hour.'.$key] = 'required';
                $rules['thru_hour.'.$key] = 'required';
            endforeach;
        }

        return $rules;
    }
}
