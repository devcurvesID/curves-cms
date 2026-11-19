<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WMScheduleRequest extends FormRequest
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

        if ($this->method() == 'POST'):
            foreach ($input['wm_date'] as $key => $value):
                if (!empty($value)):
                    $rules['wm_date.'.$key] = 'required|date';
                endif;
            endforeach;
        elseif ($this->method() == 'PATCH'):
            if (!empty($this->request->get('sch'))):
                foreach ($input['sch'] as $key => $value):
                    if (!empty($value)):
                        $rules['wm_date.'.$key] = 'required|date';
                    endif;
                endforeach;
            endif;
        endif;

        return $rules;
    }
}
