<?php

namespace App\Console\Commands;

use App\Model\Staff\StaffPointHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StaffPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:staff_point';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekap poin staff ke tabel staff_points_histories, lalu reset current_point di tabel staff_points menjadi 0.';

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

        $staff_points = \App\Model\Staff\StaffPoint::select('user_id', 'current_point')->get();
        if (!empty($staff_points)):
            if (count($staff_points) > 0):
                foreach ($staff_points as $staff_point):
                    // Create Staff Point History
                    $sp_history = new StaffPointHistory;
                    $sp_history->user_id = $staff_point->user_id;
                    $sp_history->from_date = $from_date;
                    $sp_history->thru_date = $thru_date;
                    $sp_history->total_point = $staff_point->current_point;
                    $sp_history->save();

                    // Update Staff Point
                    $user = User::findOrFail($staff_point->user_id);
                    $sp = $user->user_staffpoint;
                    $sp->current_point = 0;
                    $user->user_staffpoint()->save($sp);
                endforeach;
            endif;
        endif;

        $this->info('Reset Point BERHASIL!');
    }
}
