<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\MonthlyEndingBalance;
use App\Model\Accounting\TempJournal;
use App\Model\Club\ClubInfo;
use App\Model\Jubilant\Jubilant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AccountingTemporaryJournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    public function index(Request $request)
    {
        $temp_journals = TempJournal::where('approved', '=', false)
            ->where('d_or_c', '=', 'Debit');

        // Filter Date
        if (!empty($request->from_date)):
            $temp_journals->whereDate('trans_date', '>=', Carbon::parse($request->from_date)->toDateString());
        endif;

        if (!empty($request->thru_date)):
            $temp_journals->whereDate('trans_date', '<=', Carbon::parse($request->thru_date)->toDateString());
        endif;

        // Filter Jubilant
        if (!empty($request->jubilant)){
            $temp_journals->where('jubilant_id', '=', $request->jubilant);
        }else{
            $ct_jubilant = 0;
            $signed_in_user = AccountingAccount::findOrFail(Session::get('account')->id);
            $temp_journals->where(function ($jub) use ($signed_in_user, $ct_jubilant) {
                foreach ($signed_in_user->accounting_jubilant as $value):
                    if (empty($ct_jubilant)):
                        $jub->where('jubilant_id', '=', $value->id);
                    else:
                        $jub->orWhere('jubilant_id', '=', $value->id);
                    endif;
                    $ct_jubilant++;
                endforeach;
            });
        }

        // Filter Club
        if (!empty($request->club)):
            $temp_journals->where('club_id', '=', $request->club);
        endif;

        $temp_journals = $temp_journals->orderBy('pair', 'ASC')->paginate(50);
        //dd($temp_journals);

        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        if (count($user->accounting_jubilant) > 0):
            $counter = 0;
            $jubilants = Jubilant::where(function ($jub) use ($user, $counter) {
                foreach ($user->accounting_jubilant as $value):
                    if (empty($counter)):
                        $jub->where('id', '=', $value->id);
                    else:
                        $jub->orWhere('id', '=', $value->id);
                    endif;
                    $counter++;
                endforeach;
            })
                ->pluck('jubilant_name', 'id');

            $counter = 0;
            $clubs = ClubInfo::where(function ($club) use ($jubilants, $counter) {
                foreach ($jubilants as $key => $value):
                    if (empty($counter)):
                        $club->where('jubilant_id', '=', $key);
                    else:
                        $club->orWhere('jubilant_id', '=', $key);
                    endif;
                    $counter++;
                endforeach;
            })
                ->orderBy('club_name', 'asc')
                ->get();
        else:
            return redirect('cf-acc/dashboard');
        endif;

        return view('cf_acc.temp_journals.temp-journal-index', compact('temp_journals', 'jubilants', 'clubs'));
    }

    public function store_temp_journal(Request $request)
    {
        $this->validate($request, [
            'tj_id' => 'required|exists:temp_journals,id'
        ]);

        // Update Temp Journals (Debit & Credit)
        $temp_journal = TempJournal::findOrFail($request->tj_id);
        $temp_journal_c = TempJournal::where('pair', '=', $temp_journal->pair)
            ->where('d_or_c', '=', 'Credit')
            ->first();

        if (Carbon::parse($request->trans_date) != Carbon::parse($temp_journal->trans_date)):
            $temp_journal->trans_date = $request->trans_date;
            $temp_journal_c->trans_date = $request->trans_date;
        endif;

        if (!empty($request->bank_date)):
            $temp_journal->bank_date = $request->bank_date;
            $temp_journal_c->bank_date = $request->bank_date;
        endif;

        if ($request->debit != $temp_journal->debit):
            $temp_journal->debit = $request->debit;
            $temp_journal_c->credit = $request->debit;
        endif;

        $temp_journal->approved = true;
        $temp_journal->save();

        $temp_journal_c->approved = true;
        $temp_journal_c->save();

        // Write The Accounting Journal V2
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'Journal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Debit
        $acc_d = new AccountingJournalV2;
        $acc_d->jubilant_id = $temp_journal->jubilant_id;
        $acc_d->coa_id = $temp_journal->coa_id;
        $acc_d->trans_date = $temp_journal->trans_date;
        $acc_d->bank_date = $temp_journal->bank_date;
        $acc_d->debit = $request->debit;
        $acc_d->credit = $temp_journal->credit;
        $acc_d->invoice_number = $temp_journal->invoice_number;
        $acc_d->information = $temp_journal->information;
        $acc_d->d_or_c = $temp_journal->d_or_c;
        $acc_d->club_id = $temp_journal->club_id;
        $acc_d->pair = $pair->current_number;
        $acc_d->save();

        // Credit
        $acc_c = new AccountingJournalV2;
        $acc_c->jubilant_id = $temp_journal_c->jubilant_id;
        $acc_c->coa_id = $temp_journal_c->coa_id;
        $acc_c->trans_date = $temp_journal_c->trans_date;
        $acc_c->bank_date = $temp_journal_c->bank_date;
        $acc_c->debit = $temp_journal_c->debit;
        $acc_c->credit = $temp_journal_c->credit;
        $acc_c->invoice_number = $temp_journal_c->invoice_number;
        $acc_c->information = $temp_journal_c->information;
        $acc_c->d_or_c = $temp_journal_c->d_or_c;
        $acc_c->club_id = $temp_journal_c->club_id;
        $acc_c->pair = $pair->current_number;
        $acc_c->save();

        // Cek jika perubahan diperuntukkan bagi bulan yang telah lalu
        if ((Carbon::parse($acc_d->trans_date)->format('m-Y') != Carbon::today()->format('m-Y')) && (Carbon::parse($acc_d->trans_date) < Carbon::today())):
            $this->checkMonthlyEndingBalance($acc_d, $acc_c);
        endif;

        return response()->json(true);
    }

    public function checkMonthlyEndingBalance(AccountingJournalV2 $acc_d, AccountingJournalV2 $acc_c)
    {
        // Set Filter recap date
        $recap_date = Carbon::parse($acc_d->trans_date)->lastOfMonth();

        // Debit
        $meb_d = MonthlyEndingBalance::where('jubilant_id', '=', $acc_d->jubilant_id)
            ->where('club_id', '=', $acc_d->club_id)
            ->where('coa_id', '=', $acc_d->coa_id)
            ->whereDate('recap_date', '=', Carbon::parse($recap_date)->toDateString())
//            ->where('d_or_c', '=', 'Debit')
            ->first();

        if (!empty($meb_d)):
            $update_meb_d = MonthlyEndingBalance::findOrFail($meb_d->id);
            $update_meb_d->debit = $update_meb_d->debit + $acc_d->debit;
            $update_meb_d->credit = $update_meb_d->credit + $acc_d->credit;
            $update_meb_d->d_or_c = ($update_meb_d->debit >= $update_meb_d->credit) ? 'Debit' : 'Credit';
            $update_meb_d->save();
        else:
            $new_meb_d = new MonthlyEndingBalance;
            $new_meb_d->jubilant_id = $acc_d->jubilant_id;
            $new_meb_d->club_id = $acc_d->club_id;
            $new_meb_d->coa_id = $acc_d->coa_id;
            $new_meb_d->recap_date = $recap_date;
            $new_meb_d->debit = $acc_d->debit;
            $new_meb_d->credit = $acc_d->credit;
            $new_meb_d->d_or_c = ($acc_d->debit >= $acc_d->credit) ? 'Debit' : 'Credit';
            $new_meb_d->save();
        endif;

        // Credit
        $meb_c = MonthlyEndingBalance::where('jubilant_id', '=', $acc_c->jubilant_id)
            ->where('club_id', '=', $acc_c->club_id)
            ->where('coa_id', '=', $acc_c->coa_id)
            ->whereDate('recap_date', '=', Carbon::parse($recap_date)->toDateString())
//            ->where('d_or_c', '=', 'Credit')
            ->first();

        if (!empty($meb_c)):
            $update_meb_c = MonthlyEndingBalance::findOrFail($meb_c->id);
            $update_meb_c->debit = $update_meb_c->debit + $acc_c->debit;
            $update_meb_c->credit = $update_meb_c->credit + $acc_c->credit;
            $update_meb_c->d_or_c = ($update_meb_c->debit >= $update_meb_c->credit) ? 'Debit' : 'Credit';
            $update_meb_c->save();
        else:
            $new_meb_c = new MonthlyEndingBalance;
            $new_meb_c->jubilant_id = $acc_c->jubilant_id;
            $new_meb_c->club_id = $acc_c->club_id;
            $new_meb_c->coa_id = $acc_c->coa_id;
            $new_meb_c->recap_date = $recap_date;
            $new_meb_c->debit = $acc_c->debit;
            $new_meb_c->credit = $acc_c->credit;
            $new_meb_c->d_or_c = ($acc_c->debit >= $acc_c->credit) ? 'Debit' : 'Credit';
            $new_meb_c->save();
        endif;
    }
}
