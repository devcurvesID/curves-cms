<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WMGoalAddNewDataRequest extends FormRequest
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

        $rules = [
            'set_goal_date' => 'required|date',
            'weight' => 'required|between:0,500.00',
            'body_fat' => 'required|between:0,100.00',
            'muscle_mass' => 'required|between:0,200.00',
            'bone_mass' => 'required|between:0,200.00',
            'bmi' => 'required|between:0,100.00',
            'dci' => 'required',
            'metabolic' => 'required|numeric|digits_between:0,100',
            'body_water' => 'required|between:0,100.00',
            'visceral' => 'required',
            'chest' => 'required|between:0,200.00',
            'waist' => 'required|between:0,200.00',
            'abdomen' => 'required|between:0,200.00',
            'hip' => 'required|between:0,200.00',
            'thigh' => 'required|between:0,200.00',
            'arm' => 'required|between:0,200.00',
        ];

        if ($this->method() == 'PATCH') {
            $rules['edit_xfactor'] = 'sometimes';
            foreach ($input['xfactor'] as $key => $val):
                $rules['xfactor.'.$key] = 'required_if:edit_xfactor,on';
            endforeach;
        } elseif ($this->method() == 'POST') {
            if (!empty($input['xfactor'])):
                foreach ($input['xfactor'] as $key => $val):
                    $rules['xfactor.'.$key] = 'required|string|max:255';
                endforeach;
            endif;
        }

        return $rules;
    }
}
