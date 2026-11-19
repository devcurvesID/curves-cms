<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkContactedLeadRequest extends FormRequest
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
        /*$confirm = $this->request->get('confirm');
        $no_answer = $this->request->get('no_answer');*/
        $input = $this->request->all();

        $contacted = $input['contacted'];

        if (!empty($input['contacted'])):
            foreach ($input['no_answer'] as $key => $val):
                $rules['confirm.'.$key] = 'sometimes';
                $rules['no_answer.'.$key] = 'required|numeric';
                $rules['reschedule.'.$key] = 'sometimes';
                $rules['reschedule_date.'.$key] = 'required_if:reschedule.'.$key.',on';
                $rules['reschedule_time.'.$key] = 'required_if:reschedule.'.$key.',on';
                $rules['contacted.'.$key] = 'sometimes';
            endforeach;

            return $rules;
        else:
            return [];
        endif;
    }
}
