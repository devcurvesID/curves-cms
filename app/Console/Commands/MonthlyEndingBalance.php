<?php

namespace App\Console\Commands;

use App\Model\Accounting\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyEndingBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:meb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kalkulasi Saldo Akhir COA di setiap akhir bulan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $from_date = new Carbon('first day of last month');
        $thru_date = new Carbon('last day of last month');
        $recap_date = Carbon::parse($from_date)->subDay();

        $coas = ChartOfAccount::selectRaw('chart_of_accounts.id, chart_of_accounts.jubilant_id, 
            SUM(accounting_journals_v2.debit) as total_debit, SUM(accounting_journals_v2.credit) as total_credit, 
            accounting_journals_v2.club_id')
            ->join('accounting_journals_v2', function ($journal) use ($from_date, $thru_date) {
                $journal->on('accounting_journals_v2.coa_id', '=', 'chart_of_accounts.id');
                $journal->whereDate('trans_date', '>=', Carbon::parse($from_date)->toDateString())
                    ->whereDate('trans_date', '<=', Carbon::parse($thru_date)->toDateString());
            })
            ->groupBy('chart_of_accounts.id', 'accounting_journals_v2.club_id')
            ->get();

        // Create Monthly Ending Balance based on COA
        if (count($coas) > 0):
            foreach ($coas as $coa):
                // Check last Monthly Ending Balance
                $check_last_meb = \App\Model\Accounting\MonthlyEndingBalance::where('jubilant_id', '=', $coa->jubilant_id)
                    ->where('club_id', '=', $coa->club_id)
                    ->where('coa_id', '=', $coa->id)
                    ->whereDate('recap_date', '=', Carbon::parse($recap_date)->toDateString())
                    ->first();

                // Create new Monthly Ending Balance
                $meb = new \App\Model\Accounting\MonthlyEndingBalance;
                $meb->jubilant_id = $coa->jubilant_id;
                $meb->club_id = $coa->club_id;
                $meb->coa_id = $coa->id;
                $meb->recap_date = $thru_date;
                $meb->debit = !empty($check_last_meb) ? ($check_last_meb->debit + $coa->total_debit) : $coa->total_debit;
                $meb->credit = !empty($check_last_meb) ? ($check_last_meb->credit + $coa->total_credit) : $coa->total_credit;
                $meb->d_or_c = ($meb->debit >= $meb->credit) ? 'Debit' : 'Credit';
                $meb->save();
            endforeach;
        endif;

        // Create Monthly Ending Balance based on Last MEB
        $last_mebs = \App\Model\Accounting\MonthlyEndingBalance::where('jubilant_id', '=', $coa->jubilant_id)
            ->get();

        foreach ($last_mebs as $last_meb):
            // Check if MEB is already made
            $check_cur_meb = \App\Model\Accounting\MonthlyEndingBalance::where('jubilant_id', '=', $last_meb->jubilant_id)
                ->where('club_id', '=', $last_meb->club_id)
                ->where('coa_id', '=', $last_meb->coa_id)
                ->whereDate('recap_date', '=', Carbon::parse($thru_date)->toDateString())
                ->first();

            if (empty($check_cur_meb)):
                $meb = new \App\Model\Accounting\MonthlyEndingBalance;
                $meb->jubilant_id = $last_meb->jubilant_id;
                $meb->club_id = $last_meb->club_id;
                $meb->coa_id = $last_meb->coa_id;
                $meb->recap_date = $thru_date;
                $meb->debit = $last_meb->debit;
                $meb->credit = $last_meb->credit;
                $meb->d_or_c = $last_meb->d_or_c;
                $meb->save();
            endif;
        endforeach;

        $this->info('Generate Saldo Akhir bulan ' . Carbon::parse($from_date)->format('F Y') . ' berhasil');
    }
}
