<?php

namespace App\Http\Controllers;

use App\Model\Accounting\MainAccount;
use App\Model\MasterData\CoaDefault;
use Illuminate\Http\Request;

class MasterCoaDefaultController extends Controller
{
    public function __construct()
    {
        $this->middleware('developer-only');
    }

    public function index() {
        $main_accounts = MainAccount::pluck('account_abv_name', 'id');
        $coas = CoaDefault::where('coa_number', '<>', '0-0000')->orderBy('coa_number', 'asc')->get();

        return view('developer.coa-default.index', compact('main_accounts', 'coas'));
    }

    public function store_coa_default(Request $request){
        $this->validate($request, [
            'number' => 'required|unique:mst_coas,coa_number',
            'name' => 'required|unique:mst_coas,coa_name',
            'coa_abv' => 'required|unique:mst_coas,coa_abv_name',
            'coa_main' => 'required',
        ]);

        $coa = new CoaDefault;
        $coa->coa_number = $request->number;
        $coa->coa_name = $request->name;
        $coa->coa_abv_name = $request->coa_abv;
        $coa->main_account_id = $request->coa_main;
        $coa->save();

        return redirect('cf-lite/master/coa-default');
    }

    public function update_coa_default(Request $request){
        $this->validate($request, [
            'edit_number' => 'required|unique:mst_coas,coa_number,'.$request->coa_id,
            'edit_name' => 'required|unique:mst_coas,coa_name,'.$request->coa_id,
            'edit_coa_abv' => 'required|unique:mst_coas,coa_abv_name,'.$request->coa_id,
            'edit_coa_main' => 'required',
        ]);

        $coa = CoaDefault::findOrFail($request->coa_id);
        if (!empty($coa)) {
            $coa->coa_number = $request->edit_number;
            $coa->coa_name = $request->edit_name;
            $coa->coa_abv_name = $request->edit_coa_abv;
            $coa->main_account_id = $request->edit_coa_main;
            $coa->save();
        }

        return redirect('cf-lite/master/coa-default');
    }

    public function edit_ajax(Request $request) {
        $coa = CoaDefault::findOrFail($request->coa_id);
        return response()->json($coa);
    }
}
