<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesRequest;
use App\Model\Role\Role;
use Session;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index() {
        $roles = Role::all()->sortBy('role');
        $count = $roles->count();
        return view('cf_back.roles.roles', compact('roles', 'count'));
    }

    public function store(RolesRequest $request) {
        $input = $request->all();

        Role::create($input);

        Session::flash('cf_back.flash_message', trans('messages.role_create_message'));

        return redirect('cf-back/role');
    }

    public function update($id, RolesRequest $request) {
        $role = Role::findOrFail($id);

        $role->role = $request->input('role');
        $role->save();

        Session::flash('cf_back.flash_message', trans('messages.role_update_message'));

        return redirect('cf-back/role');
    }

    public function destroy($id) {
        $role = Role::findOrFail($id);
        $role->delete();

        Session::flash('cf_back.flash_message', trans('messages.role_delete_message'));

        return redirect('cf-back/role');
    }
}
