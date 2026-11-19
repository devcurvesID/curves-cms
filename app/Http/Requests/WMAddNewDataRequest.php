<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WMAddNewDataRequest extends FormRequest
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
        if ($this->method() == 'PATCH'):
            $three_times_a_week = 'sometimes';
            $total_wo_per_month = 'sometimes';
        elseif ($this->method() == 'POST'):
            $three_times_a_week = 'required';
            $total_wo_per_month = 'required_if:three_times_a_week,Yes';
        endif;

        return [
            'wm_date' => 'required|date',
            'age' => 'required|numeric|digits_between:1,3',
            'height' => 'required|numeric|between:0,300',
            'bp_high' => 'required|numeric|digits_between:2,3',
            'bp_low' => 'required|numeric|digits_between:2,3',
            'rhr' => 'required|numeric|digits_between:2,3',
            'weight' => 'required|numeric|between:0,500.00',
            'body_fat' => 'required|numeric|between:0,100.00',
            'muscle_mass' => 'required|numeric|between:0,200.00',
            'bone_mass' => 'required|numeric|between:0,200.00',
            'bmi' => 'required|numeric|between:0,100.00',
            'dci' => 'required|numeric|between:0,10000',
            'metabolic' => 'required|numeric|digits_between:0,100',
            'body_water' => 'required|numeric|between:0,100.00',
            'visceral' => 'required|numeric',
            'chest' => 'required|numeric|between:0,200.00',
            'waist' => 'required|numeric|between:0,200.00',
            'abdomen' => 'required|numeric|between:0,200.00',
            'hip' => 'required|numeric|between:0,200.00',
            'thigh' => 'required|numeric|between:0,200.00',
            'arm' => 'required|numeric|between:0,200.00',
            'three_times_a_week' => $three_times_a_week,
            'total_wo_per_month' => $total_wo_per_month,
            'set_next_wm' => 'sometimes',
            'next_wm_date' => 'required_if:set_next_wm,set_next_wm',
            'next_wm_hour' => 'required_if:set_next_wm,set_next_wm',
        ];
    }
}
