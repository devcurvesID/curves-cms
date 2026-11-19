<?php

namespace App\Http\Controllers;

use App\Model\Accounting\ChartOfAccount;
use App\Model\MasterData\CostRevenue;
use Illuminate\Http\Request;
use Session;

class AccountingMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    public function cost_revenue(){
        $cost_revenue = CostRevenue::get();
        $coas = ChartOfAccount::where('main_account_id', '<>', 1)
            ->where('main_account_id', '<>', 22)
            ->orderBy('coa_abv_name', 'asc')->get();

        return view('cf_acc.master.cost-revenue', compact('cost_revenue', 'coas'));
    }

    public function store_cost_revenue(Request $request){
        $this->validate($request, [
            'account_info' => 'required|unique:mst_cost_revenues,account_info',
            'transaction_info' => 'required',
        ]);

        $cost = new CostRevenue;
        $cost->account_info = $request->account_info;
        $cost->default_transaction_info = $request->transaction_info;
        $cost->coa_id = $request->coa;
        $cost->save();

        return redirect('cf-acc/accounting/cost-revenue');
    }

    public function delete_cost_revenue($id){
        $cost = CostRevenue::findOrFail($id);
        try{
            $cost->delete();
            Session::flash('flash_message',trans('messages.cost_revenue_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_cost_revenue'));
        }
        return redirect('cf-acc/accounting/cost-revenue');
    }

    public function update_cost_revenue(Request $request){
        $this->validate($request, [
            'edit_account_info' => 'required|unique:mst_cost_revenues,account_info,'.$request->cost_id,
            'edit_transaction_info' => 'required',
        ]);

        $cost = CostRevenue::findOrFail($request->cost_id);
        if(!empty($cost)){
            $cost->account_info = $request->edit_account_info;
            $cost->default_transaction_info = $request->edit_transaction_info;
            $cost->coa_id = $request->edit_coa;
            $cost->save();
            Session::flash('flash_message',trans('messages.cost_revenue_updated'));
        } else {
            Session::flash('error_message',trans('messages.failed_to_update_cost_revenue'));
        }
        return redirect('cf-acc/accounting/cost-revenue');
    }

    public function edit_cost_revenue_ajax(Request $request){
        $this->validate($request, [
            'cost_id' => 'required',
        ]);
        $cost = CostRevenue::findOrFail($request->cost_id);

        return response()->json(array('cost'=>$cost));
    }
}
