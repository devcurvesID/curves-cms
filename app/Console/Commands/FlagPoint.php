<?php

namespace App\Console\Commands;

use App\Model\Log\LogStaffPointAcquisition;
use App\Model\MasterData\Point;
use App\Model\Staff\CoachTarget;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FlagPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:flag_point';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bonus Point yang di-generate setiap awal bulan';

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

        $points = Point::select('name', 'point')
            ->where('type', '=', 'Flag')
            ->where('target_point', '=', 'Staff')
            ->get();

        $flagA = 0;
        $flagB = 0;
        $flagC = 0;
        $flagD = 0;
        $flagE = 0;
        foreach ($points as $point):
            if ($point->name == 'Flag A'):
                $flagA = $point->point;
            elseif ($point->name == 'Flag B'):
                $flagB = $point->point;
            elseif ($point->name == 'Flag C'):
                $flagC = $point->point;
            elseif ($point->name == 'Flag D'):
                $flagD = $point->point;
            elseif ($point->name == 'Flag E'):
                $flagE = $point->point;
            endif;
        endforeach;

        $coaches = User::where('role_id', '<>', '7')
            ->whereHas('user_memberstatus', function ($user) {
                $user->where('status', '=', 'Active');
            })
            ->whereHas('coachuser_coachmember')
            ->get();

        $tulisan = '';
        foreach ($coaches as $coach):
            $bonus_point = 0;
            foreach ($coach->coachuser_coachmember as $coachmember):
                if (($coachmember->coachmember_memberuser->user_memberstatus->status == 'Active') || ($coachmember->coachmember_memberuser->user_memberstatus->status == 'Hold')):
                    if ($coachmember->coachmember_memberuser->user_memberpersonal->flag == 'A'):
                        $bonus_point += $flagA;
                    elseif ($coachmember->coachmember_memberuser->user_memberpersonal->flag == 'B'):
                        $bonus_point += $flagB;
                    elseif ($coachmember->coachmember_memberuser->user_memberpersonal->flag == 'C'):
                        $bonus_point += $flagC;
                    elseif ($coachmember->coachmember_memberuser->user_memberpersonal->flag == 'D'):
                        $bonus_point += $flagD;
                    elseif ($coachmember->coachmember_memberuser->user_memberpersonal->flag == 'E'):
                        $bonus_point += $flagE;
                    endif;
                endif;
            endforeach;
$tulisan .= $bonus_point.'-';
            // Insert Point to Coach Target
            $coach_targets = CoachTarget::where('status', '=', 'Active')
                ->where('coach_id', '=', $coach->id)
                ->whereHas('coachtarget_targetcategorydetail', function ($tc_detail) {
                    $tc_detail->where('target_interval', '=', 'Monthly')
                        ->where('type', '=', 'Staff')
                        ->whereHas('targetcategorydetail_targetcategory', function ($target) {
                            $target->where('category_name', '=', 'Flag');
                        });
                })
                ->get();

            if (!empty($coach_targets)):
                foreach ($coach_targets as $coach_target):
                    $tulisan .= $coach_target->id.', ';
                    $target = CoachTarget::findOrFail($coach_target->id);
                    $target->acquired = $target->acquired + $bonus_point;
                    $target->save();
                endforeach;
            endif;
        endforeach;

        $this->info('Update Flag Point BERHASIL!'.$tulisan);
    }
}
