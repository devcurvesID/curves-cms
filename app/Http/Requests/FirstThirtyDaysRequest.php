<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirstThirtyDaysRequest extends FormRequest
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
            'thirty_staff' => 'required',
        ];
        $input = $this->request->all();
        $thirty_results = $input['thirty_result'];

        if (!empty($input['thirty_box'])):
            foreach ($input['thirty_box'] as $key => $value):
                if (!empty($value)):
                    $rules['thirty_result.'.$key] = 'required|string';
                endif;
            endforeach;
        endif;

        return $rules;
    }
}
