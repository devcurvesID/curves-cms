<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkLeadsAppointmentRequest extends FormRequest
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

        if (!empty($input['fa_contact'])):
            foreach ($input['fa_contact'] as $key => $val):
                if ($input['no_answer'][$key] == 0) {
                    $rules['fa_date.'.$key] = 'required|date';
                    $rules['fa_hour.'.$key] = 'required';
                } else {
                    $rules['fa_date.'.$key] = 'sometimes';
                    $rules['fa_hour.'.$key] = 'sometimes';
                }
            endforeach;

            return $rules;
        else:
            return [];
        endif;
    }
}
