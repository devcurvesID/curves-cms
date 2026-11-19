<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\AccountingJournal;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Club\ClubInfo;
use App\Model\Jubilant\Jubilant;
use App\Model\Log\LogManualTransaction;
use App\Model\MasterData\Bank;
use App\Model\MasterData\CostRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class AccountingManualTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    public function manual_transaction(){
        $clubs = ClubInfo::where('status', '=', 'Active')
            ->whereHas('club_accounting', function ($q){
                $q->where('accounting_id', '=', Session::get('account')->id);
            })->get();

        $coas = ChartOfAccount::selectRaw('chart_of_accounts.id, chart_of_accounts.coa_abv_name, chart_of_accounts.coa_number')
            ->where('main_account_id', '<>', 1)
            ->where('main_account_id', '<>', 22)
            ->join('main_accounts', 'main_account_id', '=', 'main_accounts.id')
            ->where('main_accounts.account_type', '<>', 'Biaya')
            ->where('main_accounts.account_type', '<>', 'Pendapatan')
            ->orderBy('coa_abv_name', 'asc')->get();

        $cost_revenues = CostRevenue::get();

        return view('cf_acc.main.manual-transaction', compact('clubs', 'cost_revenues', 'coas'));
    }

    public function store_manual_transaction(Request $request){
        $this->validate($request, [
            'transaction_info' => 'required',
            'trans_date' => 'required',
        ]);

        //Cek Coa Kas dan Bank
        if($request->via == 1):
            $coa = ChartOfAccount::where('coa_number','=','1-1100')->first();
        else:
            $bank = Bank::findOrFail($request->via);
            $coa = ChartOfAccount::where('coa_number','=',$bank->bank_chartofaccount->coa_number)->first();
        endif;

        if ($request->radio == "cost_revenue"):
            //Set Coa dari cost revenue
            $costrevenue = CostRevenue::findOrFail($request->select_cost_revenue);
            if (!empty($costrevenue)):
                $coa_pilihan = ChartOfAccount::where('coa_number', '=', $costrevenue->costrevenue_chartofaccount->coa_number)->first();
            endif;
            //Set Accounting Jurnal
            if ($request->debit != 0):
                //debit
                $acc_jur_deb = new AccountingJournal;
                $acc_jur_deb->coa_id = $coa_pilihan->id;
                $acc_jur_deb->trans_date = $request->trans_date;
                $acc_jur_deb->debit = $request->debit;
                $acc_jur_deb->information = $request->transaction_info;
                $acc_jur_deb->club_id = $request->club;
                $acc_jur_deb->save();

                //credit
                $acc_jur_cre = new AccountingJournal;
                $acc_jur_cre->coa_id = $coa->id;
                $acc_jur_cre->trans_date = $request->trans_date;
                $acc_jur_cre->credit = $request->debit;
                $acc_jur_cre->information = $request->transaction_info;
                $acc_jur_cre->club_id = $request->club;
                $acc_jur_cre->save();

            elseif($request->credit != 0):
                //debit
                $acc_jur_deb = new AccountingJournal;
                $acc_jur_deb->coa_id = $coa->id;
                $acc_jur_deb->trans_date = $request->trans_date;
                $acc_jur_deb->debit = $request->credit;
                $acc_jur_deb->information = $request->transaction_info;
                $acc_jur_deb->club_id = $request->club;
                $acc_jur_deb->save();

                //credit
                $acc_jur_cre = new AccountingJournal;
                $acc_jur_cre->coa_id = $coa_pilihan->id;
                $acc_jur_cre->trans_date = $request->trans_date;
                $acc_jur_cre->credit = $request->credit;
                $acc_jur_cre->information = $request->transaction_info;
                $acc_jur_cre->club_id = $request->club;
                $acc_jur_cre->save();
            endif;

            //Create Log
            $log = new LogManualTransaction;
            $log->acc_journal_debit_id = $acc_jur_deb->id;
            $log->acc_journal_credit_id = $acc_jur_cre->id;
            $log->club_id = $request->club;
            $log->coa_id = $coa_pilihan->id;
            $log->debit = $acc_jur_deb->debit;
            $log->credit = $acc_jur_cre->credit;
            $log->information = $request->transaction_info;
            $log->coa_via_payment_id = $coa->id;
            $log->account_id = Session::get('account')->id;
            $log->save();
        elseif($request->radio == "assets_liability"):
            //Set Coa dari assets liability
            $assets_liability = ChartOfAccount::findOrFail($request->select_assets_liability);

            //Set Accounting Jurnal
            if ($request->debit != 0):
                //debit
                $acc_jur_deb = new AccountingJournal;
                $acc_jur_deb->coa_id = $assets_liability->id;
                $acc_jur_deb->trans_date = $request->trans_date;
                $acc_jur_deb->debit = $request->debit;
                $acc_jur_deb->information = $request->transaction_info;
                $acc_jur_deb->club_id = $request->club;
                $acc_jur_deb->save();

                //credit
                $acc_jur_cre = new AccountingJournal;
                $acc_jur_cre->coa_id = $coa->id;
                $acc_jur_cre->trans_date = $request->trans_date;
                $acc_jur_cre->credit = $request->debit;
                $acc_jur_cre->information = $request->transaction_info;
                $acc_jur_cre->club_id = $request->club;
                $acc_jur_cre->save();

            elseif($request->credit != 0):
                //debit
                $acc_jur_deb = new AccountingJournal;
                $acc_jur_deb->coa_id = $coa->id;
                $acc_jur_deb->trans_date = $request->trans_date;
                $acc_jur_deb->debit = $request->credit;
                $acc_jur_deb->information = $request->transaction_info;
                $acc_jur_deb->club_id = $request->club;
                $acc_jur_deb->save();

                //credit
                $acc_jur_cre = new AccountingJournal;
                $acc_jur_cre->coa_id = $assets_liability->id;
                $acc_jur_cre->trans_date = $request->trans_date;
                $acc_jur_cre->credit = $request->credit;
                $acc_jur_cre->information = $request->transaction_info;
                $acc_jur_cre->club_id = $request->club;
                $acc_jur_cre->save();
            endif;

            //Create Log
            $log = new LogManualTransaction;
            $log->acc_journal_debit_id = $acc_jur_deb->id;
            $log->acc_journal_credit_id = $acc_jur_cre->id;
            $log->club_id = $request->club;
            $log->coa_id = $assets_liability->id;
            $log->debit = $acc_jur_deb->debit;
            $log->credit = $acc_jur_cre->credit;
            $log->information = $request->transaction_info;
            $log->coa_via_payment_id = $coa->id;
            $log->account_id = Session::get('account')->id;
            $log->save();
        endif;

        Session::flash('flash_message', trans('messages.input_manual_transaction_success'));
        return redirect('cf-acc/transaction');
    }

    public function via_transaction_ajax(Request $request){
        $this->validate($request, [
            'club_id' => 'required',
        ]);

        $banks = Bank::whereHas('bank_club', function ($q) use ($request){
            $q->where('club_id', '=', $request->club_id);
        })->get();

        return response()->json(array('bank'=>$banks));
    }

    //10032020
    public function manual_transactionV2(){
        $jubilants = Jubilant::whereHas('jubilant_accounting')->orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');
        $clubs = ClubInfo::where('status', '=', 'Active')->whereHas('club_jubilant', function ($q) {
            $q->whereHas('jubilant_accounting');
        })->orderBy('club_name', 'asc')->get();

        $coas = ChartOfAccount::where('coa_number', '<>', '0-0000')->get();

        return view('cf_acc.main.manual-transaction-v2', compact('jubilants', 'clubs', 'coas'));
    }

    public function store_manual_transactionV2(Request $request) {
        // Get Journal Current Number
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $invoice_number = sprintf('%08d', $jnum).'/FR/'.sprintf('%03d', $request->club).'/'.$month_roman[Carbon::parse($request->trans_date)->format('n')].'/'.Carbon::parse($request->trans_date)->format('Y');

        if (!empty($request->coa_debit) && !empty($request->coa_credit)) {
            // Pair because not empty coa debit and credit
            //DEBIT
            $acc_journal_debit = new AccountingJournalV2;
            $acc_journal_debit->jubilant_id = $request->jubilant;
            $acc_journal_debit->coa_id = $request->coa_debit;
            $acc_journal_debit->trans_date = $request->trans_date;
            $acc_journal_debit->debit = $request->debit;
            $acc_journal_debit->credit = 0;
            $acc_journal_debit->invoice_number = $invoice_number;
            $acc_journal_debit->information = $request->transaction_info;
            $acc_journal_debit->d_or_c = 'Debit';
            $acc_journal_debit->club_id = $request->club;
            $acc_journal_debit->pair = $jnum;
            $acc_journal_debit->save();

            //CREDIT
            $acc_journal_credit = new AccountingJournalV2;
            $acc_journal_credit->jubilant_id = $request->jubilant;
            $acc_journal_credit->coa_id = $request->coa_credit;
            $acc_journal_credit->trans_date = $request->trans_date;
            $acc_journal_credit->debit = 0;
            $acc_journal_credit->credit = $request->credit;
            $acc_journal_credit->invoice_number = $invoice_number;
            $acc_journal_credit->information = $request->transaction_info;
            $acc_journal_credit->d_or_c = 'Credit';
            $acc_journal_credit->club_id = $request->club;
            $acc_journal_credit->pair = $jnum;
            $acc_journal_credit->save();

            //Check Monthly Ending Balance
            // Cek jika perubahan diperuntukkan bagi bulan yang telah lalu
            if ((Carbon::parse($acc_journal_debit->trans_date)->format('m-Y') != Carbon::today()->format('m-Y')) && (Carbon::parse($acc_journal_debit->trans_date) < Carbon::today())):
                // Update Monthly Ending Balance
                app('App\Http\Controllers\AccountingTemporaryJournalController')->checkMonthlyEndingBalance($acc_journal_debit, $acc_journal_credit);
            endif;
        } else {
            // Pair null because empty coa debit or credit
            $acc_journal = new AccountingJournalV2;
            $acc_journal->jubilant_id = $request->jubilant;
            $acc_journal->coa_id = ($request->coa_debit != null) ? $request->coa_debit : $request->coa_credit;
            $acc_journal->trans_date = $request->trans_date;
            $acc_journal->debit = ($request->debit != 0) ? $request->debit : 0;
            $acc_journal->credit = ($request->credit != 0) ? $request->credit : 0;
            $acc_journal->invoice_number = $invoice_number;
            $acc_journal->information = $request->transaction_info;
            $acc_journal->d_or_c = ($request->coa_debit != null) ? 'Debit' : 'Credit';
            $acc_journal->club_id = $request->club;
            $acc_journal->save();
        }

        Session::flash('flash_message', trans('messages.input_manual_transaction_success'));
        return redirect('cf-acc/transaction');
    }

    // BEGINNING BALANCE
    public function beginning_balance() {
        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        $jubilants = Jubilant::whereHas('jubilant_accounting', function ($q) use ($user) {
            $q->where('accounting_id', '=', $user->id);
        })->orderBy('jubilant_name', 'asc')->get();

        $clubs = ClubInfo::whereHas('club_jubilant', function ($q) {
            $q->whereHas('jubilant_accounting');
        })->orderBy('club_name', 'asc')->get();

        $coas = ChartOfAccount::where('coa_number', 'ILIKE', '%1-%')
            ->orWhere('coa_number', 'ILIKE', '%2-%')
            ->orWhere('coa_number', 'ILIKE', '%3-%')
            ->get();

        return view('cf_acc.main.beginning-balance-v2', compact('jubilants','clubs', 'coas'));
    }

    public function store_beginning_balance(Request $request) {
        $this->validate($request, [
            'club' => 'required|exists:club_infos,id',
            'coa' => 'required|exists:chart_of_accounts,id',
            'debit' => 'required|numeric',
            'credit' => 'required|numeric',
        ]);

        $from_date = Carbon::parse(new Carbon('01-'.$request->date));

        // Get Journal Current Number
        $journal_number = AutoNumber::where('name','=','Journal')->first();
        $jnum = $journal_number->current_number;
        $journal_number->current_number += 1;
        $journal_number->save();

        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $invoice_number = sprintf('%08d', $jnum).'/SALDOAWAL/'.sprintf('%03d', $request->club).'/'.$month_roman[Carbon::parse($from_date)->format('n')].'/'.Carbon::parse($from_date)->format('Y');

        $club = ClubInfo::findOrFail($request->club);
        $coa = ChartOfAccount::where('jubilant_id', '=', $request->jubilant)->where('id', '=', $request->coa)->first();

        // Insert Beginning Balance
        $acc_journals = new AccountingJournalV2();
        $acc_journals->jubilant_id = $request->jubilant;
        $acc_journals->coa_id = $request->coa;
        $acc_journals->trans_date = Carbon::parse($from_date)->toDateString();
        $acc_journals->debit = $request->debit;
        $acc_journals->credit = $request->credit;
        $acc_journals->invoice_number = $invoice_number;
        $acc_journals->information = 'Saldo Awal '.$coa->coa_number.' '.$club->club_name.' '.Carbon::parse($from_date)->format('F').' '.Carbon::parse($from_date)->format('Y');
        $acc_journals->d_or_c = ($request->debit != 0) ? 'Debit' : 'Credit';
        $acc_journals->club_id = $request->club;
        $acc_journals->save();

        Session::flash('flash_message', trans('messages.beginning_balance_has_been_successfully_stored'));
        return redirect('cf-acc/accounting/beginning-balance');
    }

    public function import_beginning_balance(Request $request) {
        if ($request->hasFile('data')) {
            $path = $request->file('data')->getRealPath();
            $data = Excel::load($path, function ($reader) {
                $reader->noHeading();
            })->get();

            $temp_jubilant = [];
            foreach ($data as $key => $value) {
                if ($value[0] != null) {
                    if ($key == 0) {
                        $jubilant = Jubilant::where('jubilant_name', 'ILIKE', $value[1])->first();
                        $temp_jubilant = $jubilant;
                    } elseif ($key == 1) {
                    } else {
                        $date = Carbon::parse($value[0])->toDateString();
                        $coa = ChartOfAccount::where('jubilant_id', '=', $temp_jubilant->id)->where('coa_number', '=', $value[1])->first();
                        $cek = AccountingJournalV2::where('coa_id', '=', $coa->id)->whereDate('trans_date', '=', Carbon::parse($date))->first();
                        $club = ClubInfo::where('club_code', 'ILIKE', '%'.$value[2].'%')->first();

                         if (!empty($club) && !empty($temp_jubilant)) {
                             if (!empty($cek)) {
                                 //update data
                                 $acc_journals = AccountingJournalV2::where('jubilant_id', '=', $temp_jubilant->id)
                                     ->where('coa_id', '=', $coa->id)
                                     ->where('trans_date', '=', $date)->first();
                                 if (!empty($acc_journals)) {
                                     $acc_journals->debit = $value[3];
                                     $acc_journals->credit = $value[4];
                                     $acc_journals->save();
                                 }
                             } else {
                                 //new data
                                 // Get Journal Current Number
                                 $journal_number = AutoNumber::where('name','=','Journal')->first();
                                 $jnum = $journal_number->current_number;
                                 $journal_number->current_number += 1;
                                 $journal_number->save();

                                 $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                                 $invoice_number = sprintf('%08d', $jnum).'/SALDOAWAL/'.sprintf('%03d', $club->id).'/'.$month_roman[Carbon::parse($date)->format('n')].'/'.Carbon::parse($date)->format('Y');

                                 // Insert Beginning Balance
                                $acc_journals = new AccountingJournalV2();
                                $acc_journals->jubilant_id = $temp_jubilant->id;
                                $acc_journals->coa_id = $coa->id;
                                $acc_journals->trans_date = $date;
                                $acc_journals->debit = $value[3];
                                $acc_journals->credit = $value[4];
                                $acc_journals->invoice_number = $invoice_number;
                                $acc_journals->information = 'Saldo Awal '.$coa->coa_number.' '.$club->club_name.' '.Carbon::parse($date)->format('F').' '.Carbon::parse($date)->format('Y');
                                $acc_journals->d_or_c = ($value[3] != 0) ? 'Debit' : 'Credit';
                                $acc_journals->club_id = $club->id;
                                $acc_journals->save();
                             }
                        }
                    }
                }
            }
            Session::flash('flash_message', trans('messages.data_has_been_imported'));
            return redirect('cf-acc/accounting/beginning-balance');
        } else {
            Session::flash('flash_message', trans('messages.no_data_has_been_imported'));
            return redirect('cf-acc/accounting/beginning-balance');
        }
    }
}
