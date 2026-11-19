<?php

namespace App\Http\Controllers;

use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\CostRevenueCenter;
use App\Model\Club\ClubInfo;
use Illuminate\Http\Request;
use Session;

class CostRevenueCenterController extends Controller
{
    public function index() {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->get();

        return view('cf_acc.accounting.cost-revenue-center', compact('clubs'));
    }

    public function update_cost_revenue(Request $request) {
        $this->validate($request, [
            'club' => 'required',
            'code' => 'required|unique:cost_revenue_centers,cr_code,'.$request->club.',club_id',
            'name' => 'required|string|unique:cost_revenue_centers,name,'.$request->club.',club_id',
        ]);

        $club = ClubInfo::findOrFail($request->club);
        $club_cr = new CostRevenueCenter;
        $club_cr->cr_code = $request->code;
        $club_cr->name = $request->name;
        $club->club_costrevenuecenter()->save($club_cr);

        return redirect('cf-acc/accounting/cost-revenue-center');
    }

    public function get_cost_revenue(Request $request) {
        $club = ClubInfo::findOrFail($request->club_id);

        if (!empty($club->club_costrevenuecenter)):
            $club->setAttribute('cr_code', $club->club_costrevenuecenter->cr_code);
            $club->setAttribute('cr_name', $club->club_costrevenuecenter->name);
        endif;

        return response()->json($club);
    }
}
