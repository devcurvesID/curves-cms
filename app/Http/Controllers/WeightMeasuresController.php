<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\MasterData\WeighMeasureAttribute;
use Session;

class WeightMeasuresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function weight_measures_list() {
        $weight_measures = WeighMeasureAttribute::orderBy('attribute_name', 'asc')->paginate(10);

        return view('cf_lite/attributes/weight-measures-list', compact('weight_measures'));
    }

    public function store_weight_measures(Request $request){
        $this->validate($request, [
            'attribute_name' => 'required|string',
            'description' => 'sometimes',
        ]);
    	$weight_measure = new WeighMeasureAttribute;
        $weight_measure->attribute_name = $request->attribute_name;
        $weight_measure->description = $request->description;
        $weight_measure->save();

        Session::flash('flash_message', trans('messages.weight_measure_create_message'));

        return redirect('cf-lite/attributes/weight-measures-list');
    }

    public function update_weight_measures($id, Request $request){
    	$weight_measure = WeighMeasureAttribute::findOrFail($id);
    	$weight_measure->attribute_name = $request->attribute_name;
        $weight_measure->description = $request->description;
    	$weight_measure->update();
    	//$member_attrib->update($request->all());

        Session::flash('flash_message', trans('messages.weight_measure_update_message'));

        return redirect('cf-lite/attributes/weight-measures-list');
    }

    public function delete_weight_measures($id){
    	$weight_measure = WeighMeasureAttribute::findOrFail($id);
    	$weight_measure->delete();

        Session::flash('flash_message', trans('messages.weight_measures_delete_message'));

        return redirect('cf-lite/attributes/weight-measures-list');
    }
}
