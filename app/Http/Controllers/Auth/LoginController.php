<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Helpers\Accurate;
use Session;
use App\Services\ApiClientService;
use Illuminate\Support\Facades\DB;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'cf-lite/dashboard';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'username';
        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    public function authenticated($request, $user )
    {
        //$accu = Accurate::accurateClub($user->club_id);
       // Session::put('accurate', $accu);
        //$_SESSION['accurate'] = Accurate::accurateClub($user->club_id);

        //dd(Session::get('accurate'));

        // Jonviter DEV
        // $user_by_id = DB::table('usermobilecurves_v')->where('id', $user->id)->first();
        // if ($user_by_id):
        //     $apiClientOne = new ApiClientService();
        //     $fetch_usercurves = $apiClientOne->post("/mobile-curves/user", (array) $user_by_id);
        // endif;
        // Jonviter DEV
        return redirect()->intended($this->redirectPath());
    }


}
