<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CLFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules =[];
        $input = $this->request->all();

        foreach ($input['cl_file'] as $i=>$value):
            if(!empty($value)):
                $rules['cl_file.'.$i] = 'required';
                $rules['file_id.'.$i] = 'required';
//                id=0->add
                $rules['file_status.'.$i] = 'required';
//                0-> nothing 1->edit 2->create
            endif;
        endforeach;

        return $rules;
    }
}
