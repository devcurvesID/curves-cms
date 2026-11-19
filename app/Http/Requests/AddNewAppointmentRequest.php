<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddNewAppointmentRequest extends FormRequest
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
        /*if (!empty($this->get('btn-lead')) || !empty($this->get('btn-member'))) {
            $user_id = 'required';
            $appointment_id = 'sometimes';
        } elseif (!empty($this->get('btn-guest-rescheduled')) || !empty($this->get('btn-member-rescheduled'))) {
            $user_id = 'sometimes';
            $appointment_id = 'required';
        }*/

        return [
            'user_id' => 'sometimes',
            'appointment_id' => 'sometimes',
            'appointment_type' => 'sometimes',
            'appointment_date' => 'required|date',
            'appointment_hour' => 'required',
            'appointment_note' => 'sometimes',
        ];
    }
}
