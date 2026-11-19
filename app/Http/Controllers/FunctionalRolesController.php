<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Role\FunctionalRole;
use Session;

class FunctionalRolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    //CF LITE
    public function functional_roles_list() {
        $func_roles = FunctionalRole::orderBy('functional_role', 'asc')->paginate(10);

        return view('cf_lite.roles.functional-roles-list', compact('func_roles'));
    }

    public function store_functional_role(Request $request){
        $this->validate($request, [
            'functional_roles' => 'required|string',
        ]);
    	$func_role = new FunctionalRole;
        $func_role->functional_role = $request->functional_roles;
        $func_role->save();

        Session::flash('flash_message', trans('messages.functional_role_create_message'));

        return redirect('cf-lite/roles/functional-roles-list');
    }

    public function update_functional_role($id, Request $request){
        $this->validate($request, [
            'functional_roles' => 'required|string',
        ]);
    	$func_role = FunctionalRole::findOrFail($id);
    	$func_role->functional_role = $request->functional_roles;
    	$func_role->update();
    	//$func_role->update($request->all());

        Session::flash('flash_message', trans('messages.functional_role_update_message'));

        return redirect('cf-lite/roles/functional-roles-list');
    }

    public function delete_functional_role($id){
    	$func_role = FunctionalRole::findOrFail($id);
    	$func_role->delete();

        Session::flash('flash_message', trans('messages.functional_role_delete_message'));

        return redirect('cf-lite/roles/functional-roles-list');
    }
}
