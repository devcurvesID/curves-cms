<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkLockerInfoRequest extends FormRequest
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
            'number_start' => 'required|min:1|integer',
            'locker_name_start' => 'required|integer|min:0',
            'number_end' => 'required|min:1|integer',
        ];
        $input = $this->request->all();
        $rules['locker_name_end'] = 'required|integer|min:'.$input['locker_name_start'];

        return $rules;
    }
}
