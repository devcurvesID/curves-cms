<?php

namespace App\Http\Controllers;

use App\Model\Accounting\ChartOfAccount;
use App\Model\Club\ClubInfo;
use App\Model\MasterData\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('bank');
    }

    public function bank(){
        if (Auth::user()->role_id == 4):
            $clubs = ClubInfo::selectRaw('club_infos.id, club_infos.club_name, club_infos.status')
                ->where('club_infos.status', '=', 'Active')
                ->where('users.role_id', '=', 4)
                ->join('users', 'users.club_id', '=', 'club_infos.id')
                ->orderBy('club_infos.club_name', 'asc')->get();
        else:
            $clubs = ClubInfo::where('status', '=', 'Active')
                ->orderBy('club_name', 'asc')->get();
        endif;
        $coa = ChartOfAccount::where('main_account_id', '=', 1)->where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)->pluck('coa_abv_name', 'id');
        $banks = Bank::where('id','<>', 1)
            ->orderBy('bank_name', 'asc')->get();

        return view('cf_lite.bank.bank', compact('clubs', 'coa', 'banks'));
    }

    public function store_new_bank(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:mst_banks,bank_name',
            'abv_name' => 'required|unique:mst_banks,bank_abv',
            'number' => 'required|unique:mst_banks,bank_account_number',
            'coa' => 'required',
        ]);

        $bank = new Bank;
        $bank->bank_name = $request->name;
        $bank->bank_abv = $request->abv_name;
        $bank->bank_account_number = $request->number;
        $bank->coa_id = $request->coa;
        $bank->save();

        $bank->bank_club()->attach($request->input('clubs'));

        Session::flash('flash_message', trans('messages.bank_successfully_added'));

        return redirect('cf-lite/bank');
    }

    public function update_bank(Request $request){
        $this->validate($request, [
            'edit_name' => 'required|unique:mst_banks,bank_name,' . $request->bank_id,
            'edit_abv_name' => 'required|unique:mst_banks,bank_abv,' . $request->bank_id,
            'edit_number' => 'required|unique:mst_banks,bank_account_number,'.$request->bank_id,
            'edit_coa' => 'required',
        ]);

        $bank = Bank::findOrFail($request->bank_id);
        if(!empty($bank)){
            $bank->bank_name = $request->edit_name;
            $bank->bank_abv = $request->edit_abv_name;
            $bank->bank_account_number = $request->edit_number;
            $bank->coa_id = $request->edit_coa;

            if($bank->save()){
                $bank->bank_club()->sync($request->input('clubs'));
                Session::flash('flash_message',trans('messages.bank_updated'));
            }else{
                Session::flash('error_message',trans('messages.failed_to_update_bank'));
            }
        }else{
            Session::flash('error_message',trans('messages.bank_not_found'));
        }
        return redirect('cf-lite/bank');
    }

    public function delete_bank($id){
        $bank = Bank::findOrFail($id);
        $bank->bank_club()->detach();
        try{
            $bank->delete();
            Session::flash('flash_message',trans('messages.bank_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_bank'));
        }

        return redirect('cf-lite/bank');
    }

    public function edit_bank_ajax(Request $request){
        $this->validate($request, [
            'bank_id' => 'required',
        ]);

        $bank = Bank::findOrFail($request->bank_id);
        $selectedclub = $bank->bank_club;

        $allclub = ClubInfo::where('status', '=', 'Active')->get();

        return response()->json(array('bank'=>$bank, 'selectedclub' => $selectedclub, 'allclub' => $allclub));
    }
}
