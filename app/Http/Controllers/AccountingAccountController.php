<?php

namespace App\Http\Controllers;

use App\Model\Accounting\AccountingAccount;
use App\Model\Club\ClubInfo;
use App\Model\Jubilant\Jubilant;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountingAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth', ['except' => [
            'login',
            'acc_login_authentication',
            'registration_login_check',
            'registration',
            'store_registration',
            'registration_logout'
        ]]);

        $this->middleware('auth', ['only' => [
//            'registration',
            'store_registration',
//            'registration_logout'
        ]]);
    }

    public function login() {
        if (!Session::get('acc')):
            return view('cf_acc.accounts.login-page');
        else:
            return redirect('cf-acc/dashboard');
        endif;
    }

    public function acc_login_authentication(Request $request) {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $check = AccountingAccount::where('username', '=', $request->username)->first();

        if ($check && Hash::check($request->password, $check->password)):
            Session::put('acc', true);
            Session::put('account', AccountingAccount::find($check->id));
            return redirect('cf-acc/dashboard');
        else:
            return redirect('cf-acc/login');
        endif;
    }

    public function acc_logout() {
        Session::flush();

        return redirect('cf-acc/login');
    }

    public function registration_login_check(Request $request) {
        $the_status = false;
        $check = User::where('username', '=', $request->reg_username)->first();
        if ($check && Hash::check($request->reg_password, $check->password)):
            $the_status = true;
        endif;

        if ($the_status):
            //cek role
            if ($check->role_id <= 4):
                if (Auth::attempt(['username' => $request->reg_username, 'password' => $request->reg_password])):
                    Session::put('registration', true);
                    Session::put('user', $check->id);

                    return redirect('cf-acc/registration');
                endif;
            endif;
        endif;

        return redirect('cf-acc/login');
    }

    public function registration() {
        if (!Session::get('registration')):
            return redirect('cf-acc/login');
        endif;

        $user = User::findOrFail(Session::get('user'));
        
        // OLD (Sekarang user pilih jubilant bukan pilih club)
        // if ($user->role_id <= 3):
        //     $clubs = ClubInfo::pluck('club_name', 'id');
        // else:
        //     $clubs = $user->manager_club->pluck('club_name', 'id');
        // endif;
        // end of old

        // NEW 17 Desember 2019
        if($user->user_club->club_jubilant->type == 'Main'){
            $jubilants = Jubilant::pluck('jubilant_name','id');
        }else if($user->user_club->club_jubilant->type == 'Franchise'){
            $jubilants = Jubilant::where('id','=',$user->user_club->jubilant_id)->pluck('jubilant_name','id');
        }

        // OLD
        // return view('cf_acc.accounts.registration', compact('clubs'));

        // NEW (17 Desember 2019) Bukan pilih club tapi pilih jubilant
        return view('cf_acc.accounts.registration',compact('jubilants','user'));
    }

    public function store_registration(Request $request) {
        $this->validate($request, [
            'name' => 'required|string',
            'username' => 'required|string|max:50|unique:accounting_accounts',
            'password' => 'required|string|min:6|confirmed',
            'jubilants' => 'required',
        ]);

        $account = new AccountingAccount;
        $account->username = $request->username;
        $account->password = bcrypt($request->password);
        $account->encrypted_password = '';
        $account->name = $request->name;
        $account->save();

        // OLD 
        // if (!empty($request->clubs)):
        //     $account->accounting_club()->attach($request->input('clubs'));
        // endif;
        // END OF OLD

        // NEW (17 Desember 2019)
        if(!empty($request->jubilants)){
            $account->accounting_jubilant()->attach($request->input('jubilants'));
        }
        //END OF NEW

        Session::flush();
        return redirect('cf-acc/login');
    }

    public function registration_logout() {
        Session::flush();
        Auth::logout();

        return redirect('cf-acc/login');
    }
}
