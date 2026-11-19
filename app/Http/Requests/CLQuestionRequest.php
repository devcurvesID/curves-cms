<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CLQuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules =[];
        $input = $this->request->all();

        foreach ($input['question'] as $i=>$value):
            if(!empty($value)):
                $rules['question.'.$i] = 'required';
                $rules['id.'.$i] = 'required';
//                id=0->add
                $rules['status.'.$i] = 'required';
//                0-> nothing 1->edit 2->create
            endif;
        endforeach;

        return $rules;
    }
}
