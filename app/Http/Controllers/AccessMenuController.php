<?php

namespace App\Http\Controllers;

use Session;
use App\Model\Club\ClubInfo;
use App\Model\MasterData\AccessMenu;
use App\User;
use Illuminate\Http\Request;

class AccessMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function access_menu_club_list() {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->paginate(12);

        return view('cf_lite.access_menus.club-list', compact('clubs'));
    }

    public function access_menu_staff_list($id) {
        $club = ClubInfo::findOrFail($id);

        $staffs = User::where('club_id', '=', $id)->where('role_id', '<>', '7')->orderBy('role_id', 'asc')->paginate(12);

        return view('cf_lite.access_menus.staff-list', compact('staffs', 'club'));
    }

    public function access_menu_config($id) {
        $staff = User::findOrFail($id);

        $access_menu_list = AccessMenu::all();

        return view('cf_lite.access_menus.access-menu-config', compact('staff', 'access_menu_list'));
    }

    public function update_staff_access_setting($id, Request $request) {
        $staff = User::findOrFail($id);

        if (count($staff->user_accessmenu) > 0):
            $staff->user_accessmenu()->sync($request->input('access'));
        else:
            $staff->user_accessmenu()->attach($request->input('access'));
        endif;

        Session::flash('cf_lite.includes.flash-message', trans('messages.staff_access_menu_has_been_updated'));
        return redirect('cf-lite/access-menu/staff/'.$staff->id);
    }
}
