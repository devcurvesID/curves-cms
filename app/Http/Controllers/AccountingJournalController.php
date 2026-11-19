<?php

namespace App\Http\Controllers;

use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\AmortizationDetail;
use App\Model\Accounting\MemorialJournal;
use App\Model\Accounting\MonthlyEndingBalance;
use App\Model\Accounting\PurchasingBook;
use App\Model\Accounting\PurchasingBookPayment;
use App\Model\Club\ClubInfo;
use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\GuestMembership\GuestMembership;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\MembershipType;
use App\Model\Member\MemberDeposit;
use App\Model\Payment\MemberPayment;
use App\Model\Locker\LockerPayment;
use Illuminate\Http\Request;
use App\AutoNumber;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Accounting\AccountingJournal;
use App\Model\Accounting\TempJournal;
use App\Model\MasterData\Bank;
use App\Services\ApiClientService;

class AccountingJournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    // Accounting Journal saat Membayar pembayaran pertama
    public function store_membership_payment_accounting($member_payment ,Request $request){
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        // Tambahkan ke bagian debet
    	if($request->paymenttype == 1 || $request->paymenttype == 6 || $request->paymenttype == 7){
    		$coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
    	}else{
            $bank = Bank::findOrFail($request->bank);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
    	}

        $payment_date = new DateTime;

        if(!empty($coa)){
            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $payment_date = $request->pay_date;
            endif;

            $acc_journal = new AccountingJournal;
            $acc_journal->chart_of_acc_id = $coa->id;
            $acc_journal->trans_date = $payment_date;
            $acc_journal->debit = $request->sf_amount + $request->mf_amount - $request->dc_amount;
            $acc_journal->information = 'Terima Kas '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Tambahkan ke bagian kredit
        $coa_kredit = ChartOfAccount::where('coa_number','=','1-1260')->get();
        if(!empty($coa_kredit)){
            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $payment_date = $request->pay_date;
            endif;

            $acc_journal = new AccountingJournal;
            $acc_journal->chart_of_acc_id = $coa_kredit->id;
            $acc_journal->trans_date = $payment_date;
            $acc_journal->debit = $request->sf_amount + $request->mf_amount - $request->dc_amount;
            $acc_journal->information = 'Terima Kas '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }
    }

    //Accountong Journal untuk pembayaran Cicilan
    public function store_payment_debt_accounting($member_debt_payment, Request $request){
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();
            // Tambahkan ke bagian debet
        if($request->paymenttype == 1 || $request->paymenttype == 6 || $request->paymenttype == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($request->bank);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        $payment_date = new DateTime;

        if(!empty($coa)){
            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $payment_date = $request->pay_date;
            endif;

            $acc_journal = new AccountingJournal;
            $acc_journal->chart_of_acc_id = $coa->id;
            $acc_journal->trans_date = $payment_date;
            $acc_journal->debit = $request->sf_amount + $request->mf_amount - $request->dc_amount;
            $acc_journal->information = 'Terima Kas '.$member_debt_payment->payment_number;
            $acc_journal->club_id = $member_debt_payment->memberdebtpayment_memberpayment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Tambahkan ke bagian kredit
        $coa_kredit = ChartOfAccount::where('coa_number','=','1-1260')->get();
        if(!empty($coa_kredit)){
            if (strtotime($request->pay_date) == strtotime(Carbon::today())):
                $payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else:
                $payment_date = $request->pay_date;
            endif;

            $acc_journal = new AccountingJournal;
            $acc_journal->chart_of_acc_id = $coa_kredit->id;
            $acc_journal->trans_date = $payment_date;
            $acc_journal->debit = $request->sf_amount + $request->mf_amount - $request->dc_amount;
            $acc_journal->information = 'Terima Kas '.$member_debt_payment->payment_number;
            $acc_journal->club_id = $member_debt_payment->memberdebtpayment_memberpayment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }
    }

    public function membership_journal($member_payment, $membership_type) {
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $payment = MemberPayment::findOrFail($member_payment->id);
        $membership = MembershipType::findOrFail($membership_type->id);
        $amortisasi = $payment->amount_paid;

        //JURNAL AKUNTANSI
        //Jurnal Kas
        if($payment->payment_method_id == 1 || $payment->payment_method_id == 6 || $payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        // Cek apakah member payment memiliki digital product
        $has_digital_product = $member_payment->memberpayment_digitalproductpayment()->exists();
        if($has_digital_product) $this->add_digital_product($payment);

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->debit = $has_digital_product ? $payment->amount_paid - $payment->memberpayment_digitalproductpayment->price : $payment->amount_paid;
            $acc_journal->information = 'Terima Penjualan Jasa '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_muka)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->credit = $has_digital_product ? $payment->amount_paid - $payment->memberpayment_digitalproductpayment->price : $payment->amount_paid;
            $acc_journal->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //JURNAL MEMORIAL
        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        //Jurnal Pendapatan diterima di muka
        if(!empty($coa_muka)){
            $acc_journal_d = new MemorialJournal;
            $acc_journal_d->coa_id = $coa_muka->id;
            $acc_journal_d->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_d->debit = $payment->amount_paid;
            $acc_journal_d->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal_d->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_d->journal_number = $mnum;
            $acc_journal_d->save();
        }

        //Jurnal Penjualan Jasa
        $coa_jasa = ChartOfAccount::where('coa_number','=','4-1000')->first();

        if(!empty($coa_jasa)){
            //Biaya Layanan
            $acc_journal_sf = new MemorialJournal;
            $acc_journal_sf->coa_id = $coa_jasa->id;
            $acc_journal_sf->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_sf->credit = $payment->sf_amount - $payment->dc_amount;
            $amortisasi -= $payment->sf_amount - $payment->dc_amount;
            $acc_journal_sf->information = 'Biaya Layanan '.$payment->payment_number;
            $acc_journal_sf->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_sf->journal_number = $mnum;
            $acc_journal_sf->save();

            //Iuran Bulanan
            $acc_journal_mf = new MemorialJournal;
            $acc_journal_mf->coa_id = $coa_jasa->id;
            $acc_journal_mf->trans_date = Carbon::parse($payment->payment_date);

            if ($membership_type->payment_type == 'Prepaid'):
                $acc_journal_mf->credit = round($payment->mf_amount / $membership->duration);
                $amortisasi -= round($payment->mf_amount / $membership->duration);
            else:
                $acc_journal_mf->credit = $payment->mf_amount;
                $amortisasi -= $payment->mf_amount;
            endif;

            $acc_journal_mf->information = 'Iuran Bulanan '.$payment->payment_number;
            $acc_journal_mf->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_mf->journal_number = $mnum;
            $acc_journal_mf->save();

            //Amortisasi Jasa
            $acc_journal_amor = new MemorialJournal;
            $acc_journal_amor->coa_id = $coa_jasa->id;
            $acc_journal_amor->trans_date = Carbon::parse($payment->payment_date);

            if ($amortisasi > 0):
                $acc_journal_amor->credit = $amortisasi;
            else:
                $acc_journal_amor->debit = $amortisasi;
            endif;

            $acc_journal_amor->information = 'Amortisasi Jasa '.$payment->payment_number;
            $acc_journal_amor->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_amor->journal_number = $mnum;
            $acc_journal_amor->save();
        }
    }

    private function add_digital_product($member_payment, $approved = false){
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Cari COA ID berdasarkan metode pembayaran
        if($member_payment->payment_method_id == 1 || $member_payment->payment_method_id == 6 || $member_payment->payment_method_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else :
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where('id', '=', $member_payment->memberpayment_bank->coa_id)
                ->first();
        endif;

        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->debit = $member_payment->memberpayment_digitalproductpayment->price;
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Terima penjualan digital product ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->user_club->id;
        $temp_journal->save();

        // (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-4000')
                    ->orWhere('coa_name', 'ilike', '%penjualan produk%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa_penjualan->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->credit = $member_payment->memberpayment_digitalproductpayment->price;
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Penjualan digital product ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->user_club->id;
        $temp_journal->save();

    }

    //Set minus accounting journal untuk membership
    public function edit_membership_journal($member_payment, $membership_type) {
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $payment = MemberPayment::findOrFail($member_payment->id);
        $membership = MembershipType::findOrFail($membership_type);
        $amortisasi = $payment->amount_paid;

        //JURNAL AKUNTANSI
        //Jurnal Kas
        if($payment->payment_method_id == 1 || $payment->payment_method_id == 6 || $payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->debit = -($payment->amount_paid);
            $acc_journal->information = 'Terima Penjualan Jasa '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_muka)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->credit = -($payment->amount_paid);
            $acc_journal->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }
    }

    //Set new accounting journal untuk membership
    public function new_membership_journal($member_payment, $membership_type) {
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $payment = MemberPayment::findOrFail($member_payment->id);
        $membership = MembershipType::findOrFail($membership_type);
        $amortisasi = $payment->amount_paid;

        //JURNAL AKUNTANSI
        //Jurnal Kas
        if($payment->payment_method_id == 1 || $payment->payment_method_id == 6 || $payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->debit = $payment->amount_paid;
            $acc_journal->information = 'Terima Penjualan Jasa '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_muka)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->credit = $payment->amount_paid;
            $acc_journal->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }
    }

    public function locker_payment_journal($locker_payment){
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        //Jurnal Kas
        if($locker_payment->paymentmethod_id == 1 || $locker_payment->paymentmethod_id == 6 || $locker_payment->paymentmethod_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($locker_payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }
        $default_coa = ChartOfAccount::where('coa_number','=','0-0000')->first();

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $acc_journal->debit = $locker_payment->price;
            $acc_journal->information = 'Terima Penjualan Sewa Loker '.$locker_payment->payment_number;
            $acc_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }else{
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $default_coa->id;
            $acc_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $acc_journal->debit = $locker_payment->price;
            $acc_journal->information = 'Terima Penjualan Sewa Loker '.$locker_payment->payment_number;
            $acc_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Pendapatan Jasa Diterima di Muka
        $coa_pdm = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_pdm)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_pdm->id;
            $acc_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $acc_journal->credit = $locker_payment->price;
            $acc_journal->information = 'Pendapatan diterima di muka Sewa Loker '.$locker_payment->payment_number;
            $acc_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }else{
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $default_coa->id;
            $acc_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $acc_journal->credit = $locker_payment->price;
            $acc_journal->information = 'Pendapatan diterima di muka Sewa Loker '.$locker_payment->payment_number;
            $acc_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Jurnal Memorial Pendapatan diterima di muka
        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        if(!empty($coa_pdm)){
            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $coa_pdm->id;
            $mem_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $mem_journal->debit = $locker_payment->price;
            $mem_journal->information = 'Pendapatan diterima di muka Sewa Loker '.$locker_payment->payment_number;
            $mem_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }else{
            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $default_coa->id;
            $mem_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $mem_journal->debit = $locker_payment->price;
            $mem_journal->information = 'Pendapatan diterima di muka Sewa Loker '.$locker_payment->payment_number;
            $mem_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }

        //Jurnal Memorial Penjualan Loker
        $coa_sewa_loker = ChartOfAccount::where('coa_number','=','4-5000')->first();

        if(!empty($coa_sewa_loker)){
            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $coa_sewa_loker->id;
            $mem_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $mem_journal->credit = $locker_payment->price;
            $mem_journal->information = 'Penjualan Sewa Loker '.$locker_payment->payment_number;
            $mem_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }else{
            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $default_coa->id;
            $mem_journal->trans_date = Carbon::parse($locker_payment->payment_date);
            $mem_journal->credit = $locker_payment->price;
            $mem_journal->information = 'Penjualan Sewa Loker '.$locker_payment->payment_number;
            $mem_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }
    }

	public function product_payment($product_payment){
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        //Jurnal Akuntansi
        //Jurnal Kas
        if($product_payment->payment_method_id == 1 || $product_payment->payment_method_id == 6 || $product_payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($product_payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }
        $default_coa = ChartOfAccount::where('coa_number', '=', '0-0000')->first();

        if ($product_payment->is_reward) {
            if(!empty($coa)):
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->debit = $product_payment->total;
                $acc_journal->information = 'Terima penjualan product '.$product_payment->payment_number.' (Hadiah)';
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            else:
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $default_coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->debit = $product_payment->total;
                $acc_journal->information = 'Terima penjualan product '.$product_payment->payment_number.' (Hadiah)';
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            endif;

            //Pendapatan diterima di muka
            $coa_ddm = ChartOfAccount::where('coa_number','=','1-1260')->first();

            if (!empty($coa_ddm)):
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_ddm->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->credit = $product_payment->total;
                $acc_journal->information = 'Pendapatan diterima di muka '.$product_payment->payment_number.' (Hadiah)';
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            else:
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $default_coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->credit = $product_payment->total;
                $acc_journal->information = 'Pendapatan diterima di muka '.$product_payment->payment_number.' (Hadiah)';
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            endif;

            //Jurnal Memorial
            // Memorial Auto Number Added 19112019
            $memorial_number = AutoNumber::where('name','=','Memorial')->first();
            $mnum = $memorial_number->current_number;
            $memorial_number->current_number += 1;
            $memorial_number->save();

            if(!empty($coa_ddm)):
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $coa_ddm->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->debit = $product_payment->total;
                $acc_memorial->information = 'Pendapatan diterima di muka '.$product_payment->payment_number.' (Hadiah)';
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            else:
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $default_coa->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->debit = $product_payment->total;
                $acc_memorial->information = 'Pendapatan diterima di muka '.$product_payment->payment_number.' (Hadiah)';
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            endif;

            //Jurnal penjualan product
            $coa_penjualan = ChartOfAccount::where('coa_number','=','4-4000')->first();

            if (!empty($coa_penjualan)):
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $coa_penjualan->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->credit = $product_payment->total;
                $acc_memorial->information = 'Penjualan product '.$product_payment->payment_number.' (Hadiah)';
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            else:
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $default_coa->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->credit = $product_payment->total;
                $acc_memorial->information = 'Penjualan product '.$product_payment->payment_number.' (Hadiah)';
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            endif;
        } else {
            if(!empty($coa)):
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->debit = $product_payment->total;
                $acc_journal->information = 'Terima penjualan product '.$product_payment->payment_number;
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            else:
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $default_coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->debit = $product_payment->total;
                $acc_journal->information = 'Terima penjualan product '.$product_payment->payment_number;
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            endif;

            //Pendapatan diterima di muka
            $coa_ddm = ChartOfAccount::where('coa_number','=','1-1260')->first();

            if (!empty($coa_ddm)):
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_ddm->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->credit = $product_payment->total;
                $acc_journal->information = 'Pendapatan diterima di muka '.$product_payment->payment_number;
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            else:
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $default_coa->id;
                $acc_journal->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_journal->credit = $product_payment->total;
                $acc_journal->information = 'Pendapatan diterima di muka '.$product_payment->payment_number;
                $acc_journal->club_id = $product_payment->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            endif;

            //Jurnal Memorial
            // Memorial Auto Number Added 19112019
            $memorial_number = AutoNumber::where('name','=','Memorial')->first();
            $mnum = $memorial_number->current_number;
            $memorial_number->current_number += 1;
            $memorial_number->save();

            if(!empty($coa_ddm)):
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $coa_ddm->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->debit = $product_payment->total;
                $acc_memorial->information = 'Pendapatan diterima di muka '.$product_payment->payment_number;
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            else:
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $default_coa->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->debit = $product_payment->total;
                $acc_memorial->information = 'Pendapatan diterima di muka '.$product_payment->payment_number;
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            endif;

            //Jurnal penjualan product
            $coa_penjualan = ChartOfAccount::where('coa_number','=','4-4000')->first();

            if (!empty($coa_penjualan)):
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $coa_penjualan->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->credit = $product_payment->total;
                $acc_memorial->information = 'Penjualan product '.$product_payment->payment_number;
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            else:
                $acc_memorial = new MemorialJournal;
                $acc_memorial->coa_id = $default_coa->id;
                $acc_memorial->trans_date = Carbon::parse($product_payment->payment_date);
                $acc_memorial->credit = $product_payment->total;
                $acc_memorial->information = 'Penjualan product '.$product_payment->payment_number;
                $acc_memorial->club_id = $product_payment->club_id;
                $acc_memorial->journal_number = $mnum;
                $acc_memorial->save();
            endif;
        }

        // Jonviter DEV
        $product_payment->payment_date = $product_payment->payment_date->toDateTimeString(); 
        $paymentData = $product_payment->toArray();
        $apiClient = new ApiClientService();
        $mobile_curves_api = $apiClient->post("/mobile-curves/payment-product/{$product_payment->member_id}", $paymentData);
        // Jonviter DEV


        //Cek product keyTag dan air minum
        //Jurnal penjualan lainnya(key tag dan air mineral)
//        $coa_penjualan_lainnya = ChartOfAccount::where('coa_number', '=', '4-6000')->first();
//        foreach ($detail as $item):
//            if ($item->productpaymentdetail_mst_product->name == ''):
//                $acc_journal = new AccountingJournal;
//                $acc_journal->coa_id = $coa_penjualan_lainnya->id;
//                $acc_journal->trans_date = Carbon::parse($product->payment_date);
//                $acc_journal->credit = $product->total;
//                $acc_journal->information = 'Penjualan lainnya '.$product->payment_number;
//                $acc_journal->club_id = $product->club_id;
//                $acc_journal->save();
//            endif;
//        endforeach;
    }

    public function membership_renewal_journal($member_payment, $membership_type) {
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $payment = MemberPayment::findOrFail($member_payment->id);
        $membership = MembershipType::findOrFail($membership_type->id);
        $amortisasi = $payment->amount_paid;

        //Jurnal Kas
        if($payment->payment_method_id == 1 || $payment->payment_method_id == 6 || $payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->debit = $payment->amount_paid;
            $acc_journal->information = 'Terima Penjualan Jasa '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_muka)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->credit = $payment->amount_paid;
            $acc_journal->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal->club_id = $payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();

            $acc_journal_d = new MemorialJournal;
            $acc_journal_d->coa_id = $coa_muka->id;
            $acc_journal_d->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_d->debit = $payment->amount_paid;
            $acc_journal_d->information = 'Pendapatan diterima di muka '.$payment->payment_number;
            $acc_journal_d->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_d->journal_number = $mnum;
            $acc_journal_d->save();
        }

        //Jurnal Penjualan Jasa
        $coa_jasa = ChartOfAccount::where('coa_number','=','4-1000')->first();

        if(!empty($coa_jasa)){
            //Biaya Layanan
            $acc_journal_sf = new MemorialJournal;
            $acc_journal_sf->coa_id = $coa_jasa->id;
            $acc_journal_sf->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_sf->credit = $payment->sf_amount - $payment->dc_amount;
            $amortisasi -= $payment->sf_amount - $payment->dc_amount;
            $acc_journal_sf->information = 'Biaya Layanan '.$payment->payment_number;
            $acc_journal_sf->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_sf->journal_number = $mnum;
            $acc_journal_sf->save();

            //Iuran Bulanan
            $acc_journal_mf = new MemorialJournal;
            $acc_journal_mf->coa_id = $coa_jasa->id;
            $acc_journal_mf->trans_date = Carbon::parse($payment->payment_date);

            if ($membership_type->payment_type == 'Prepaid'):
                $acc_journal_mf->credit = round($payment->mf_amount / $membership->duration);
                $amortisasi -= round($payment->mf_amount / $membership->duration);
            else:
                $acc_journal_mf->credit = $payment->mf_amount;
                $amortisasi -= $payment->mf_amount;
            endif;

            $acc_journal_mf->information = 'Iuran Bulanan '.$payment->payment_number;
            $acc_journal_mf->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_mf->journal_number = $mnum;
            $acc_journal_mf->save();

            //Amortisasi Jasa
            $acc_journal_amor = new MemorialJournal;
            $acc_journal_amor->coa_id = $coa_jasa->id;
            $acc_journal_amor->trans_date = Carbon::parse($payment->payment_date);

            if ($amortisasi > 0):
                $acc_journal_amor->credit = $amortisasi;
            else:
                $acc_journal_amor->debit = $amortisasi;
            endif;

            $acc_journal_amor->information = 'Amortisasi Jasa '.$payment->payment_number;
            $acc_journal_amor->club_id = $payment->memberpayment_user->club_id;
            $acc_journal_amor->journal_number = $mnum;
            $acc_journal_amor->save();
        }
    }

    public function installment_payment($member_payment){
        //Journal Number Added 15/03/2019
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        //Jurnal Kas
        if($member_payment->payment_method_id == 1 || $member_payment->payment_method_id == 6 || $member_payment->payment_method_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($member_payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }
        $default_coa = ChartOfAccount::where('coa_number', '=', '0-0000')->first();

        if(!empty($coa)):
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_journal->debit = $member_payment->amount_due;
            $acc_journal->information = 'Terima cicilan '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        else:
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $default_coa->id;
            $acc_journal->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_journal->debit = $member_payment->amount_due;
            $acc_journal->information = 'Terima cicilan '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        endif;

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if (!empty($coa_muka)):
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_journal->credit = $member_payment->amount_due;
            $acc_journal->information = 'Pendapatan diterima di muka '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        else:
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $default_coa->id;
            $acc_journal->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_journal->credit = $member_payment->amount_due;
            $acc_journal->information = 'Pendapatan diterima di muka '.$member_payment->payment_number;
            $acc_journal->club_id = $member_payment->memberpayment_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        endif;

        //Jurnal Memorial
        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        if(!empty($coa_muka)):
            $acc_memorial = new MemorialJournal;
            $acc_memorial->coa_id = $coa_muka->id;
            $acc_memorial->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_memorial->debit = $member_payment->amount_due;
            $acc_memorial->information = 'Pendapatan diterima di muka '.$member_payment->payment_number;
            $acc_memorial->club_id = $member_payment->memberpayment_user->club_id;
            $acc_memorial->journal_number = $mnum;
            $acc_memorial->save();
        else:
            $acc_memorial = new MemorialJournal;
            $acc_memorial->coa_id = $default_coa->id;
            $acc_memorial->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_memorial->debit = $member_payment->amount_due;
            $acc_memorial->information = 'Pendapatan diterima di muka '.$member_payment->payment_number;
            $acc_memorial->club_id = $member_payment->memberpayment_user->club_id;
            $acc_memorial->journal_number = $mnum;
            $acc_memorial->save();
        endif;

        //Jurnal jasa
        $coa_jasa = ChartOfAccount::where('coa_number','=','4-1000')->first();

        if (!empty($coa_jasa)):
            $acc_memorial = new MemorialJournal;
            $acc_memorial->coa_id = $coa_jasa->id;
            $acc_memorial->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_memorial->credit = $member_payment->amount_due;
            $acc_memorial->information = 'Amortisasi jasa '.$member_payment->payment_number;
            $acc_memorial->club_id = $member_payment->memberpayment_user->club_id;
            $acc_memorial->journal_number = $mnum;
            $acc_memorial->save();
        else:
            $acc_memorial = new MemorialJournal;
            $acc_memorial->coa_id = $default_coa->id;
            $acc_memorial->trans_date = Carbon::parse($member_payment->payment_date);
            $acc_memorial->credit = $member_payment->amount_due;
            $acc_memorial->information = 'Amortisasi jasa '.$member_payment->payment_number;
            $acc_memorial->club_id = $member_payment->memberpayment_user->club_id;
            $acc_memorial->journal_number = $mnum;
            $acc_memorial->save();
        endif;
    }

    public function ledger(Request $request) {
        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');

        if (!empty($request->from_date)):
            $from_date = Carbon::parse($request->from_date);
        endif;

        if (!empty($request->thru_date)):
            $thru_date = Carbon::parse($request->thru_date);
        endif;

        $clubs = ClubInfo::whereHas('club_accounting', function ($q) {
            $q->where('accounting_id', '=', Session::get('account')->id);
        })->orderBy('club_name', 'asc')
//            ->pluck('club_name', 'id');
            ->get();

        // COA Added 28082019
        $coas = ChartOfAccount::select('id', 'coa_number', 'coa_name')
            ->orderBy('coa_number', 'asc')
            ->get();

        if (!empty($request->clubs)):
            $chosen_club = $request->clubs;
            if (count($chosen_club) == 1):
                $chosen_club = $chosen_club[0];
            endif;
        else:
            $chosen_club = null;
            foreach ($clubs as $key => $value):
                if (($chosen_club == null) || ($chosen_club > $key)):
                    $chosen_club = $key;
                endif;
            endforeach;
        endif;

        // COA Added 28082019
        if (!empty($request->coas)):
            $chosen_coa = $request->coas;
            if (count($chosen_coa) == 1):
                $chosen_coa = $chosen_coa[0];
            endif;
        else:
            $chosen_coa = null;
//            foreach ($coas as $key => $value):
//                if (($chosen_coa == null) || ($chosen_coa > $key)):
//                    $chosen_coa = $key;
//                endif;
//            endforeach;
        endif;

        $ledgers = AccountingJournal::whereDate('trans_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('trans_date', '<=', Carbon::parse($thru_date)->toDateString());

        if (!empty($request->clubs)):
            $counter = 0;
            foreach ($request->clubs as $club):
                if ($counter == 0):
                    $ledgers->where('club_id', '=', $club);
                else:
                    $ledgers->orWhere('club_id', '=', $club);
                endif;
                $counter++;
            endforeach;
        else:
            $ledgers->where('club_id', '=', $chosen_club);
        endif;

        // COA Added 28082019
        if (!empty($request->coas)):
            $ledgers->where(function ($c) use ($request) {
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

        $journals = $ledgers->orderBy('coa_id', 'asc')->orderBy('trans_date', 'asc')->get()->groupBy('coa_id');
//        $ledger_groups = $journals->groupBy('coa_id');

//        return $journals;
        return view('cf_acc.main.ledger', compact('from_date', 'thru_date', 'clubs', 'chosen_club', 'journals', 'coas', 'chosen_coa'));
    }

    public function ledger_v2(Request $request) {
        // INITIALIZATION
        $user = AccountingAccount::findOrFail(Session::get('account')->id);

        $jubilants = Jubilant::whereHas('jubilant_accounting', function ($r) use ($user) {
            $r->where('accounting_id', '=', $user->id);
        })
            ->orderBy('jubilant_name', 'asc')
            ->pluck('jubilant_name', 'id');

        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) use ($user) {
            $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                $r->where('accounting_id', '=', $user->id);
            });
        })->orderBy('club_name', 'asc')->get();

        $coas = ChartOfAccount::select('id', 'coa_number', 'coa_name', 'jubilant_id')
            ->whereHas('coa_jubilant', function ($jubilant) use ($user) {
                $jubilant->whereHas('jubilant_accounting', function ($acc) use ($user) {
                    $acc->where('accounting_id', '=', $user->id);
                });
            })
            ->orderBy('coa_number', 'asc')
            ->get();

        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');

        if(!empty($request->month)):
            $from_date = Carbon::parse('01-'.$request->month);
            $thru_date = Carbon::parse($from_date)->lastOfMonth();
        endif;


        // SET Chosen CLUB & COA
        // Club
        if (!empty($request->clubs)):
            $chosen_club = $request->clubs;
            if (count($chosen_club) == 1):
                $chosen_club = $chosen_club[0];
            endif;
        else:
            $chosen_club = null;
            foreach ($clubs as $key => $value):
                if (($chosen_club == null) || ($chosen_club > $key)):
                    $chosen_club = $key;
                endif;
            endforeach;
        endif;

        // COA
        if (!empty($request->coas)):
            $chosen_coa = $request->coas;
            if (count($chosen_coa) == 1):
                $chosen_coa = $chosen_coa[0];
            endif;
        else:
            $chosen_coa = null;
        endif;

        // THE JOURNALS
        $ledgers = AccountingJournalV2::whereDate('trans_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('trans_date', '<=', Carbon::parse($thru_date)->toDateString());

        if (!empty($request->jubilant)):
            $ledgers->where('jubilant_id', '=', $request->jubilant);
        else:
            $counter = 0;
            foreach ($user->accounting_jubilant as $jubilant):
                if ($counter == 0):
                    $ledgers->where('jubilant_id', '=', $jubilant->id);
                else:
                    $ledgers->orWhere('jubilant_id', '=', $jubilant->id);
                endif;
                $counter++;
            endforeach;
        endif;

        if (!empty($request->clubs)):
            $counter = 0;
            foreach ($request->clubs as $club):
                if ($counter == 0):
                    $ledgers->where('club_id', '=', $club);
                else:
                    $ledgers->orWhere('club_id', '=', $club);
                endif;
                $counter++;
            endforeach;
        else:
            $ledgers->where('club_id', '=', $chosen_club);
        endif;

        if (!empty($request->coas)):
            $ledgers->where(function ($c) use ($request) {
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

        $journals = $ledgers->orderBy('coa_id', 'asc')
            ->orderBy('trans_date', 'asc')
            ->get()
            ->groupBy('coa_id');


        // BEGINNING BALANCE
        $bb_thru_date = Carbon::parse($from_date)->subDay();
        $bb_from_date = Carbon::parse($bb_thru_date)->firstOfMonth();

        $beginning_balances = MonthlyEndingBalance::whereDate('recap_date', '>=', Carbon::parse($bb_from_date)->toDateString())
            ->whereDate('recap_date', '<=', Carbon::parse($bb_thru_date)->toDateString())
            ->get();
        //dd($chosen_club);
        return view('cf_acc.main.ledger-v2', compact('from_date', 'thru_date', 'jubilants', 'clubs', 'chosen_club', 'journals', 'coas', 'chosen_coa', 'beginning_balances'));
    }

    public function general_ledger(Request $request){
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');
        $coas = ChartOfAccount::orderBy('coa_number', 'asc')->get();

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }
        $accjournals = AccountingJournal::where('trans_date','>=',$fromdate)->where('trans_date','<=',$thrudate);

        if(!empty($request->clubs)){

            $clubstring = '(';
            foreach ($request->clubs as $key => $value) {
                $clubstring .= $value;
                if($key < count($request->clubs)-1){
                    $clubstring .= ', ';
                }
            }
            $clubstring .= ')';

            $accjournals->whereRaw('club_id IN '.$clubstring);
        }else{
            $accjournals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
        }

        // COA Added 29082019
        if (!empty($request->coas)):
            $accjournals->where(function ($c) use ($request) {
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

        $accjournals = $accjournals->get();

        return view('cf_acc.main.general-ledger',compact('accjournals','clubs','fromdate','thrudate','coas'));
    }

    public function general_ledger_v2(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);

        $jubilants = Jubilant::whereHas('jubilant_accounting', function ($r) use ($user) {
            $r->where('accounting_id', '=', $user->id);
        })
            ->orderBy('jubilant_name', 'asc')
            ->pluck('jubilant_name', 'id');

        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) use ($user) {
            $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                $r->where('accounting_id', '=', $user->id);
            });
        })->orderBy('club_name', 'asc')->get();

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        $accjournals = AccountingJournalV2::whereDate('trans_date', '>=', $fromdate)
            ->whereDate('trans_date', '<=', $thrudate)->whereHas('accjournalv2_jubilant', function ($q) use ($user) {
                $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                    $r->where('accounting_id', '=', $user->id);
                });
            })
            ->where('information', 'not ilike', '%amortisasi%'); // Updated 03032020

        if (!empty($request->jubilant)) {
            $accjournals->where('jubilant_id', '=', $request->jubilant);
            if(!empty($request->clubs)){
                $accjournals->where('club_id', '=', $request->clubs);
            }
        }

        $accjournals = $accjournals->get();

        return view('cf_acc.main.general-ledger-v2', compact('fromdate', 'thrudate', 'jubilants', 'clubs', 'accjournals'));
    }

    public function memorial_journal(Request $request){
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');

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
        $memjournals = MemorialJournal::where('trans_date','>=',$fromdate)->where('trans_date','<=',$thrudate);

        if(!empty($request->clubs)){

            $clubstring = '(';
            foreach ($request->clubs as $key => $value) {
                $clubstring .= $value;
                if($key < count($request->clubs)-1){
                    $clubstring .= ', ';
                }
            }
            $clubstring .= ')';

            $memjournals->whereRaw('club_id IN '.$clubstring);
        }else{
            $memjournals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
        }

        // COA Added 28082019
        if (!empty($request->coas)):
            $memjournals->where(function ($c) use ($request) {
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

        $memjournals = $memjournals->get();

        return view('cf_acc.main.memorial-journal',compact('memjournals','clubs','fromdate','thrudate', 'coas'));
    }

    public function memorial_journal_v2(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);

        $jubilants = Jubilant::whereHas('jubilant_accounting', function ($r) use ($user) {
            $r->where('accounting_id', '=', $user->id);
        })
            ->orderBy('jubilant_name', 'asc')
            ->pluck('jubilant_name', 'id');

        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) use ($user) {
            $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                $r->where('accounting_id', '=', $user->id);
            });
        })->orderBy('club_name', 'asc')->get();

        $coas = ChartOfAccount::select('id', 'coa_number', 'coa_name', 'jubilant_id')
            ->whereHas('coa_jubilant', function ($jubilant) use ($user) {
                $jubilant->whereHas('jubilant_accounting', function ($acc) use ($user) {
                    $acc->where('accounting_id', '=', $user->id);
                });
            })
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

        // Memorial Journal
//        $memorials = MemberPayment::selectRaw('
//            SUM(member_payments.amount_paid) as payment,
//            SUM(amortization_details.amount_paid) as dues
//        ')
////            ->whereDate('member_payments.payment_date', '>=', Carbon::parse($fromdate)->toDateString())
//            ->whereDate('member_payments.payment_date', '<=', Carbon::parse($thrudate)->toDateString())
//            ->join('amortization_headers', function ($amor_head) use ($thrudate) {
//                $amor_head->on('amortization_headers.user_id', '=', 'member_payments.user_id');
//                $amor_head->whereDate('amortization_headers.contract_start', '<=', Carbon::parse($thrudate)->toDateString());
//            })
//            ->join('amortization_details', function ($amor_detail) use ($fromdate, $thrudate) {
//                $amor_detail->on('amortization_details.amortization_header_id', '=', 'amortization_headers.id');
//                $amor_detail->where('amortization_details.status', '=', 'Published')
//                    ->whereDate('amortization_date', '>=', Carbon::parse($fromdate)->toDateString())
//                    ->whereDate('amortization_date', '<=', Carbon::parse($thrudate)->toDateString());
//            })
//            ->get();

        $payments = MemberPayment::selectRaw('SUM(member_payments.amount_paid) as payment')
            ->whereDate('member_payments.payment_date', '<=', Carbon::parse($thrudate)->toDateString())
            ->join('amortization_headers', function ($amor_head) use ($thrudate) {
                $amor_head->on('amortization_headers.user_id', '=', 'member_payments.user_id');
                $amor_head->whereDate('amortization_headers.contract_start', '<=', Carbon::parse($thrudate)->toDateString());
            });

        $amortizations = AmortizationDetail::selectRaw('SUM(amount_paid) as amortization')
            ->whereDate('amortization_date', '<=', Carbon::parse($thrudate)->toDateString());

        $dues = AmortizationDetail::selectRaw('SUM(amount_paid) as dues')
            ->where('status', '=', 'Published')
            ->whereDate('amortization_date', '>=', Carbon::parse($fromdate)->toDateString())
            ->whereDate('amortization_date', '<=', Carbon::parse($thrudate)->toDateString());

        if (!empty($request->jubilant)):
            // Payment
            $payments->whereHas('memberpayment_user', function ($user) use ($request) {
                $user->whereHas('user_club', function ($club) use ($request) {
                    $club->where('jubilant_id', '=', $request->jubilant);
                });

                if (!empty($request->club)):
                    $user->where('club_id', '=', $request->club);
                endif;
            });

            // Amortization
            $amortizations->whereHas('amordetail_amorheader', function ($header) use ($request) {
                $header->whereHas('amorheader_user', function ($user) use ($request) {
                    $user->whereHas('user_club', function ($club) use ($request) {
                        $club->where('jubilant_id', '=', $request->jubilant);
                    });

                    if (!empty($request->club)):
                        $user->where('club_id', '=', $request->club);
                    endif;
                });
            });

            // Dues
            $dues->whereHas('amordetail_amorheader', function ($header) use ($request) {
                $header->whereHas('amorheader_user', function ($user) use ($request) {
                    $user->whereHas('user_club', function ($club) use ($request) {
                        $club->where('jubilant_id', '=', $request->jubilant);
                    });

                    if (!empty($request->club)):
                        $user->where('club_id', '=', $request->club);
                    endif;
                });
            });
        endif;

        // Assemble
        $payments = $payments->get();
        $amortizations = $amortizations->get();
        $dues = $dues->get();

//        return $payments[0]->payment.' - '.$amortizations[0]->amortization.' - '.$dues[0]->dues;
        return view('cf_acc.main.memorial-journal-v2',compact(
            'jubilants','clubs','fromdate','thrudate', 'coas', 'payments', 'amortizations', 'dues'
        ));
    }

    public function cash_flow(Request $request){
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');

        // COA Added 28082019
        $coas = ChartOfAccount::select('id', 'coa_number', 'coa_name')
            ->where('coa_number', '<>', '1-1100')
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

        $accjournals = AccountingJournal::whereHas('accountingjournal_chartofaccount',function($q){
            $q->where('coa_number','<>','1-1100');
        })->whereRaw('journal_number IN (SELECT journal_number from accounting_journals JOIN chart_of_accounts ON accounting_journals.coa_id = chart_of_accounts.id WHERE coa_number = \'1-1100\')')
            ->where('trans_date','>=',$fromdate)
            ->where('trans_date','<=',$thrudate);

        if(!empty($request->clubs)){

            $clubstring = '(';
            foreach ($request->clubs as $key => $value) {
                $clubstring .= $value;
                if($key < count($request->clubs)-1){
                    $clubstring .= ', ';
                }
            }
            $clubstring .= ')';

            $accjournals->whereRaw('club_id IN '.$clubstring);
        }else{
            $accjournals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
        }

        // COA Added 28082019
        if (!empty($request->coas)):
            $accjournals->where(function ($c) use ($request) {
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

        $accjournals = $accjournals->get();

        return view('cf_acc.main.cash-flow',compact('accjournals','clubs','fromdate','thrudate', 'coas'));
    }

    //10 Jan 2020
    public function cash_flow_v2(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $jubilants = Jubilant::whereHas('jubilant_accounting')->orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');
        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) {
            $q->whereHas('jubilant_accounting');
        })->orderBy('club_name', 'asc')->get();
        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        $accJournals = AccountingJournalV2::whereRaw('pair IN (SELECT pair from accounting_journals_v2 JOIN chart_of_accounts ON accounting_journals_v2.coa_id = chart_of_accounts.id WHERE coa_number = \'1-1100\')')
            ->where('trans_date','>=',$fromdate)
            ->where('trans_date','<=',$thrudate)
            ->whereHas('accjournalv2_jubilant', function ($q) use ($user) {
                $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                    $r->where('accounting_id', '=', $user->id);
                });
            });

        if (!empty($request->jubilant)) {
            $accJournals->where('jubilant_id', '=', $request->jubilant);
            if(!empty($request->clubs)){
                $accJournals->where('club_id', '=', $request->clubs);
            }
        }

        $accJournals->orderBy('pair', 'asc');
        $accJournals->orderBy('id', 'asc');
        $accJournals = $accJournals->get();

        return view('cf_acc.main.cash-flow-v2', compact('accJournals','jubilants','clubs', 'fromdate', 'thrudate'));
    }

	public function trial_balance(Request $request) {
        if (!empty($request->tbs)):
            $tbs = json_decode($request->tbs);
            $from_date = Carbon::parse($request->from_date);
            $thru_date = Carbon::parse($request->thru_date);
            $chosen_club = json_decode($request->chosen_clubs);
            $chosen_coa = json_decode($request->chosen_coas);

            $clubs = ClubInfo::whereHas('club_accounting', function ($q) {
                $q->where('accounting_id', '=', Session::get('account')->id);
            })->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');

            if (!empty($chosen_coa)):
                $coa_count = 0;
                $coas = ChartOfAccount::where('id', '<>', null);
                if (count($chosen_coa) > 1):
                    foreach ($chosen_coa as $key => $value):
                        if ($coa_count == 0):
                            $coas->where('id', '=', $value);
                        else:
                            $coas->orWhere('id', '=', $value);
                        endif;
                        $coa_count++;
                    endforeach;
                else:
                    $coas->where('id', '=', $chosen_coa);
                endif;

                $coas = $coas->orderBy('coa_number', 'asc')->get();
            else:
                $coas = ChartOfAccount::orderBy('coa_number', 'asc')->get();
            endif;

            return view('cf_acc.main.trial-balance', compact('tbs', 'from_date', 'thru_date', 'chosen_club', 'clubs', 'coas', 'chosen_coa'));
        else:
            return redirect('cf-acc/ledger');
        endif;
    }

    public function bank_flow(Request $request){
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');
        $banks = Bank::whereHas('bank_club',function($q) use ($user){
            $q->whereHas('club_accounting',function($r) use ($user){
                $r->where('accounting_id','=',$user->id);
            });
        })->pluck('bank_name','id');

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

        $bankstring = '(';
        if(!empty($request->banks)){
            foreach ($request->banks as $key => $value) {
                $bankstring .= $value;
                if($key < count($request->banks)-1){
                    $bankstring .= ', ';
                }
            }
        }else{
            $listbanks = Bank::whereHas('bank_club',function($q) use($user){
                $q->whereHas('club_accounting',function($r) use($user){
                    $r->where('accounting_accounts.id','=',$user->id);
                });
            })->pluck('id');
            foreach ($listbanks as $key => $value) {
                $bankstring .= $value;
                if($key < count($listbanks)-1){
                    $bankstring .= ', ';
                }
            }
        }
        $bankstring .= ')';

        $accjournals = AccountingJournal::whereHas('accountingjournal_chartofaccount',function($q) use ($bankstring){
            // $q->whereHas('chartofaccount_bank',function($r) use ($bankstring){
            //     $r->whereRaw('mst_banks.id IN '.$bankstring);
            // });
            $q->whereRaw('chart_of_accounts.id NOT IN (SELECT coa_id from mst_banks WHERE mst_banks.id IN '.$bankstring.')');
        })->whereRaw('journal_number IN (SELECT journal_number from accounting_journals JOIN chart_of_accounts ON accounting_journals.coa_id = chart_of_accounts.id JOIN mst_banks ON chart_of_accounts.id = mst_banks.coa_id WHERE mst_banks.id IN '.$bankstring.')')->where('trans_date','>=',$fromdate)->where('trans_date','<=',$thrudate);

        if(!empty($request->clubs)){

            $clubstring = '(';
            foreach ($request->clubs as $key => $value) {
                $clubstring .= $value;
                if($key < count($request->clubs)-1){
                    $clubstring .= ', ';
                }
            }
            $clubstring .= ')';

            $accjournals->whereRaw('club_id IN '.$clubstring);
        }else{
            $accjournals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
        }

        // COA Added 28082019
        if (!empty($request->coas)):
            $accjournals->where(function ($c) use ($request) {
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

        $accjournals = $accjournals->get();

        return view('cf_acc.main.bank-flow',compact('accjournals','clubs','banks','fromdate','thrudate', 'coas'));
    }

    //10 Jan 2020
    public function bank_flow_v2(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $jubilants = Jubilant::whereHas('jubilant_accounting')->orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');
        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) {
            $q->whereHas('jubilant_accounting');
        })->orderBy('club_name', 'asc')->get();
        $banks = Bank::whereHas('bank_club')->orderBy('bank_name', 'asc')->get();

        $fromdate = new Carbon('first day of this month');
        $thrudate = new Carbon('last day of this month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        $bankstring = '(';
        if(!empty($request->banks)){
            $bank = Bank::findOrFail($request->banks);
            $listbanks = Bank::whereHas('bank_club',function($q) use ($request) {
                $q->where('id_bank', '=', $request->banks)->whereHas('club_jubilant', function ($r) {
                    $r->whereHas('jubilant_accounting');
                });
            })->whereHas('bank_chartofaccount')->pluck('id');
            foreach ($listbanks as $key => $value) {
                $bankstring .= $value;
            }
        }else{
            $listbanks = Bank::whereHas('bank_club',function($q) use ($user){
                $q->whereHas('club_jubilant',function($r) use ($user) {
                    $r->whereHas('jubilant_accounting', function ($s) use ($user) {
                        $s->where('accounting_id','=',$user->id);
                    });
                });
            })->pluck('id');

            foreach ($listbanks as $key => $value) {
                $bankstring .= $value;
                if($key < count($listbanks)-1){
                    $bankstring .= ', ';
                }
            }
        }
        $bankstring .= ')';

        $accJournals = AccountingJournalV2::whereHas('accjournalv2_coa')->whereRaw('
                pair IN (
                SELECT pair from accounting_journals_v2
                JOIN chart_of_accounts ON accounting_journals_v2.coa_id = chart_of_accounts.id
                JOIN mst_banks ON chart_of_accounts.id = mst_banks.coa_id WHERE mst_banks.id IN '.$bankstring.')
            ')
            ->where(function ($q) use ($fromdate, $thrudate) {
                $q->where(function ($r) use ($fromdate, $thrudate) {
                    $r->where('trans_date','>=',$fromdate)->where('trans_date','<=',$thrudate);
                })->orWhere(function ($s) use ($fromdate, $thrudate) {
                        $s->where('bank_date','>=',$fromdate)->where('bank_date','<=',$thrudate);
                });
            })
//            ->where('trans_date','>=',$fromdate)
//            ->where('trans_date','<=',$thrudate)
            ->whereHas('accjournalv2_jubilant', function ($q) use ($user) {
                $q->whereHas('jubilant_accounting', function ($r) use ($user) {
                    $r->where('accounting_id', '=', $user->id);
                });
            });

        if (!empty($request->jubilant)) {
            $accJournals->where('jubilant_id', '=', $request->jubilant);
            if(!empty($request->clubs)){
                $accJournals->where('club_id', '=', $request->clubs);
                if (!empty($request->bank)) {
                    $bank = Bank::findOrFail($request->banks);
                    $accJournals->where('coa_id', '=', $bank->coa_id);
                }
            }
        }

        $accJournals->orderBy('pair', 'asc');
        $accJournals->orderBy('id', 'asc');
        $accJournals = $accJournals->get();

        return view('cf_acc.main.bank-flow-v2', compact('accJournals', 'jubilants', 'clubs', 'banks', 'fromdate', 'thrudate'));
    }

    public function bank_flow_get_bank_ajax(Request $request) {
        if ($request->club_id != '') {
            $banks = Bank::whereHas('bank_club', function ($q) use($request) {
                $q->where('id', '=', $request->club_id);
            })->get();
        } else {
            if ($request->jubilant_id != '') {
                $banks = Bank::whereHas('bank_club', function ($q) use($request) {
                    $q->where('jubilant_id', '=', $request->jubilant_id);
                })->get();
            } else {
                $banks = Bank::whereHas('bank_club')->get();
            }
        }

        return response()->json($banks);
    }

	public function income_statement(Request $request) {
        if (!empty($request->tbs)):
            $tbs = json_decode($request->tbs);
            $from_date = Carbon::parse($request->from_date);
            $thru_date = Carbon::parse($request->thru_date);
            $chosen_club = json_decode($request->chosen_clubs);

            $clubs = ClubInfo::whereHas('club_accounting', function ($q) {
                $q->where('accounting_id', '=', Session::get('account')->id);
            })->orderBy('club_name', 'asc')
                ->pluck('club_name', 'id');

            $coas = ChartOfAccount::orderBy('coa_number', 'asc')->get();
            $coa_sales_expenses = ChartOfAccount::where('coa_number', '=', '5-2000')
                ->orWhere('main_account_id', '=', '17')
                ->get();
            $coa_out_of_businesses = ChartOfAccount::where('main_account_id', '=', '19')
                ->orWhere('main_account_id', '=', '20')
                ->get();

            return view('cf_acc.main.income-statement', compact('tbs', 'from_date', 'thru_date', 'chosen_club', 'clubs', 'coas', 'chosen_coa',
                'coa_sales_expenses', 'coa_out_of_businesses'));
        endif;
    }

    public function promo_journal($gm_payment) {
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $payment = GuestMemberPayment::findOrFail($gm_payment->id);
//        $promo = GuestMembership::findOrFail($promo->id);
        $amortisasi = $payment->amount_paid;

        //JURNAL AKUNTANSI
        //Jurnal Kas
        if($payment->payment_type_id == 1 || $payment->payment_type_id == 6 || $payment->payment_type_id == 7){
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        }else{
            $bank = Bank::findOrFail($payment->bank_id);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        }

        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->debit = $payment->amount_paid;
            $acc_journal->information = 'Terima Penjualan Promo '.$payment->payment_number;
            $acc_journal->club_id = $payment->gmpayment_gmmember->gmmember_guest->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //Jurnal Pendapatan Jasa Diterima di Muka
        $coa_muka = ChartOfAccount::where('coa_number','=','1-1260')->first();

        if(!empty($coa_muka)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_muka->id;
            $acc_journal->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal->credit = $payment->amount_paid;
            $acc_journal->information = 'Pendapatan promo diterima di muka '.$payment->payment_number;
            $acc_journal->club_id = $payment->gmpayment_gmmember->gmmember_guest->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();
        }

        //JURNAL MEMORIAL
        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        //Jurnal Pendapatan diterima di muka
        if(!empty($coa_muka)){
            $acc_journal_d = new MemorialJournal;
            $acc_journal_d->coa_id = $coa_muka->id;
            $acc_journal_d->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_d->debit = $payment->amount_paid;
            $acc_journal_d->information = 'Pendapatan Promo diterima di muka '.$payment->payment_number;
            $acc_journal_d->club_id = $payment->gmpayment_gmmember->gmmember_guest->club_id;
            $acc_journal_d->journal_number = $mnum;
            $acc_journal_d->save();
        }

        //Jurnal Penjualan Jasa
        $coa_jasa = ChartOfAccount::where('coa_number','=','4-1000')->first();

        if(!empty($coa_jasa)){
            //Biaya Layanan
            $acc_journal_sf = new MemorialJournal;
            $acc_journal_sf->coa_id = $coa_jasa->id;
            $acc_journal_sf->trans_date = Carbon::parse($payment->payment_date);
            $acc_journal_sf->credit = $payment->amount_paid;
            $amortisasi -= $payment->amount_paid;
            $acc_journal_sf->information = 'Biaya Promo '.$payment->payment_number;
            $acc_journal_sf->club_id = $payment->gmpayment_gmmember->gmmember_guest->club_id;
            $acc_journal_sf->journal_number = $mnum;
            $acc_journal_sf->save();

            //Amortisasi Jasa
            if ($amortisasi != 0):
                $acc_journal_amor = new MemorialJournal;
                $acc_journal_amor->coa_id = $coa_jasa->id;
                $acc_journal_amor->trans_date = Carbon::parse($payment->payment_date);

                if ($amortisasi > 0):
                    $acc_journal_amor->credit = $amortisasi;
                else:
                    $acc_journal_amor->debit = $amortisasi;
                endif;

                $acc_journal_amor->information = 'Amortisasi Promo '.$payment->payment_number;
                $acc_journal_amor->club_id = $payment->gmpayment_gmmember->gmmember_guest->club_id;
                $acc_journal_amor->journal_number = $mnum;
                $acc_journal_amor->save();
            endif;
        }
    }

    public function add_purchasing_book_journal($purchasingbook, $purchasingpayment, $status){
        // status 0 untuk pembelian unpaid atau pembayaran masih halfpaid
        // status 1 untuk pembayaran belum lunas menjadi lunas

        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $coa_default = ChartOfAccount::where('coa_number','=','0-0000')->first();
        if($purchasingbook->payment_status == 'Paid'){

            if($status == 1){
                // Jurnal Uang Muka
                $coauangmuka = ChartOfAccount::where('coa_number','=','1-1250')->first();
                if(!empty($coauangmuka)){
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coauangmuka->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->debit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }else{
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coa_default->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->debit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }

                // Jurnal Kas Bank
                $coakasbank = ChartOfAccount::find($purchasingpayment->coa_id);
                if(!empty($coakasbank)){
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coakasbank->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->credit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }else{
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coa_default->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->credit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }
            }else{
                // Jurnal Item
                $coaitem = ChartOfAccount::find($purchasingbook->coa_id);
                if(!empty($coaitem)){
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coaitem->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->debit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }else{
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coa_default->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->debit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }

                // Jurnal Kas Bank
                $coakasbank = ChartOfAccount::find($purchasingpayment->coa_id);
                if(!empty($coakasbank)){
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coakasbank->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->credit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }else{
                    $acc_journal = new AccountingJournal;
                    $acc_journal->coa_id = $coa_default->id;
                    $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                    $acc_journal->credit = $purchasingpayment->amount_paid;
                    $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                    $acc_journal->club_id = $purchasingbook->club_id;
                    $acc_journal->journal_number = $jnum;
                    $acc_journal->save();
                }
            }

        }else if($purchasingbook->payment_status == 'Halfpaid'){
            // Jurnal Uang Muka
            $coauangmuka = ChartOfAccount::where('coa_number','=','1-1250')->first();
            if(!empty($coauangmuka)){
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coauangmuka->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->debit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }else{
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_default->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->debit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }

            // Jurnal Kas Bank
            $coakasbank = ChartOfAccount::find($purchasingpayment->coa_id);
            if(!empty($coakasbank)){
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coakasbank->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->credit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }else{
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_default->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->credit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }
        }

        if($status == 1){
            $coaitem = ChartOfAccount::find($purchasingbook->coa_id);
            if(!empty($coaitem)){
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coaitem->id;
                $acc_journal->trans_date = Carbon::parse($purchasingbook->payment_date);
                $acc_journal->debit = $purchasingbook->amount_paid;
                $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }else{
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_default->id;
                $acc_journal->trans_date = Carbon::parse($purchasingbook->payment_date);
                $acc_journal->debit = $purchasingbook->amount_paid;
                $acc_journal->information = 'Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }

            // Jurnal Uang Muka
            $coauangmuka = ChartOfAccount::where('coa_number','=','1-1250')->first();
            if(!empty($coauangmuka)){
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coauangmuka->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->credit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }else{
                $acc_journal = new AccountingJournal;
                $acc_journal->coa_id = $coa_default->id;
                $acc_journal->trans_date = Carbon::parse($purchasingpayment->payment_date);
                $acc_journal->credit = $purchasingpayment->amount_paid;
                $acc_journal->information = 'Uang Muka Pembelian '.$purchasingbook->purchasing_info;
                $acc_journal->club_id = $purchasingbook->club_id;
                $acc_journal->journal_number = $jnum;
                $acc_journal->save();
            }
        }
    }

    public function add_purchasing_book_journalV2(PurchasingBook $purchasingBook, PurchasingBookPayment $bookPayment) {
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        // Coa Hutang
        $coa_hutang = ChartOfAccount::where('jubilant_id', '=', $purchasingBook->jubilant_id)
            ->where('coa_number', '=', '2-1000')
            ->where('coa_name', 'ILIKE', '%hutang usaha%')
            ->first();
        // Insert Data
        // Check Status
        // Unpaid = Insert 2 row
        // Paid = Insert 2 row
        // Half Paid = Insert 3 row
        if ($purchasingBook->payment_status == 'Unpaid') {
            // Debit
            $accjournaldebit = new AccountingJournalV2;
            $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournaldebit->coa_id = $purchasingBook->coa_id;
            $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
            $accjournaldebit->debit = $purchasingBook->amount_due;
            $accjournaldebit->credit = 0;
            $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
            $accjournaldebit->information = $purchasingBook->purchasing_info;
            $accjournaldebit->d_or_c = 'Debit';
            $accjournaldebit->club_id = $purchasingBook->club_id;
            $accjournaldebit->pair = $jnum;
            $accjournaldebit->save();

            // Credit
            $accjournalcredit = new AccountingJournalV2;
            $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalcredit->coa_id = $coa_hutang->id; // Hutang
            $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
            $accjournalcredit->debit = 0;
            $accjournalcredit->credit = $purchasingBook->amount_due;
            $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
            $accjournalcredit->information = $purchasingBook->purchasing_info;
            $accjournalcredit->d_or_c = 'Credit';
            $accjournalcredit->club_id = $purchasingBook->club_id;
            $accjournalcredit->pair = $jnum;
            $accjournalcredit->save();

        } elseif ($purchasingBook->payment_status == 'Paid') {
            // Debit
            $accjournaldebit = new AccountingJournalV2;
            $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournaldebit->coa_id = $purchasingBook->coa_id;
            $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
            $accjournaldebit->debit = $purchasingBook->amount_due;
            $accjournaldebit->credit = 0;
            $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
            $accjournaldebit->information = $purchasingBook->purchasing_info;
            $accjournaldebit->d_or_c = 'Debit';
            $accjournaldebit->club_id = $purchasingBook->club_id;
            $accjournaldebit->pair = $jnum;
            $accjournaldebit->save();

            // Credit
            $accjournalcredit = new AccountingJournalV2;
            $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalcredit->coa_id = $bookPayment->coa_id;
            $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
            $accjournalcredit->debit = 0;
            $accjournalcredit->credit = $purchasingBook->amount_paid;
            $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
            $accjournalcredit->information = $purchasingBook->purchasing_info;
            $accjournalcredit->d_or_c = 'Credit';
            $accjournalcredit->club_id = $purchasingBook->club_id;
            $accjournalcredit->pair = $jnum;
            $accjournalcredit->save();

        } elseif ($purchasingBook->payment_status == 'Halfpaid') {
            // Debit
            $accjournaldebit = new AccountingJournalV2;
            $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournaldebit->coa_id = $purchasingBook->coa_id;
            $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
            $accjournaldebit->debit = $purchasingBook->amount_due;
            $accjournaldebit->credit = 0;
            $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
            $accjournaldebit->information = $purchasingBook->purchasing_info;
            $accjournaldebit->d_or_c = 'Debit';
            $accjournaldebit->club_id = $purchasingBook->club_id;
            $accjournaldebit->pair = $jnum;
            $accjournaldebit->save();

            // Credit
            $accjournalcredit = new AccountingJournalV2;
            $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalcredit->coa_id = $bookPayment->coa_id;
            $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
            $accjournalcredit->debit = 0;
            $accjournalcredit->credit = $purchasingBook->amount_paid;
            $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
            $accjournalcredit->information = $purchasingBook->purchasing_info;
            $accjournalcredit->d_or_c = 'Credit';
            $accjournalcredit->club_id = $purchasingBook->club_id;
            $accjournalcredit->pair = $jnum;
            $accjournalcredit->save();

            // Hutang
            $accjournalhutang = new AccountingJournalV2;
            $accjournalhutang->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalhutang->coa_id = $coa_hutang->id; // Hutang
            $accjournalhutang->trans_date = $purchasingBook->purchasing_date;
            $accjournalhutang->debit = 0;
            $accjournalhutang->credit = $purchasingBook->rest_of_bill;
            $accjournalhutang->invoice_number = $purchasingBook->invoice_number;
            $accjournalhutang->information = $purchasingBook->purchasing_info;
            $accjournalhutang->d_or_c = 'Credit';
            $accjournalhutang->club_id = $purchasingBook->club_id;
            $accjournalhutang->pair = $jnum;
            $accjournalhutang->save();

        }
    }

    public function pay_purchasing_book_journal(PurchasingBook $purchasingBook, PurchasingBookPayment $bookPayment){
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $coa_hutang = ChartOfAccount::where('jubilant_id', '=', $purchasingBook->jubilant_id)
            ->where('coa_number', '=', '2-1000')
            ->where('coa_name', 'ILIKE', '%hutang usaha%')
            ->first();
        // Insert Data
        // Debit
        $accjournaldebit = new AccountingJournalV2;
        $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
        $accjournaldebit->coa_id = $coa_hutang->id; // Hutang
        $accjournaldebit->trans_date = $bookPayment->payment_date;
        $accjournaldebit->debit = $bookPayment->amount_paid;
        $accjournaldebit->credit = 0;
        $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
        $accjournaldebit->information = $purchasingBook->purchasing_info;
        $accjournaldebit->d_or_c = 'Debit';
        $accjournaldebit->club_id = $purchasingBook->club_id;
        $accjournaldebit->pair = $jnum;
        $accjournaldebit->save();

        // Credit
        $accjournalcredit = new AccountingJournalV2;
        $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
        $accjournalcredit->coa_id = $bookPayment->coa_id;
        $accjournalcredit->trans_date = $bookPayment->payment_date;
        $accjournalcredit->debit = 0;
        $accjournalcredit->credit = $bookPayment->amount_paid;
        $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
        $accjournalcredit->information = $purchasingBook->purchasing_info;
        $accjournalcredit->d_or_c = 'Credit';
        $accjournalcredit->club_id = $purchasingBook->club_id;
        $accjournalcredit->pair = $jnum;
        $accjournalcredit->save();
    }

    public function pembalik_purchasing_book(PurchasingBook $purchasingBook, $status) {
        // Status = 0 Set Minus
        // Status = 1 Set new data

        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        // Hutang
        $coa_hutang = ChartOfAccount::where('jubilant_id', '=', $purchasingBook->jubilant_id)
            ->where('coa_number', '=', '2-1000')
            ->where('coa_name', 'ILIKE', '%hutang usaha%')
            ->first();

        if ($status == 0) {
            // Debit
            $accjournaldebit = new AccountingJournalV2;
            $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournaldebit->coa_id = $purchasingBook->coa_id;
            $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
            $accjournaldebit->debit = -($purchasingBook->amount_due);
            $accjournaldebit->credit = 0;
            $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
            $accjournaldebit->information = $purchasingBook->purchasing_info;
            $accjournaldebit->d_or_c = 'Debit';
            $accjournaldebit->club_id = $purchasingBook->club_id;
            $accjournaldebit->pair = $jnum;
            $accjournaldebit->save();

            // Credit
            $accjournalcredit = new AccountingJournalV2;
            $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalcredit->coa_id = $coa_hutang->id;
            $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
            $accjournalcredit->debit = 0;
            $accjournalcredit->credit = -($purchasingBook->amount_due);
            $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
            $accjournalcredit->information = $purchasingBook->purchasing_info;
            $accjournalcredit->d_or_c = 'Credit';
            $accjournalcredit->club_id = $purchasingBook->club_id;
            $accjournalcredit->pair = $jnum;
            $accjournalcredit->save();

        } elseif ($status == 1) {
            // Debit
            $accjournaldebit = new AccountingJournalV2;
            $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournaldebit->coa_id = $purchasingBook->coa_id;
            $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
            $accjournaldebit->debit = $purchasingBook->amount_due;
            $accjournaldebit->credit = 0;
            $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
            $accjournaldebit->information = $purchasingBook->purchasing_info;
            $accjournaldebit->d_or_c = 'Debit';
            $accjournaldebit->club_id = $purchasingBook->club_id;
            $accjournaldebit->pair = $jnum;
            $accjournaldebit->save();

            // Credit
            $accjournalcredit = new AccountingJournalV2;
            $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
            $accjournalcredit->coa_id = $coa_hutang->id;
            $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
            $accjournalcredit->debit = 0;
            $accjournalcredit->credit = $purchasingBook->amount_due;
            $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
            $accjournalcredit->information = $purchasingBook->purchasing_info;
            $accjournalcredit->d_or_c = 'Credit';
            $accjournalcredit->club_id = $purchasingBook->club_id;
            $accjournalcredit->pair = $jnum;
            $accjournalcredit->save();

        }
    }

    public function pembalik_purchasing_book_payment(PurchasingBookPayment $bookPayment, $status) {
        // Status = 0 Set Minus
        // Status = 1 Set new data
        $purchasingBook = PurchasingBook::findOrFail($bookPayment->purchasing_book_id);
        if (!empty($purchasingBook)) {
            $journal_number = AutoNumber::where('name','=','Journal')->first();
            $jnum = $journal_number->current_number;
            $journal_number->current_number += 1;
            $journal_number->save();

            if ($status == 0) {
                // Debit
                $accjournaldebit = new AccountingJournalV2;
                $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
                $accjournaldebit->coa_id = $purchasingBook->coa_id;
                $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
                $accjournaldebit->debit = -($bookPayment->amount_paid);
                $accjournaldebit->credit = 0;
                $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
                $accjournaldebit->information = $purchasingBook->purchasing_info;
                $accjournaldebit->d_or_c = 'Debit';
                $accjournaldebit->club_id = $purchasingBook->club_id;
                $accjournaldebit->pair = $jnum;
                $accjournaldebit->save();

                // Credit
                $accjournalcredit = new AccountingJournalV2;
                $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
                $accjournalcredit->coa_id = $bookPayment->coa_id;
                $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
                $accjournalcredit->debit = 0;
                $accjournalcredit->credit = -($bookPayment->amount_paid);
                $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
                $accjournalcredit->information = $purchasingBook->purchasing_info;
                $accjournalcredit->d_or_c = 'Credit';
                $accjournalcredit->club_id = $purchasingBook->club_id;
                $accjournalcredit->pair = $jnum;
                $accjournalcredit->save();

            } elseif ($status == 1) {
                // Debit
                $accjournaldebit = new AccountingJournalV2;
                $accjournaldebit->jubilant_id = $purchasingBook->jubilant_id;
                $accjournaldebit->coa_id = $purchasingBook->coa_id;
                $accjournaldebit->trans_date = $purchasingBook->purchasing_date;
                $accjournaldebit->debit = $bookPayment->amount_paid;
                $accjournaldebit->credit = 0;
                $accjournaldebit->invoice_number = $purchasingBook->invoice_number;
                $accjournaldebit->information = $purchasingBook->purchasing_info;
                $accjournaldebit->d_or_c = 'Debit';
                $accjournaldebit->club_id = $purchasingBook->club_id;
                $accjournaldebit->pair = $jnum;
                $accjournaldebit->save();

                // Credit
                $accjournalcredit = new AccountingJournalV2;
                $accjournalcredit->jubilant_id = $purchasingBook->jubilant_id;
                $accjournalcredit->coa_id = $bookPayment->coa_id;
                $accjournalcredit->trans_date = $purchasingBook->purchasing_date;
                $accjournalcredit->debit = 0;
                $accjournalcredit->credit = $bookPayment->amount_paid;
                $accjournalcredit->invoice_number = $purchasingBook->invoice_number;
                $accjournalcredit->information = $purchasingBook->purchasing_info;
                $accjournalcredit->d_or_c = 'Credit';
                $accjournalcredit->club_id = $purchasingBook->club_id;
                $accjournalcredit->pair = $jnum;
                $accjournalcredit->save();
            }
        }
    }

    public function refund_journal($member_deposit_id, $amount) {
        $member_deposit = MemberDeposit::findOrFail($member_deposit_id);

        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        // Memorial Auto Number Added 19112019
        $memorial_number = AutoNumber::where('name','=','Memorial')->first();
        $mnum = $memorial_number->current_number;
        $memorial_number->current_number += 1;
        $memorial_number->save();

        $coa = ChartOfAccount::where('coa_number','=','1-1260')->first();
        if(!empty($coa)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa->id;
            $acc_journal->trans_date = $member_deposit->payment_date;
            $acc_journal->debit = $amount;
            $acc_journal->information = 'Retur Penjualan '.$member_deposit->payment_number;
            $acc_journal->club_id = $member_deposit->memberdeposit_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();

            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $coa->id;
            $mem_journal->trans_date = $member_deposit->payment_date;
            $mem_journal->credit = $amount;
            $mem_journal->information = 'Retur Penjualan '.$member_deposit->payment_number;
            $mem_journal->club_id = $member_deposit->memberdeposit_user->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }

        $coa_refund = ChartOfAccount::where('coa_number','=','4-3000')->first();
        if(!empty($coa_refund)){
            $acc_journal = new AccountingJournal;
            $acc_journal->coa_id = $coa_refund->id;
            $acc_journal->trans_date = $member_deposit->payment_date;
            $acc_journal->credit = $amount;
            $acc_journal->information = 'Retur Penjualan '.$member_deposit->payment_number;
            $acc_journal->club_id = $member_deposit->memberdeposit_user->club_id;
            $acc_journal->journal_number = $jnum;
            $acc_journal->save();

            $mem_journal = new MemorialJournal;
            $mem_journal->coa_id = $coa_refund->id;
            $mem_journal->trans_date = $member_deposit->payment_date;
            $mem_journal->debit = $amount;
            $mem_journal->information = 'Retur Penjualan '.$member_deposit->payment_number;
            $mem_journal->club_id = $member_deposit->memberdeposit_user->club_id;
            $mem_journal->journal_number = $mnum;
            $mem_journal->save();
        }
    }

    // Edit General Ledger Added August 28th 2019
    public function edit_gl(Request $request){
        $radio = $request->radio;
        $scr = $request->select_cost_revenue;
        $sal = $request->select_assets_liability;
        $amount = $request->amount;
        foreach ($amount as $key => $value) {
            $acc_journal = AccountingJournal::findOrFail($key);
            if(!empty($acc_journal)){
                if($radio[$key] == 'cost_revenue'){
                    $acc_journal->coa_id = $scr[$key];
                    $acc_journal->debit = $amount[$key];
                    $acc_journal->credit = 0;
                    $acc_journal->trans_date = $request->date;
                }else if($radio[$key] == 'assets_liability'){
                    $acc_journal->coa_id = $sal[$key];
                    $acc_journal->debit = 0;
                    $acc_journal->credit = $amount[$key];
                    $acc_journal->trans_date = $request->date;

                    // change date in memorial journal
                    $temp_memorial = MemorialJournal::whereRaw('information ILIKE \'' . $acc_journal->information . '\'')->first();
                    if (!empty($temp_memorial)):
                        $memorial_journal = MemorialJournal::where('journal_number', '=', $temp_memorial->journal_number)->get();
                        foreach ($memorial_journal as $item):
                            $item->trans_date = $acc_journal->trans_date;

                            $item->save();
                        endforeach;
                    endif;
                }
            }
            $acc_journal->save();

        }
        return redirect('cf-acc/general-ledger');
    }

    public function edit_gl_ajax(Request $request){
        $acc_journal = AccountingJournal::where('id','=',$request->gl_id)->get();
        $collection = collect($acc_journal);
        $acc_journals = AccountingJournal::where('journal_number','=',$acc_journal[0]->journal_number)->whereNotIn('id',[$acc_journal[0]->id])->get();
        foreach ($acc_journals as $aj) {
            $collection->push($aj);
        }

        return response()->json($collection);
    }

    public function cashflow_statement(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');

        $fromdate = new Carbon('first day of last month');
        $thrudate = new Carbon('last day of last month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        // Get Bank's COA from chosen club
        $bank_coas = [];
        if (!empty($request->clubs)):
            foreach ($request->clubs as $key => $value):
                $chosen_club = ClubInfo::findOrFail($value);
                foreach ($chosen_club->club_bank as $bank):
                    array_push($bank_coas, $bank->bank_chartofaccount->coa_number);
                endforeach;
            endforeach;

            $bank_coas = array_unique($bank_coas);
        endif;

        if (!empty($bank_coas)):
            $accjournals = AccountingJournal::selectRaw('SUM(credit) as total')
                ->whereHas('accountingjournal_chartofaccount',function($q) use ($bank_coas){
                    $q->where('coa_number','<>','1-1100');

                    foreach ($bank_coas as $bank):
                        $q->where('coa_number', '<>', $bank);
                    endforeach;
                });

            $journal_filter = '(SELECT journal_number from accounting_journals JOIN chart_of_accounts ON accounting_journals.coa_id = chart_of_accounts.id WHERE coa_number = \'1-1100\'';
            foreach ($bank_coas as $bank):
                $journal_filter .= ' OR coa_number = \''.$bank.'\'';
            endforeach;
            $journal_filter .= ')';

            $accjournals->whereRaw('journal_number IN '.$journal_filter)
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            // Count Expenses
            $expenses = AccountingJournal::selectRaw('SUM(debit) as total, chart_of_accounts.coa_name as coa')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%6-%');

            // Count Interest
            $interest = AccountingJournal::selectRaw('SUM(debit) as total')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('chart_of_accounts.coa_number', '=', '\'8-1100\'')
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            // Count Taxes
            $taxes = AccountingJournal::selectRaw('SUM(debit) as total, chart_of_accounts.coa_name as coa')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('chart_of_accounts.coa_number', '=', '\'1-1460\'')
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            // Count Invests
            $invests = AccountingJournal::selectRaw('SUM(debit) as total, chart_of_accounts.coa_name as coa')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-2%')
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            // Cash Eqivalent Beginning
            $cash_eqiv = AccountingJournal::selectRaw('debit, club_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-11%')
                ->where('information', '=', 'SALDO AWAL')
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            // Capital Stock
            $capital_stocks = AccountingJournal::selectRaw('SUM(debit) as total')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('chart_of_accounts.coa_number', '=', '\'3-1000\'')
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate);

            if(!empty($request->clubs)){
                $clubstring = '(';
                foreach ($request->clubs as $key => $value) {
                    $clubstring .= $value;
                    if($key < count($request->clubs)-1){
                        $clubstring .= ', ';
                    }
                }
                $clubstring .= ')';

                $accjournals->whereRaw('club_id IN '.$clubstring);
                $expenses->whereRaw('club_id IN '.$clubstring);
                $interest->whereRaw('club_id IN '.$clubstring);
                $taxes->whereRaw('club_id IN '.$clubstring);
                $invests->whereRaw('club_id IN '.$clubstring);
                $cash_eqiv->whereRaw('club_id IN '.$clubstring);
                $capital_stocks->whereRaw('club_id IN '.$clubstring);
            }else{
                $accjournals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $expenses->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $interest->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $taxes->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $invests->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $cash_eqiv->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $capital_stocks->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
            }

            $accjournals = $accjournals->get();
            $expenses = $expenses->groupBy('chart_of_accounts.coa_name')->get();
            $interest = $interest->get();
            $taxes = $taxes->groupBy('chart_of_accounts.coa_name')->get();
            $invests = $invests->groupBy('chart_of_accounts.coa_name')->get();
            $cash_eqiv = $cash_eqiv->groupBy('debit', 'club_id')->get();
            $capital_stocks = $capital_stocks->get();

            return view('cf_acc.main.cashflow-statement',compact('accjournals', 'clubs', 'fromdate', 'thrudate', 'expenses', 'interest', 'taxes', 'invests', 'cash_eqiv', 'capital_stocks'));
        else:
            return view('cf_acc.main.cashflow-statement',compact('clubs','fromdate','thrudate'));
        endif;
    }

    public function balance(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');

        $fromdate = new Carbon('first day of last month');
        $thrudate = new Carbon('last day of last month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        // Get Bank's COA from chosen club
        $bank_coas = [];
        if (!empty($request->clubs)):
            foreach ($request->clubs as $key => $value):
                $chosen_club = ClubInfo::findOrFail($value);
                foreach ($chosen_club->club_bank as $bank):
                    array_push($bank_coas, $bank->bank_chartofaccount->coa_number);
                endforeach;
            endforeach;

            $bank_coas = array_unique($bank_coas);
        endif;

        if (!empty($bank_coas)):
            // Kas & Bank
            $cashbanks = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
//                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-1100%');

            // Added 12112019
            ->where(function ($riki) use ($bank_coas) {
                $riki->where('chart_of_accounts.coa_number', 'ILIKE', '%1-1100%');

                foreach ($bank_coas as $bank):
                    $riki->orWhere('chart_of_accounts.coa_number', 'ILIKE', '%'.$bank.'%');
                endforeach;
            });

            // Piutang
            $acc_receivables = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-12%')
                ->where('chart_of_accounts.coa_number', '<>', '\'1-1260\'');

            // Biaya Dibayar Dimuka
            $prepaid_expenses = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-14%');

            // Aktiva Tetap
            $fixed_assets = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-12%');

            // Akumulasi Penyusutan
            $accu_depreciations = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%1-13%');

            // Utang Usaha
            $debts = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where(function ($coa) {
                    $coa->where('chart_of_accounts.coa_number', 'ILIKE', '%2-1%')
                        ->orWhere('chart_of_accounts.coa_number', 'ILIKE', '%2-2%')
                        ->orWhere('chart_of_accounts.coa_number', '=', '\'1-1260\'');
                });

            // Biaya yang Masih Harus Dibayar
            $ymhs = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%2-3%');

            // Ekuitas
            $equities = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where(function ($coa) {
                    $coa->where('chart_of_accounts.coa_number', 'ILIKE', '%3-%')
                        ->orWhere('chart_of_accounts.coa_number', '=', '\'2-2200\'');
                });

//            foreach ($bank_coas as $bank):
//                $cashbanks->orWhere('chart_of_accounts.coa_number', 'ILIKE', '%'.$bank.'%');
//            endforeach;

            if(!empty($request->clubs)){
                $clubstring = '(';
                foreach ($request->clubs as $key => $value) {
                    $clubstring .= $value;
                    if($key < count($request->clubs)-1){
                        $clubstring .= ', ';
                    }
                }
                $clubstring .= ')';

                $cashbanks->whereRaw('club_id IN '.$clubstring);
                $acc_receivables->whereRaw('club_id IN '.$clubstring);
                $prepaid_expenses->whereRaw('club_id IN '.$clubstring);
                $fixed_assets->whereRaw('club_id IN '.$clubstring);
                $accu_depreciations->whereRaw('club_id IN '.$clubstring);
                $debts->whereRaw('club_id IN '.$clubstring);
                $ymhs->whereRaw('club_id IN '.$clubstring);
                $equities->whereRaw('club_id IN '.$clubstring);
            }else{
                $cashbanks->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $acc_receivables->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $prepaid_expenses->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $fixed_assets->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $accu_depreciations->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $debts->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $ymhs->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $equities->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
            }

            $cashbanks = $cashbanks->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $acc_receivables = $acc_receivables->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $prepaid_expenses = $prepaid_expenses->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $fixed_assets = $fixed_assets->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $accu_depreciations = $accu_depreciations->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $debts = $debts->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $ymhs = $ymhs->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $equities = $equities->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();

            return view('cf_acc.main.balance', compact('clubs', 'fromdate', 'thrudate', 'bank_coas',
                'cashbanks', 'acc_receivables', 'prepaid_expenses', 'fixed_assets', 'accu_depreciations',
                'debts', 'ymhs', 'equities'));
        else:
            return view('cf_acc.main.balance', compact('clubs', 'fromdate', 'thrudate'));
        endif;
    }

    public function income_statement_v2(Request $request) {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $clubs = ClubInfo::whereHas('club_accounting',function($q) use ($user){
            $q->where('accounting_id','=',$user->id);
        })->pluck('club_name','id');

        $fromdate = new Carbon('first day of last month');
        $thrudate = new Carbon('last day of last month');
        if(!empty($request->from_date)){
            $fromdate = $request->from_date;
        }
        if(!empty($request->thru_date)){
            $thrudate = $request->thru_date;
        }

        // Get Bank's COA from chosen club
        $bank_coas = [];
        if (!empty($request->clubs)):
            foreach ($request->clubs as $key => $value):
                $chosen_club = ClubInfo::findOrFail($value);
                foreach ($chosen_club->club_bank as $bank):
                    array_push($bank_coas, $bank->bank_chartofaccount->coa_number);
                endforeach;
            endforeach;

            $bank_coas = array_unique($bank_coas);
        endif;

        if (!empty($bank_coas)):
            // Penjualan
//            $sales = AccountingJournal::selectRaw('SUM(debit) as total,
//                chart_of_accounts.coa_name as coa,
//                chart_of_accounts.id as coa_id')
//                ->join('chart_of_accounts', function ($join) {
//                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
//                })
//                ->where('trans_date','>=',$fromdate)
//                ->where('trans_date','<=',$thrudate)
//                ->where('chart_of_accounts.coa_number', 'ILIKE', '%4-%');

            $sales = MemorialJournal::selectRaw('SUM(credit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id,
                chart_of_accounts.coa_number as coa_num')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'memorial_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%4-%')
                ->where('chart_of_accounts.coa_number', '<>', '4-3000');

            $retur_sales = AccountingJournal::selectRaw('SUM(credit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id,
                chart_of_accounts.coa_number as coa_num')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', '=', '4-3000');

            // Beban Penjualan
            $sales_expenses = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where(function ($q) {
                    $q->where('chart_of_accounts.coa_number', 'ILIKE', '%6-1%')
                        ->orWhere('chart_of_accounts.coa_number', '=', '\'5-2000\'');
                });

            // Beban Operasional
            $operationals = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where(function ($q) {
                    $q->where('chart_of_accounts.coa_number', 'ILIKE', '%6-2%')
                        ->orWhere('chart_of_accounts.coa_number', '=', '\'6-2128\'');
                });

            // Pendapatan Lain-Lain
            $external_sources = AccountingJournal::selectRaw('SUM(debit) as total,
                chart_of_accounts.coa_name as coa,
                chart_of_accounts.id as coa_id')
                ->join('chart_of_accounts', function ($join) {
                    $join->on('chart_of_accounts.id', '=', 'accounting_journals.coa_id');
                })
                ->where('trans_date','>=',$fromdate)
                ->where('trans_date','<=',$thrudate)
                ->where('chart_of_accounts.coa_number', 'ILIKE', '%7-%');

            if(!empty($request->clubs)){
                $clubstring = '(';
                foreach ($request->clubs as $key => $value) {
                    $clubstring .= $value;
                    if($key < count($request->clubs)-1){
                        $clubstring .= ', ';
                    }
                }
                $clubstring .= ')';

                $sales->whereRaw('club_id IN '.$clubstring);
                $retur_sales->whereRaw('club_id IN '.$clubstring);
                $sales_expenses->whereRaw('club_id IN '.$clubstring);
                $operationals->whereRaw('club_id IN '.$clubstring);
                $external_sources->whereRaw('club_id IN '.$clubstring);
            }else{
                $sales->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $retur_sales->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $sales_expenses->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $operationals->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
                $external_sources->whereRaw('club_id IN (SELECT club_id FROM accounting_clubs WHERE accounting_id = '.$user->id.')');
            }

            $sales = $sales->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $retur_sales = $retur_sales->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $sales_expenses = $sales_expenses->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $operationals = $operationals->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();
            $external_sources = $external_sources->groupBy('chart_of_accounts.coa_name', 'chart_of_accounts.id')->orderBy('chart_of_accounts.id', 'asc')->get();

            return view('cf_acc.main.income-statement-v2', compact('clubs', 'fromdate', 'thrudate', 'bank_coas',
                'sales', 'retur_sales', 'sales_expenses', 'operationals', 'external_sources'));
        else:
            return view('cf_acc.main.income-statement-v2', compact('clubs', 'fromdate', 'thrudate'));
        endif;
    }
}
