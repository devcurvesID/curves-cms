<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubDetailRequest extends FormRequest
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

        if ($this->get('attribute') == 'info') {
            $rules = [
                'club_name' => 'required|string|max:255|unique:club_infos,club_name,'.$this->get('club_attribute'),
                'address' => 'required|max:255',
                'postal' => 'required|numeric|digits_between:5,7',
                'phones' => 'required|string|max:15',
                'email' => 'required|string|max:50',
                'facebook' => 'required|string|max:50',
                'club_code' => 'sometimes',
                'jubilant' => 'required|exists:mst_jubilants,id',
            ];
        } elseif ($this->get('attribute') == 'schedule') {
            foreach ($input['schedule_id'] as $key => $value):
                $rules['from_day.'.$key] = 'required';
                $rules['thru_day.'.$key] = 'required';
                $rules['from_hour.'.$key] = 'required';
                $rules['thru_hour.'.$key] = 'required';
            endforeach;
        } elseif ($this->get('attribute') == 'csf') {
            foreach ($input['csf_id'] as $key => $value):
                $rules['membership_type.'.$key] = 'required';
                $rules['sf_amount.'.$key] = 'required|numeric';
                $rules['mf_amount.'.$key] = 'required|numeric';
                $rules['pre_pay_discount.'.$key] = 'required|numeric|between:0,100.00';
                $rules['pre_pay_discount_value.'.$key] = 'required|numeric';
                $rules['agreement_renewal.'.$key] = 'required';
            endforeach;
        } else {
            $rules = [
                'club_name' => 'required|string|max:255',
                'address' => 'required|max:255',
                'phones' => 'required|string|max:15',
                'email' => 'required|string|max:50',
                'facebook' => 'required|string|max:50',
                'club_code' => 'sometimes',
            ];
            foreach ($input['schedule_id'] as $key => $value):
                $rules['from_day.'.$key] = 'required';
                $rules['thru_day.'.$key] = 'required';
                $rules['from_hour.'.$key] = 'required';
                $rules['thru_hour.'.$key] = 'required';
            endforeach;
            foreach ($input['csf_id'] as $key => $value):
                $rules['membership_type.'.$key] = 'required';
                $rules['sf_amount.'.$key] = 'required|numeric';
                $rules['mf_amount.'.$key] = 'required|numeric';
                $rules['pre_pay_discount.'.$key] = 'required|numeric|between:0,100.00';
                $rules['pre_pay_discount_value.'.$key] = 'required|numeric';
                $rules['agreement_renewal.'.$key] = 'required';
            endforeach;
        }

        return $rules;
    }
}
