<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Jubilant\Jubilant;
use Illuminate\Http\Request;
use Session;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Club\ClubInfo;
use App\Model\Accounting\PurchasingBook;
use App\Model\Accounting\PurchasingBookPayment;

class PurchasingBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    public function index(){

    }

    public function purchasing_book(Request $request){
        $user = AccountingAccount::findOrFail(Session::get('account')->id);

        $jubilants = Jubilant::whereHas('jubilant_accounting')->orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');
        $clubs = ClubInfo::where('status', '=', 'Active')->whereHas('club_jubilant', function ($q) {
            $q->whereHas('jubilant_accounting');
        })->orderBy('club_name', 'asc')->get();

        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');
    	$coapb = ChartOfAccount::where('main_account_id','=',7)->get();
    	$coapy = ChartOfAccount::where('coa_number','=','1-1100')->orWhereRaw('coa_number IN (SELECT coa_number FROM chart_of_accounts JOIN mst_banks ON chart_of_accounts.id = mst_banks.coa_id WHERE mst_banks.id IN (SELECT id_bank FROM bank_club WHERE club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')))')->get();

        // COA Added 28082019
        $coas = ChartOfAccount::select('id', 'coa_number', 'coa_name')
            ->orderBy('coa_number', 'asc')
            ->get();

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }
        $purchasingbooks = PurchasingBook::whereDate('purchasing_date','>=',$fromdate)->whereDate('purchasing_date','<=',$thrudate);

        if(!empty($request->clubs)){

            $clubstring = '(';
            foreach ($request->clubs as $key => $value) {
                $clubstring .= $value;
                if($key < count($request->clubs)-1){
                    $clubstring .= ', ';
                }
            }
            $clubstring .= ')';

            $purchasingbooks->whereRaw('club_id IN '.$clubstring);
        }else{
            $purchasingbooks->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
        }

        // COA Added 28082019
        if (!empty($request->coas)):
            $purchasingbooks->where(function ($c) use ($request) {
                $coa_count = 0;
                foreach ($request->coas as $coa):
                    if ($coa_count == 0):
                        $c->where('coa_id', '=', $coa);
                    else:
                        $c->orWhere('coa_id', '=', $coa);
                    endif;
                    $coa_count++;
                endforeach;
            });
        endif;

        $purchasingbooks = $purchasingbooks->orderBy('purchasing_date','DESC')->orderBy('created_at','DESC')->get();

    	return view('cf_acc.main.purchasing-book',compact('user','coapb','coapy','purchasingbooks','fromdate','thrudate','clubs', 'coas'));
    }

    public function store_purchasing_book(Request $request){
    	$this->validate($request,[
    		'purchase_date' => 'required|date',
    		'coapb' => 'required|integer',
    		'purchasing_info' => 'required|string',
    		'amount_due' => 'required|integer',
    		'amount_paid' => 'required|integer',
    		'coapy' => 'sometimes|integer', 
    		'selectedclub' => 'required'
    	]);

    	$purchasingbook = new PurchasingBook;
    	$purchasingbook->purchasing_date = $request->purchase_date;
    	$purchasingbook->coa_id = $request->coapb;
    	$purchasingbook->purchasing_info = $request->purchasing_info;
    	$purchasingbook->amount_due = $request->amount_due;
    	$purchasingbook->amount_paid = $request->amount_paid;
    	if($request->amount_due < $request->amount_paid){
    		$purchasingbook->rest_of_bill = 0;
    	}else{
    		$purchasingbook->rest_of_bill = $request->amount_due - $request->amount_paid;
    	}

    	if($purchasingbook->rest_of_bill == 0){
    		$purchasingbook->payment_status = 'Paid';
    	}else if($purchasingbook->rest_of_bill == $purchasingbook->amount_due){
    		$purchasingbook->payment_status = 'Unpaid';
    	}else{
    		$purchasingbook->payment_status = 'Halfpaid';
    	}
    	$purchasingbook->club_id = $request->selectedclub;
    	$purchasingbook->save();

        $pbb = new PurchasingBookPayment;

    	if($purchasingbook->payment_status != 'Unpaid'){
    		$pbb->purchasing_book_id = $purchasingbook->id;
    		$pbb->coa_id = $request->coapy;
    		$pbb->payment_date = $request->purchase_date;
    		$pbb->amount_paid = $purchasingbook->amount_paid;
    		$pbb->save();
    	}

        app('App\Http\Controllers\AccountingJournalController')->add_purchasing_book_journal($purchasingbook, $pbb, 0);

    	return redirect('cf-acc/purchasing-book');
    }

    public function pay_purchasing_book(Request $request){

    	$purchasingbook = PurchasingBook::findOrFail($request->pb_id);

    	if($purchasingbook->rest_of_bill == $request->amount_paid_pay){
    		$purchasingbook->rest_of_bill = 0;
    		$purchasingbook->payment_status = 'Paid';
    	}else if($purchasingbook->rest_of_bill > $request->amount_paid_pay){
    		$purchasingbook->rest_of_bill = $purchasingbook->rest_of_bill - $request->amount_paid_pay;
    		$purchasingbook->payment_status = 'Halfpaid';
    	}
    	$purchasingbook->amount_paid += $request->amount_paid_pay;
    	$purchasingbook->save();

    	$pbb = new PurchasingBookPayment;
		$pbb->purchasing_book_id = $purchasingbook->id;
		$pbb->coa_id = $request->coapypay;
		$pbb->payment_date = $request->payment_date_pay;
		$pbb->amount_paid = $request->amount_paid_pay;
		$pbb->save();

        if($purchasingbook->payment_status == 'Paid'){
            app('App\Http\Controllers\AccountingJournalController')->add_purchasing_book_journal($purchasingbook, $pbb, 1);
        }else{
            app('App\Http\Controllers\AccountingJournalController')->add_purchasing_book_journal($purchasingbook, $pbb, 0);
        }
		
    	return redirect('cf-acc/purchasing-book');
    }

    public function purchasing_bookV2(Request $request) {
        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');

        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        $user = AccountingAccount::findOrFail(Session::get('account')->id);

        $jubilants = Jubilant::whereHas('jubilant_accounting', function ($q) use ($user) {
            $q->where('accounting_id', '=', $user->id);
        })
            ->orderBy('jubilant_name', 'asc')
            ->pluck('jubilant_name', 'id');
        $clubs = ClubInfo::where('status', '=', 'Active')
            ->whereHas('club_jubilant', function ($q) {
                $q->whereHas('jubilant_accounting');
            })->orderBy('club_name', 'asc')->get();

        $coapb = ChartOfAccount::where('main_account_id', '=', 7)->get();
        $coapy = ChartOfAccount::where('coa_number', '=', '1-1100')
            ->orWhereRaw('
                coa_number IN (
                    SELECT coa_number 
                    FROM chart_of_accounts JOIN mst_banks ON chart_of_accounts.id = mst_banks.coa_id
                )
            ')->get();

        // SET Chosen CLUB & COA
        // Club
        if (!empty($request->clubs)) {
            $chosen_club = $request->clubs;
            if (count($chosen_club) == 1) {
                $chosen_club = $chosen_club[0];
            }
        } else {
            $chosen_club = null;
            foreach ($clubs as $key => $value) {
                if (($chosen_club == null) || ($chosen_club > $key)) {
                    $chosen_club = $key;
                }
            }
        }

        // COA
        if (!empty($request->coas)) {
            $chosen_coa = $request->coas;
            if (count($chosen_coa) == 1) {
                $chosen_coa = $chosen_coa[0];
            }
        } else {
            $chosen_coa = null;
        }

        $purchasingbooks = PurchasingBook::whereDate('purchasing_date','>=',$fromdate)->whereDate('purchasing_date','<=',$thrudate);

        if (!empty($request->jubilant)) {
            if (!empty($request->clubs)) {
                $clubstring = '(';
                foreach ($request->clubs as $key => $value) {
                    $clubstring .= $value;
                    if ($key < count($request->clubs) - 1) {
                        $clubstring .= ', ';
                    }
                }
                $clubstring .= ')';

                $purchasingbooks->whereRaw('club_id IN ' . $clubstring);
            } else {
                $purchasingbooks->where('jubilant_id', '=', $request->jubilant);
            }

            // COA Added 28082019
            if (!empty($request->coas)) {
                $purchasingbooks->where(function ($c) use ($request) {
                    $coa_count = 0;
                    foreach ($request->coas as $coa) {
                        if ($coa_count == 0) {
                            $c->where('coa_id', '=', $coa);
                        } else {
                            $c->orWhere('coa_id', '=', $coa);
                        }
                        $coa_count++;
                    }
                });
            }
        }

        $purchasingbooks = $purchasingbooks->orderBy('purchasing_date','DESC')->orderBy('created_at','DESC')->get();

        return view('cf_acc.main.purchasing-book-v2',compact('user', 'jubilants', 'chosen_coa', 'chosen_club', 'coapb','coapy','purchasingbooks','fromdate','thrudate','clubs'));
    }

    public function store_purchasing_bookV2(Request $request) {
        $this->validate($request,[
            'purchase_date' => 'required|date',
            'selectedclub' => 'required',
            'coapb' => 'required|integer',
            'purchasing_info' => 'required|string',
            'amount_due' => 'required|integer',
            'amount_paid' => 'required|integer',
            'coapy' => 'sometimes|integer',
        ]);

        $auto_number = AutoNumber::where('name','=','Purchasing')->first();
        $num = $auto_number->current_number;
        $auto_number->current_number += 1;
        $auto_number->save();

        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $invoice_number = sprintf('%08d', $num).'/PURCHASING/'.sprintf('%03d', $request->selectedclub).'/'.$month_roman[Carbon::parse($request->purchase_date)->format('n')].'/'.Carbon::parse($request->purchase_date)->format('Y');

        $purchasingbook = new PurchasingBook;
        $purchasingbook->purchasing_date = $request->purchase_date;
        $purchasingbook->coa_id = $request->coapb;
        $purchasingbook->purchasing_info = $request->purchasing_info;
        $purchasingbook->amount_due = $request->amount_due;
        $purchasingbook->amount_paid = $request->amount_paid;
        $purchasingbook->invoice_number = $invoice_number;
        $purchasingbook->jubilant_id = $request->jubilant;
        if($request->amount_due < $request->amount_paid) {
            $purchasingbook->rest_of_bill = 0;
        } else {
            $purchasingbook->rest_of_bill = $request->amount_due - $request->amount_paid;
        }

        if ($purchasingbook->rest_of_bill == 0){
            $purchasingbook->payment_status = 'Paid';
        } elseif ($purchasingbook->rest_of_bill == $purchasingbook->amount_due){
            $purchasingbook->payment_status = 'Unpaid';
        } else {
            $purchasingbook->payment_status = 'Halfpaid';
        }
        $purchasingbook->club_id = $request->selectedclub;
        if ($purchasingbook->save()) {
            $pbp = new PurchasingBookPayment;

            if($purchasingbook->payment_status != 'Unpaid'){
                $pbp->purchasing_book_id = $purchasingbook->id;
                $pbp->coa_id = $request->coapy;
                $pbp->payment_date = $request->purchase_date;
                $pbp->amount_paid = $purchasingbook->amount_paid;
                $pbp->save();
            }

            app('App\Http\Controllers\AccountingJournalController')->add_purchasing_book_journalV2($purchasingbook, $pbp);
        }

        Session::flash('flash_message',trans('messages.purchasing_book_added'));
        return redirect('cf-acc/purchasing-book');
    }

    public function pay_purchasing_bookV2(Request $request){
        $purchasingbook = PurchasingBook::findOrFail($request->pb_id);

        if ($purchasingbook->rest_of_bill == $request->amount_paid_pay){
            $purchasingbook->rest_of_bill = 0;
            $purchasingbook->payment_status = 'Paid';
        } elseif ($purchasingbook->rest_of_bill > $request->amount_paid_pay){
            $purchasingbook->rest_of_bill = $purchasingbook->rest_of_bill - $request->amount_paid_pay;
            $purchasingbook->payment_status = 'Halfpaid';
        }
        $purchasingbook->amount_paid += $request->amount_paid_pay;
        $purchasingbook->save();

        $pbp = new PurchasingBookPayment;
        $pbp->purchasing_book_id = $purchasingbook->id;
        $pbp->coa_id = $request->coapypay;
        $pbp->payment_date = $request->payment_date_pay;
        $pbp->amount_paid = $request->amount_paid_pay;
        $pbp->save();

        app('App\Http\Controllers\AccountingJournalController')->pay_purchasing_book_journal($purchasingbook, $pbp);

        return redirect('cf-acc/purchasing-book');
    }

    public function edit_purchasing_book($id) {
        $purchase_book = PurchasingBook::findOrFail($id);
        return view('cf_acc.main.purchasing-book-edit', compact('purchase_book'));
    }

    public function update_purchasing_book($id, Request $request) {
        $oldpb = PurchasingBook::findOrFail($id);
        if (!empty($oldpb)) {
            // Set minus Accounting Journal
            app('App\Http\Controllers\AccountingJournalController')->pembalik_purchasing_book($oldpb, 0);

            $newpb = PurchasingBook::findOrFail($id);
            $newpb->purchasing_date = $request->purchase_date;
            $newpb->purchasing_info = $request->purchasing_info;
            $newpb->amount_due = $request->amount_due;
            if ($request->amount_due <= $newpb->amount_paid) {
                $newpb->rest_of_bill = 0;
                $newpb->payment_status = 'Paid';
            } elseif ($request->amount_due > $newpb->rest_of_bill && $newpb->rest_of_bill > 0) {
                $newpb->rest_of_bill = $request->amount_due - $newpb->amount_paid;
                $newpb->payment_status = 'Halfpaid';
            }

            if ($newpb->save()) {
                // Add new row Accounting Journal
                app('App\Http\Controllers\AccountingJournalController')->pembalik_purchasing_book($newpb, 1);

                Session::flash('flash_message',trans('messages.successfully_edit_purchase_book_payment'));
                return redirect('cf-acc/purchasing-book/edit/'.$id);
            }
        } else {
            Session::flash('flash_message',trans('messages.nothing_change'));
            return redirect('cf-acc/purchasing-book/edit/'.$id);
        }

    }

    public function update_purchasing_book_payment(Request $request) {
        $oldpy = PurchasingBookPayment::findOrFail($request->py_id);
        $selisi = $oldpy->amount_paid - $request->amount_paid;

        if (!empty($oldpy)) {
            // Set minus Accounting Journal
            app('App\Http\Controllers\AccountingJournalController')->pembalik_purchasing_book_payment($oldpy, 0);

            $newpy = PurchasingBookPayment::findOrFail($request->py_id);
            $newpy->payment_date = $request->payment_date;
            $newpy->amount_paid = $request->amount_paid;
            if ($newpy->save()) {
                $pb = PurchasingBook::findOrFail($newpy->purchasing_book_id);
                if (!empty($pb)) {
                    $pb->amount_paid = $pb->amount_paid - $selisi;
                    $pb->rest_of_bill = ($pb->rest_of_bill + $selisi > 0) ? $pb->rest_of_bill + $selisi : 0;
                    if ($pb->rest_of_bill == 0){
                        $pb->payment_status = 'Paid';
                    } elseif ($pb->rest_of_bill == $pb->amount_due){
                        $pb->payment_status = 'Unpaid';
                    } else {
                        $pb->payment_status = 'Halfpaid';
                    }
                    $pb->save();
                }

                // Add new row Accounting Journal
                app('App\Http\Controllers\AccountingJournalController')->pembalik_purchasing_book_payment($newpy, 1);

                Session::flash('flash_message', trans('messages.successfully_edit_purchase_book_payment'));
                return redirect('cf-acc/purchasing-book/edit/'.$pb->id);
            }
        } else {
            Session::flash('flash_message', trans('messages.nothing_change'));
            return redirect('cf-acc/purchasing-book/edit/'.$pb->id);
        }
    }
}
