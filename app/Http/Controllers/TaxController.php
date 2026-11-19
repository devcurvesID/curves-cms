<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxRequest;
use App\Model\Club\ClubInfo;
use App\Model\Tax\TaxConfig;
use App\Model\Tax\Taxtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('tax-type', ['only' => [
            'tax_type_list',
            'store_tax_type',
            'update_tax_type_status',
        ]]);
    }

    public function indexTax()
    {
        $taxes = TaxConfig::all();

        $tax_type = Taxtype::pluck('tax_type', 'id');
        $clubs = ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id');
        return view('cf_back.tax.tax', compact('taxes','tax_type','clubs'));
    }

    public function storeTaxConfig(Request $request)
    {
        $tax_config = new TaxConfig;
        $tax_config->club_id = $request->club_id;
        $tax_config->tax_type_id = $request->tax_type_id;
        $tax_config->rate = $request->rate;
        $tax_config->from_date = $request->from_date;
        $tax_config->thru_date = $request->thru_date;
        $tax_config->save();

        Session::flash('flash_message', trans('messages.tax_create_message'));
        return redirect('cf-back/tax');
    }
    public function storeTax(Request $request)
    {
        $input = $request->all();
        Taxtype::create($input);
        Session::flash('flash_message', trans('messages.tax_create_message'));
        return 'wakaciau';
        return redirect('cf-back/tax');
    }

    public function editTax($id)
    {
        $taxes = TaxConfig::findOrFail($id);
        $tax_type = Taxtype::pluck('tax_type', 'id');
        $clubs = ClubInfo::orderBy('club_name', 'asc')->pluck('club_name', 'id');
        return view('cf_back.tax.edit-tax', compact('taxes','tax_type', 'clubs'));
    }

    public function updateTax($id, Request $request)
    {
        $taxes = TaxConfig::findOrFail($id);
        $taxes->club_id = $request->club_id;
        $taxes->tax_type_id = $request->tax_type_id;
        $taxes->rate = $request->rate;
        $taxes->from_date = $request->from_date;
        $taxes->thru_date = $request->thru_date;
        $taxes->save();
        Session::flash('flash_message', trans('messages.tax_update_message'));
        return redirect('cf-back/tax');
    }

    public function destroyTax($id)
    {
        $taxes = TaxConfig::findOrFail($id);
        $taxes->delete();
        Session::flash('flash_message', trans('messages.tax_delete_message'));
        return redirect('cf-back/tax');
    }

    //CF LITE
    public function tax_type_list() {
        $tax_types = Taxtype::orderBy('tax_type', 'asc')->paginate(100);

        return view('cf_lite.tax.tax-type-list', compact('tax_types'));
    }

    public function store_tax_type(Request $request) {
        $this->validate($request, [
            'tax_type' => 'required|string|max:255|unique:tax_types',
        ]);

        $tax_type = new Taxtype;
        $tax_type->tax_type = $request->tax_type;
        $tax_type->status = 'Active';
        $tax_type->save();

        Session::flash('flash_message', trans('messages.tax_type_has_been_added'));
        return redirect('cf-lite/tax-type');
    }

    public function update_tax_type_status($id) {
        $tax_type = Taxtype::findOrFail($id);

        if ($tax_type->status == 'Active'):
            $tax_type->status = 'Inactive';
            Session::flash('flash_message', trans('messages.tax_type_has_been_deactivated'));
        elseif ($tax_type->status == 'Inactive'):
            $tax_type->status = 'Active';
            Session::flash('flash_message', trans('messages.tax_type_has_been_activated'));
        endif;
        $tax_type->save();

        return redirect('cf-lite/tax-type');
    }
}
