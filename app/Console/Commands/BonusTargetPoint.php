<?php

namespace App\Console\Commands;

use App\Model\Log\LogStaffPointAcquisition;
use App\Model\MasterData\Point;
use App\Model\Staff\CoachTarget;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BonusTargetPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:bonus_target_point';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menambahkan bonus point untuk setiap target HARIAN yang tercapai';

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
        $coach_targets = CoachTarget::where('status', '=', 'Active')
            ->whereHas('coachtarget_targetcategorydetail', function ($tc_detail) {
                $tc_detail->where('target_interval', '=', 'Daily')
                    ->where('status', '=', 'Active');
            })
            ->get();

        foreach ($coach_targets as $coach_target):
            if ($coach_target->acquired >= $coach_target->target):
                $coach = CoachTarget::findOrFail($coach_target->id);
                $user = User::findOrFail($coach->coach_id);

                $point = Point::where('type', '=', 'Target')
                    ->where('type_detail', '=', 'Target '.$coach->coachtarget_targetcategorydetail->targetcategorydetail_targetcategory->category_name)
                    ->where('target_point', '=', 'Staff')
                    ->first();

                if (!empty($point)):
                    $coach->acquired = $coach->acquired + $point->point;
                    $coach->save();

                    $log_staff_point = new LogStaffPointAcquisition;
                    $log_staff_point->acquire_date = Carbon::yesterday();
                    $log_staff_point->amount = $point->point;
                    $log_staff_point->user_id = $user->id;
                    $log_staff_point->point_id = $point->id;
                    $log_staff_point->save();

                    $staff_point = $user->user_staffpoint;
                    $staff_point->current_total_point += $point->point;
                    $staff_point->current_point += $point->point;
                    $user->user_staffpoint()->save($staff_point);
                endif;
            endif;
        endforeach;

        $this->info('Set Bonus Point BERHASIL!');
    }
}
