<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateMemberStatusSettingRequest extends FormRequest
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
        $rules = [];
        $input = $this->request->all();

        if (!empty($input['member'])):
            foreach ($input['member'] as $key => $val):
                $rules['vip.'.$key] = 'sometimes';
                $rules['from_date.'.$key] = 'required|date';

                if (!empty($input['vip'][$key])):
                    $rules['thru_date.'.$key] = 'required|date';
                else:
                    $rules['thru_date.'.$key] = 'sometimes';
                endif;

                $rules['fa_coach.'.$key] = 'required';
            endforeach;
        endif;

        return $rules;
    }
}
