<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Flag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:flag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kalkulasi ulang jumlah WO untuk memperbaharui Flag anggota';

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
        $from_date = new Carbon('first day of this month');
        $thru_date = new Carbon('last day of this month');

        $this->info($from_date);
        $this->info($thru_date);

        $members = User::selectRaw('users.id, COUNT(workouts.user_id) AS total_wo')
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Hold');
            })
            ->leftJoin('workouts', function ($left) use ($from_date, $thru_date) {
                $left->on('users.id', '=', 'workouts.user_id');
                $left->whereDate('workouts.workout_date', '>=', \Carbon\Carbon::parse($from_date)->toDateString())
                    ->whereDate('workouts.workout_date', '<=', \Carbon\Carbon::parse($thru_date)->toDateString());
            })
            ->groupBy('users.id')
            ->get();

        if (!empty($members)):
            if (count($members) > 0):
                foreach ($members as $member):
                    $the_member = User::find($member->id);
                    if (!empty($the_member)):
                        $member_personal = $the_member->user_memberpersonal;
                        if ($member->total_wo >= 12):
                            $flag = 'A';
                        elseif (($member->total_wo < 12) && ($member->total_wo >= 8)):
                            $flag = 'B';
                        elseif (($member->total_wo < 8) && ($member->total_wo >= 4)):
                            $flag = 'C';
                        elseif (($member->total_wo < 4) && ($member->total_wo >= 1)):
                            $flag = 'D';
                        elseif ($member->total_wo == 0):
                            $flag = 'E';
                        endif;
                        $member_personal->flag = $flag;
                        $member->user_memberpersonal()->save($member_personal);
                    endif;
                endforeach;
            endif;
        endif;

        $this->info('Cron Berhasil');
    }
}
