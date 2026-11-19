<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivateMemberHoldRequest extends FormRequest
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

        if (count($input['activate']) > 0):
            foreach ($input['activate'] as $key => $value):
                if (!empty($value)):
                    $rules['activate.'.$key] = 'required';
                endif;
            endforeach;
        endif;

        return $rules;
    }
}
