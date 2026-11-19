<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBulkVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Model\Voucher\Voucher;
use Carbon\Carbon;
use Session;
use App\Model\Voucher\VoucherType;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function voucher_type_list() {
        $voucher_types = VoucherType::all();

        return view('cf_lite.voucher.voucher-type-list', compact('voucher_types'));
    }

    public function store_voucher_type(Request $request) {
        $this->validate($request, [
            'voucher_type' => 'required|string|unique:voucher_types',
            'description' => 'sometimes',
        ]);

        $voucher_type = new VoucherType;
        $voucher_type->voucher_type = $request->voucher_type;

        if (!empty($request->description)):
            $voucher_type->description = $request->description;
        endif;

        $voucher_type->save();

        Session::flash('cf_lite.includes.flash-message', trans('messages.voucher_type_has_been_added'));

        return redirect('cf-lite/voucher');
    }

    public function voucher_list($id) {
        $voucher_type = VoucherType::findOrFail($id);

        $vouchers = Voucher::where('type_id', '=', $voucher_type->id)
            ->where('status', '<>', 'Used')
            ->whereDate('thru_date', '>=', Carbon::now()->toDateString())
            ->orderBy('identity_number', 'asc')
            ->paginate(100);

        return view('cf_lite.voucher.voucher-list', compact('voucher_type', 'vouchers'));
    }

    public function voucher_list_filter($id, Request $request) {
        $voucher_type = VoucherType::findOrFail($id);

        $vouchers = Voucher::where('type_id', '=', $voucher_type->id);

        if (!empty($request->voucher_code)):
            $vouchers->where('identity_number', 'LIKE', '%'.$request->voucher_code.'%');
        else:
            if (!empty($request->expired)):
                $vouchers->whereDate('thru_date', '<', Carbon::now()->toDateString());
                if (!empty($request->in_stock) && !empty($request->out)):
                    $vouchers->where(function ($q) {
                        $q->where('status', '=', 'InStock')
                            ->orWhere('status', '=', 'Out');
                    });
                elseif (!empty($request->in_stock && empty($request->out))):
                    $vouchers->where('status', '=', 'InStock');
                elseif (empty($request->in_stock && !empty($request->out))):
                    $vouchers->where('status', '=', 'Out');
                endif;
            else:
                $counter = 0;
                if (!empty($request->in_stock)):
                    $vouchers->where('status', '=', $request->in_stock);
                    $counter++;
                endif;

                if (!empty($request->out)):
                    if ($counter > 0):
                        $vouchers->orWhere('status', '=', $request->out);
                    else:
                        $vouchers->where('status', '=', $request->out);
                    endif;
                    $counter++;
                endif;

                if (!empty($request->used)):
                    if ($counter > 0):
                        $vouchers->orWhere('status', '=', $request->used);
                    else:
                        $vouchers->where('status', '=', $request->used);
                    endif;
                endif;
            endif;
        endif;

        $vouchers = $vouchers->orderBy('identity_number', 'asc')->paginate(100);

        return view('cf_lite.voucher.voucher-list', compact('voucher_type', 'vouchers'));
    }

    public function voucher_bulk_add() {
        $voucher_types = VoucherType::pluck('voucher_type', 'id');

        return view('cf_lite.voucher.add-voucher', compact('voucher_types'));
    }

    public function store_voucher(AddBulkVoucherRequest $request) {
        if (count($request->voucher_code) > 0):
            foreach ($request->voucher_code as $key => $value):
                if (!empty($value)):
                    $voucher = new Voucher;
                    $voucher->identity_number = $value;
                    $voucher->from_date = $request->from_date;
                    $voucher->thru_date = $request->thru_date;
                    $voucher->type_id = $request->type_id;
                    $voucher->status = 'InStock';
                    $voucher->voucher_base = $request->voucher_base;

                    if ($request->voucher_base == 'value'):
                        $voucher->value = $request->value;
                        $voucher->percent = 0;
                    elseif ($request->voucher_base == 'percent'):
                        $voucher->value = $request->limit;
                        $voucher->percent = $request->percent;
                    endif;

                    $voucher->save();
                endif;
            endforeach;
        endif;

        return redirect('cf-lite/voucher');
    }

    public function update_voucher($id, UpdateVoucherRequest $request) {
        $voucher_type = $id;

        if ($request->has('voucher_out')):
            foreach ($request->voucher as $key => $value):
                if (!empty($value)):
                    $voucher = Voucher::findOrFail($value);
                    if ($voucher->status == 'InStock'):
                        $voucher->status = 'Out';
                        $voucher->save();
                    endif;
                endif;
            endforeach;
        else:
            foreach ($request->voucher as $key => $value):
                if (!empty($value)):
                    $voucher = Voucher::findOrFail($value);
                    $voucher->from_date = $request->from_date;
                    $voucher->thru_date = $request->thru_date;
                    $voucher->status = 'InStock';
                    $voucher->voucher_base = $request->voucher_base;

                    if ($request->voucher_base == 'value'):
                        $voucher->value = $request->new_value;
                    elseif ($request->voucher_base == 'percent'):
                        $voucher->percent = $request->new_percent;
                        $voucher->value = $request->new_limit;
                    endif;

                    $voucher->save();
                endif;
            endforeach;
        endif;

        Session::flash('cf_lite.includes.flash-message', trans('messages.voucher_has_been_updated'));

        return redirect('cf-lite/voucher/list/'.$voucher_type);
    }
}
