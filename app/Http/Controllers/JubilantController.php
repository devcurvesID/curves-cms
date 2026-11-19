<?php

namespace App\Http\Controllers;

use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\CoaDefault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class JubilantController extends Controller
{
    public function __construct()
    {
        $this->middleware('jubilant');
    }

    public function index() {
        $jubilants = Jubilant::orderBy('type', 'desc')->get();
        return view('cf_lite.jubilant.index', compact('jubilants'));
    }

    public function store_jubilant(Request $request) {
        $this->validate($request, [
            'jubilant_name' => 'required|unique:mst_jubilants,jubilant_name',
        ]);

        $jubilant = new Jubilant;
        $jubilant->jubilant_name = $request->jubilant_name;
        $jubilant->type = $request->type;


        if ($jubilant->save()) {
            Session::flash('flash_message', trans('messages.jubilant_successfully_added'));

            //Add Chart of Account
            $coadefault = CoaDefault::get();
            foreach ($coadefault as $item) {
                $coa = new ChartOfAccount;
                $coa->coa_number = $item->coa_number;
                $coa->coa_name = $item->coa_name;
                $coa->coa_abv_name = $item->coa_abv_name;
                $coa->main_account_id = $item->main_account_id;
                $coa->user_id = Auth::user()->id;
                $coa->jubilant_id = $jubilant->id;
                $coa->save();
            }

        } else {
            Session::flash('error_message', trans('messages.failed_to_add_jubilant'));
        }

        return redirect('cf-lite/master/jubilant');
    }

    public function update_jubilant(Request $request) {
        $this->validate($request, [
            'edit_jubilant_name' => 'required|unique:mst_jubilants,jubilant_name,'.$request->jubilant_id,
        ]);

        $jubilant = Jubilant::findOrFail($request->jubilant_id);
        if(!empty($jubilant)){
            $jubilant->jubilant_name = $request->edit_jubilant_name;
            $jubilant->type = $request->edit_type;

            if($jubilant->save()){
                Session::flash('flash_message',trans('messages.jubilant_updated'));
            }else{
                Session::flash('error_message',trans('messages.failed_to_update_jubilant'));
            }
        }else{
            Session::flash('error_message',trans('messages.jubilant_not_found'));
        }
        return redirect('cf-lite/master/jubilant');
    }

    public function delete_jubilant($id) {
        $jubilant = Jubilant::findOrFail($id);
        try{
            $jubilant->delete();
            Session::flash('flash_message',trans('messages.jubilant_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_jubilant'));
        }

        return redirect('cf-lite/master/jubilant');
    }

    public function edit_jubilant_ajax(Request $request) {
        $jubilant = Jubilant::findOrFail($request->jubilant_id);

        return response()->json($jubilant);
    }

    public function setting_acconting_account_jubilant($id) {
        $user = AccountingAccount::findOrFail($id);
        $jubilants = Jubilant::get();
        return view('cf_lite.jubilant.accounting-account-jubilant', compact('user', 'jubilants'));
    }

    public function store_accounting_account_setting($id, Request $request) {
        if (Auth::user()->role_id < 4) {
            $user = AccountingAccount::findOrFail($id);

            if (!empty($request->input('jubilant'))) {
                if (count($user->accounting_jubilant) > 0) {
                    $user->accounting_jubilant()->sync($request->input('jubilant'));
                } else {
                    $user->accounting_jubilant()->attach($request->input('jubilant'));
                }
            } else {
                $user->accounting_jubilant()->detach();
            }

            Session::flash('flash_message', trans('messages.accounting_account_update_succeed'));
            return redirect('cf-lite/setting/accounting-account/' . $user->id . '/setting-list-jubilant');
        } else {
            return redirect('cf-lite/setting/accounting-account');
        }
    }
}
