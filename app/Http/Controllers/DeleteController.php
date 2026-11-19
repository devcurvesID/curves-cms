<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPaymentRequest;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\TempJournal;
use App\Model\Club\ClubInfo;
use App\Model\Locker\LockerPayment;
use App\Model\Payment\MemberPayment;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('log-delete');
    }

    public function get_view(Request $request) {
        $datas = [];
        $clubs = ClubInfo::where('status', '=', 'Active')->pluck('club_name', 'id');

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        if (!empty($request->club) && !empty($request->category)) {
            if ($request->category == 'product') {
                $datas = ProductPayment::where('club_id', '=', $request->club)
                    ->whereDate('payment_date', '>=', $fromdate)
                    ->whereDate('payment_date', '<=', $thrudate)
                    ->get();
            } elseif ($request->category == 'membership') {
//                $datas = MemberPayment::where('club_id', '=', $request->club)->get();
            } elseif ($request->category == 'locker') {
                $datas = LockerPayment::where('club_id', '=', $request->club)->get();
            } elseif ($request->category == 'user') {
                $datas = User::where('club_id', '=', $request->club)->get();
            }
        }

        return view('cf_lite.delete-function', compact('clubs', 'datas', 'fromdate', 'thrudate'));
    }

    public function delete(Request $request) {
        if($request->status == 'product'){
            $data = ProductPayment::findOrFail($request->id);
            $this->delete_product($data);
        }

        return redirect('cf-lite/delete-function');
    }

    public function delete_product(ProductPayment $product) {
        // Get detail
        $product_detail = ProductPaymentDetail::where('product_payment_id', '=', $product->id)->get();
        foreach ($product_detail as $item) {
            // Set stock product
            $product_inv = ProductInventory::where('club_id','=', $product->club_id)
                ->where('product_id', '=', $item->product_id)
                ->first();
            $product_inv->qty += $item->qty;
            $product_inv->save();

            // Delete product payment detail
            $item->delete();
        }

        // Check accounting journal
        $temp_journal = TempJournal::where('invoice_number', 'ILIKE', '%'.$product->payment_number.'%')->get();
        if ($temp_journal->count()) {
            foreach ($temp_journal as $item) {
                $item->delete();
            }
        } else {
            $acc_journal = AccountingJournalV2::where('invoice_number', 'ILIKE', '%'.$product->payment_number.'%')->get();
            if ($acc_journal->count()) {
                foreach ($acc_journal as $item) {
                    $item->delete();
                }
            }
        }

        // Delete Product
        $product->delete();
    }

    public function delete_membership() {

    }

    public function delete_locker() {

    }

    public function delete_user($user) {

    }

    public function get_detail_ajax($id, Request $request) {
        if ($request->category == 'product') {
            $header = ProductPayment::findOrFail($id);
            $detail = ProductPaymentDetail::where('product_payment_id', '=', $header->id)->get();

            $header->setAttribute('member_name', $header->productpayment_user->name);
            foreach ($detail as $item) {
                $item->setAttribute('product_name', $item->productpaymentdetail_product->item_name);
            }

            return response()->json(['header' => $header, 'detail' => $detail], '200');
        }
    }
}
