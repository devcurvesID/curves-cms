<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkLeadRequest extends FormRequest
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
            'sources' => 'required',
        ];

        $input = $this->request->all();

        $phone = $input['phone'];
        $staff = $input['staff'];

        foreach ($input['fullname'] as $key => $val):
            if (!empty($val) && !empty($phone[$key]) && !empty($staff[$key])):
                $rules['fullname.'.$key] = 'required';
                $rules['phone.'.$key] = 'required|numeric';
                $rules['staff.'.$key] = 'required';
            elseif (empty($val) && !empty($phone[$key]) && empty($staff[$key])):
                $rules['fullname.'.$key] = 'required';
                $rules['staff.'.$key] = 'required';
            elseif (!empty($val) && empty($phone[$key]) && empty($staff[$key])):
                $rules['phone.'.$key] = 'required|numeric';
                $rules['staff.'.$key] = 'required';
            elseif (empty($val) && empty($phone[$key]) && !empty($staff[$key])):
                $rules['fullname.'.$key] = 'required';
                $rules['phone.'.$key] = 'required|numeric';
            elseif (!empty($val) && !empty($phone[$key]) && empty($staff[$key])):
                $rules['staff.'.$key] = 'required';
            endif;

        endforeach;

        return $rules;

        /*return [
            //
        ];*/
    }
}
