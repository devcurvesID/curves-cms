<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportSMSRequest extends FormRequest
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
            'target' => 'required|in:wo1,wo2,wo3,birthday,free',
            'custom_message' => 'required',
        ];
        $input = $this->request->all();

        if (!empty($input['member'])):
            foreach ($input['member'] as $key => $value):
                if (!empty($value)):
                    $rules['total_wo.'.$key] = 'required|numeric';
                endif;
            endforeach;
        endif;

        return $rules;
    }
}
