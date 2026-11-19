<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\MasterData\MemberAttribute;
use Session;

class MemberAttributesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function attribute_setting_choice(){
    	return view('cf_lite/attributes/attribute-setting-choice');
    }

    public function member_attributes_list() {
        $member_attributes = MemberAttribute::orderBy('attribute', 'asc')->paginate(100);

        return view('cf_lite/attributes/member-attributes-list', compact('member_attributes'));
    }

    public function store_member_attributes(Request $request){
        $this->validate($request, [
            'attribute' => 'required|string',
        ]);

    	$member_attribute = new MemberAttribute;
        $member_attribute->attribute = $request->attribute;
        $member_attribute->save();

        Session::flash('flash_message', trans('messages.member_attribute_create_message'));

        return redirect('cf-lite/attributes/member-attributes-list');
    }

    public function update_member_attributes($id, Request $request){
        $this->validate($request, [
            'attribute' => 'required|string',
        ]);
    	$member_attribute = MemberAttribute::findOrFail($id);
    	$member_attribute->attribute = $request->attribute;
    	$member_attribute->update();
    	//$member_attrib->update($request->all());

        Session::flash('flash_message', trans('messages.member_attribute_update_message'));

        return redirect('cf-lite/attributes/member-attributes-list');
    }

    public function delete_member_attributes($id){
    	$member_attribute = MemberAttribute::findOrFail($id);
    	$member_attribute->delete();

        Session::flash('flash_message', trans('messages.member_attributes_delete_message'));

        return redirect('cf-lite/attributes/member-attributes-list');
    }
}
