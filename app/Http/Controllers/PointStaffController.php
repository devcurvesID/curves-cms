<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubServiceFee;
use App\Model\Locker\LockerPayment;
use App\Model\Log\LogCoachTarget;
use App\Model\Log\LogStaffPointAcquisition;
use App\Model\MasterData\Locker;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\Point;
use App\Model\MasterData\TargetCategory;
use App\Model\Payment\MemberPayment;
use App\Model\Staff\CoachTarget;
use App\Model\Staff\StaffPoint;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointStaffController extends Controller
{
    public function membership_sales_point($user_id, $membership_type_id, $member_payment_id) {
        $member = User::findOrFail($user_id);
        $staff = User::findOrFail($member->user_memberpersonal->sales_person_id);
        $membership_type = MembershipType::findOrFail($membership_type_id);
        $member_payment = MemberPayment::findOrFail($member_payment_id);

        $code = '';
        $from_this_month = new Carbon('first day of this month');

        //GET POINTS
        //Get Code
        if (($membership_type->membership_type == 'Regular') && ($membership_type->payment_type == 'Prepaid')):
            $code .= 'PP';
        elseif (($membership_type->membership_type == 'Student') && ($membership_type->payment_type == 'Prepaid')):
            $code .= 'ST';
        else:
            if ('is_vip' == 'vip'):
                $code .= 'FS';
            endif;
        endif;

        if ($membership_type->duration == '6'):
            $code .= '6';
        elseif ($membership_type->duration == '12'):
            $code .= '12';
        elseif ($membership_type->duration == '18'):
            $code .= '18';
        elseif ($membership_type->duration == '24'):
            $code .= '24';
        endif;

        //Cek Lunas / DP+Cicilan / DP / No DP
        $type_detail = '';
        if ((($member_payment->sf_amount - $member_payment->dc_amount) > $member_payment->amount_paid) && ($member_payment->payment_status != 'Paid')):
            $code .= ' <DP';
            $type_detail = '<DP';
        elseif ((($member_payment->sf_amount - $member_payment->dc_amount) == $member_payment->amount_paid) && ($member_payment->payment_status != 'Paid')):
            $code .= ' DP';
            $type_detail = 'DP';
        else:
            if ($member_payment->rest_of_bill == 0):
                $code .= ' Lunas';
                $type_detail = 'Lunas';
            else:
                //Cek CSF
                $csf = ClubServiceFee::where('club_id', '=', $member->club_id)
                    ->where('membership_type_id', '=', $membership_type->id)
                    ->where('thru_date', null)
                    ->first();

                if (($member_payment->sf_amount - $member_payment->dc_amount + $csf->mf_amount) <= $member_payment->amount_paid):
                    $code .= ' DP+Cicilan1';
                    $type_detail = 'DP+Cicilan1';
                else:
                    $code .= ' DP';
                    $type_detail = 'DP';
                endif;
            endif;
        endif;

        //Get Master Points
        $point = Point::where('name', 'ILIKE', '%'.$code.'%')
            ->where('type', '=', 'Membership')
            ->where('type_detail', '=', $type_detail)
            ->where('target_point', '=', 'Staff')
            ->first();

        //INSERT POINT TO STAFF
        if (!empty($point)):
            //Check Staff Point
            $chosen_date = $member->user_memberstatus->from_date;

            //Create Staff Points
            if (empty($staff->user_staffpoint)):
                $staff_point = new StaffPoint;
                $staff_point->current_total_point = 0;
                $staff_point->last_months_point = 0;
                $staff_point->last_two_months_point = 0;
                $staff_point->last_months_date = new Carbon('first day of '.Carbon::parse($from_this_month)->subDays(15)->format('F Y'));
                $staff_point->last_two_months_date = new Carbon('first day of '.Carbon::parse($from_this_month)->subDays(45)->format('F Y'));
                $staff_point->current_point = 0;
                $staff->user_staffpoint()->save($staff_point);

                $staff = User::findOrFail($member->user_memberpersonal->sales_person_id);
            endif;

            //Check Date, Update Staff Point
            if (!empty($staff->user_staffpoint)):
                $staff_point = $staff->user_staffpoint;
//                if (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->format('F Y')):
//                    $staff_point->current_total_point = $staff_point->current_total_point + $point->point;
//                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(15)->format('F Y')):
//                    $staff_point->last_months_point = $staff_point->last_months_point + $point->point;
//                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(45)->format('F Y')):
//                    $staff_point->last_two_months_point = $staff_point->last_two_months_point + $point->point;
//                endif;
                $staff_point->current_total_point = $staff_point->current_total_point + $point->point;
                $staff_point->current_point = $staff_point->current_point + $point->point;
                $staff->user_staffpoint()->save($staff_point);
            endif;

            //Create Log
            $log = new LogStaffPointAcquisition;
            $log->acquire_date = $chosen_date;
            $log->amount = $point->point;
            $log->user_id = $staff->id;
            $log->point_id = $point->id;
            $log->save();

            // Update Coach Target Added 25102019
            $target_category = TargetCategory::findOrFail($point->target_category_id);
            if(!empty($target_category)):
                foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                    if ($target_detail->type == 'Staff'):
                        $target = CoachTarget::where('coach_id', '=', $staff->id)
                            ->where('target_category_detail_id', '=', $target_detail->id)
                            ->where('status', '=', 'Active')
                            ->first();

                        if (!empty($target)):
                            $this->update_target($target->id, $point->point);
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
    }
	
	public function staff_point_apo($staff_id, $date)
    {
        $staff = User::findOrFail($staff_id);
        $type_detail = 'APO';
        $from_this_month = new Carbon('first day of this month');

        //Get Master Points
        $point = Point::where('name', '=', 'Make APO')
            ->where('type_detail', '=', $type_detail)->first();

        //Insert Point to Staff
        if (!empty($point)):
            $chosen_date = $date; //dari GA fa_date

            if (empty($staff->user_staffpoint)):
                $staff_point = new StaffPoint();
                $staff_point->user_id = $staff_id;
                $staff_point->current_total_point = 1;
                $staff_point->last_months_point = 0;
                $staff_point->last_two_months_point = 0;
                $staff_point->last_months_date = new Carbon('first day of ' . Carbon::parse($from_this_month)->subDays(15)->format('F Y'));
                $staff_point->last_two_months_date = new Carbon('first day of ' . Carbon::parse($from_this_month)->subDays(45)->format('F Y'));
                $staff_point->current_point = 0;
                $staff->user_staffpoint()->save($staff_point);
            endif;

            //Check date, Update Staff Point
            if (!empty($staff->user_staffpoint)):
                $staff_point = $staff->user_staffpoint;
                if (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->format('F Y')):
                    $staff_point->current_total_point = $staff_point->current_total_point + $point->point;
                    $staff_point->current_point = $staff_point->current_point + $point->point;
                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(15)->format('F Y')):
                    $staff_point->last_months_point = $staff_point->last_months_point + $point->point;
                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(45)->format('F Y')):
                    $staff_point->last_two_months_point = $staff_point->last_two_months_point + $point->point;
                endif;
                $staff->user_staffpoint()->save($staff_point);
            endif;

            //Create Log
            $log = new LogStaffPointAcquisition();
            $log->acquire_date = $chosen_date;
            $log->amount = $point->point;
            $log->user_id = $staff->id;
            $log->point_id = $point->id;
            $log->save();

            // Update Coach Target Added 25102019
            $target_category = TargetCategory::findOrFail($point->target_category_id);
            if(!empty($target_category)):
                foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                    if ($target_detail->type == 'Staff'):
                        $target = CoachTarget::where('coach_id', '=', $staff->id)
                            ->where('target_category_detail_id', '=', $target_detail->id)
                            ->where('status', '=', 'Active')
                            ->first();

                        if (!empty($target)):
                            $this->update_target($target->id, $point->point);
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
    }

    public function staff_point_apo_showup($staff_id, $date, $cek)
    {
        $staff = User::findOrFail($staff_id);
        $type_detail = 'Show Up';
        $chosen_date = $date;
        $from_this_month = new Carbon('first day of this month');

        $point = Point::where('name', '=', 'Make APO and Show Up')
            ->where('type_detail', '=', $type_detail)->first();

        //Insert Point to Staff
        if (!empty($point)):
            if ($cek == 'showup'):
                if (!empty($staff->user_staffpoint)):
                    //Check date, Update Staff Point
                    $staff_point = $staff->user_staffpoint;
                    if (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->format('F Y')):
                        $staff_point->current_total_point = $staff_point->current_total_point + $point->point;
                        $staff_point->current_point = $staff_point->current_point + $point->point;
                    elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(15)->format('F Y')):
                        $staff_point->last_months_point = $staff_point->last_months_point + $point->point;
                    elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(45)->format('F Y')):
                        $staff_point->last_two_months_point = $staff_point->last_two_months_point + $point->point;
                    endif;
                    $staff->user_staffpoint()->save($staff_point);

                    //Create Log
                    $log = new LogStaffPointAcquisition();
                    $log->acquire_date = $chosen_date;
                    $log->amount = $point->point;
                    $log->user_id = $staff->id;
                    $log->point_id = $point->id;
                    $log->save();
                endif;
            elseif ($cek == 'undo'):
                //Check date, Update Staff Point
                $staff_point = $staff->user_staffpoint;
                if (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->format('F Y')):
                    $staff_point->current_total_point = $staff_point->current_total_point - $point->point;
                    $staff_point->current_point = $staff_point->current_point + $point->point;
                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(15)->format('F Y')):
                    $staff_point->last_months_point = $staff_point->last_months_point - $point->point;
                elseif (Carbon::parse($chosen_date)->format('F Y') == Carbon::parse($from_this_month)->subDays(45)->format('F Y')):
                    $staff_point->last_two_months_point = $staff_point->last_two_months_point - $point->point;
                endif;
                $staff->user_staffpoint()->save($staff_point);

                //Create Log
                $log = new LogStaffPointAcquisition();
                $log->acquire_date = $chosen_date;
                $log->amount = $point->point * -1;
                $log->user_id = $staff->id;
                $log->point_id = $point->id;
                $log->save();
            endif;

            // Update Coach Target Added 25102019
            $target_category = TargetCategory::findOrFail($point->target_category_id);
            if(!empty($target_category)):
                foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                    if ($target_detail->type == 'Staff'):
                        $target = CoachTarget::where('coach_id', '=', $staff->id)
                            ->where('target_category_detail_id', '=', $target_detail->id)
                            ->where('status', '=', 'Active')
                            ->first();

                        if (!empty($target)):
                            $this->update_target($target->id, $point->point);
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
    }
	
	public function add_br_point($userid, $idpoint, $date, $lead)
    {
        $user = User::findOrFail($userid);
        $valuepoints= Point::findOrFail($idpoint);
        $valuepoint = $valuepoints->point;
        $from_this_month = new Carbon('first day of this month');
        if($user->role_id!=7 && $lead->is_br=='BR')
        {
            $staffpoint = StaffPoint::where('user_id','=',$userid)->first();
            if($staffpoint==null)
            {
                $staffpoint= new StaffPoint();
                $staffpoint->user_id = $userid;
                $staffpoint->current_total_point =0;
                $staffpoint->last_months_point=0;
                $staffpoint->last_two_months_point=0;
                $staffpoint->last_months_date = Carbon::today()->subMonth(1)->firstOfMonth();
                $staffpoint->last_two_months_date = Carbon::today()->subMonth(2)->firstOfMonth();
                $staffpoint->current_point = 0;
            }
            $log = new LogStaffPointAcquisition;

            if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->subDays(45)->format('F Y'))
            {
                $staffpoint->last_two_months_point += $valuepoint;

                $log->acquire_date = $date;
                $log->amount = $valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }
            else if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->subDays(15)->format('F Y'))
            {
                $staffpoint->last_months_point += $valuepoint;

                $log->acquire_date = $date;
                $log->amount = $valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }
            else if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->format('F Y'))
            {
                $staffpoint->current_total_point += $valuepoint;
                $staffpoint->current_point = $staffpoint->current_point + $valuepoint;

                $log->acquire_date = $date;
                $log->amount = $valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }

            $staffpoint->save();

            // Update Coach Target Added 25102019
            $target_category = TargetCategory::findOrFail($valuepoints->target_category_id);
            if(!empty($target_category)):
                foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                    if ($target_detail->type == 'Staff'):
                        $target = CoachTarget::where('coach_id', '=', $user->id)
                            ->where('target_category_detail_id', '=', $target_detail->id)
                            ->where('status', '=', 'Active')
                            ->first();

                        if (!empty($target)):
                            $this->update_target($target->id, $valuepoint);
                        endif;
                    endif;
                endforeach;
            endif;
        }
    }

    public function min_br_point($userid, $idpoint, $date, $lead)
    {
        $user = User::findOrFail($userid);
        $valuepoints= Point::findOrFail($idpoint);
        $valuepoint= $valuepoints->point;
        $from_this_month = new Carbon('first day of this month');
        if($user->role_id!=7 && $lead->is_br=='BR')
        {
            $staffpoint = StaffPoint::where('user_id','=',$userid)->first();
            if($staffpoint==null)
            {
                $staffpoint= new StaffPoint();
                $staffpoint->user_id = $userid;
                $staffpoint->current_total_point =0;
                $staffpoint->last_months_point=0;
                $staffpoint->last_two_months_point=0;
                $staffpoint->last_months_date = Carbon::today()->subMonth(1)->firstOfMonth();
                $staffpoint->last_two_months_date = Carbon::today()->subMonth(2)->firstOfMonth();
                $staffpoint->current_point = 0;
            }
            $log = new LogStaffPointAcquisition;

            if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->subDays(45)->format('F Y'))
            {
                $staffpoint->last_two_months_point -= $valuepoint;

                $log->acquire_date = $date;
                $log->amount = -1*$valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }
            else if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->subDays(15)->format('F Y'))
            {
                $staffpoint->last_months_point -= $valuepoint;

                $log->acquire_date = $date;
                $log->amount = -1*$valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }
            else if(Carbon::parse($date)->format('F Y')==Carbon::parse($from_this_month)->format('F Y'))
            {
                $staffpoint->current_total_point -= $valuepoint;
                $staffpoint->current_point = $staffpoint->current_point + $valuepoint;

                $log->acquire_date = $date;
                $log->amount = -1*$valuepoint;
                $log->user_id = $userid;
                $log->point_id = $idpoint;

                $log->save();
            }

            $staffpoint->save();

            // Update Coach Target Added 25102019
            $target_category = TargetCategory::findOrFail($valuepoints->target_category_id);
            if(!empty($target_category)):
                foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                    if ($target_detail->type == 'Staff'):
                        $target = CoachTarget::where('coach_id', '=', $user->id)
                            ->where('target_category_detail_id', '=', $target_detail->id)
                            ->where('status', '=', 'Active')
                            ->first();

                        if (!empty($target)):
                            $this->update_target($target->id, $valuepoint);
                        endif;
                    endif;
                endforeach;
            endif;
        }
    }

    public function locker_point($user_id, $locker_payment_id) {
        $user = User::findOrFail($user_id);
        $locker_payment = LockerPayment::findOrFail($locker_payment_id);

        if (($user->role_id != 7) && ($locker_payment->lockerpayment_locker->duration_type == 'monthly')):
            $point = Point::where('type', '=', 'Locker')
                ->where('type_detail', '=', $locker_payment->lockerpayment_locker->duration.' Months')
                ->where('target_point', '=', 'Staff')
                ->first();

            if (!empty($point)):
                if (empty($user->user_staffpoint)):
                    $staff_point = new StaffPoint;
                    $staff_point->current_total_point = $point->point;
                    $staff_point->last_months_point = 0;
                    $staff_point->last_two_months_point = 0;
                    $staff_point->last_months_date = new Carbon('first day of '.Carbon::parse($locker_payment->payment_date)->subDays(15)->format('F Y'));
                    $staff_point->last_two_months_date = new Carbon('first day of '.Carbon::parse($locker_payment->payment_date)->subDays(45)->format('F Y'));
                    $staff_point->current_point = $point->point;
                else:
                    $staff_point = $user->user_staffpoint;
                    $staff_point->current_total_point += $point->point;
                    $staff_point->current_point += $point->point;
                endif;
                $user->user_staffpoint()->save($staff_point);

                $log_staff_point = new LogStaffPointAcquisition;
                $log_staff_point->acquire_date = $locker_payment->payment_date;
                $log_staff_point->amount = $point->point;
                $log_staff_point->user_id = $user_id;
                $log_staff_point->point_id = $point->id;
                $log_staff_point->save();

                // Update Coach Target Added 25102019
                $target_category = TargetCategory::findOrFail($point->target_category_id);
                if(!empty($target_category)):
                    foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                        if ($target_detail->type == 'Staff'):
                            $target = CoachTarget::where('coach_id', '=', $user->id)
                                ->where('target_category_detail_id', '=', $target_detail->id)
                                ->where('status', '=', 'Active')
                                ->first();

                            if (!empty($target)):
                                $this->update_target($target->id, $point->point);
                            endif;
                        endif;
                    endforeach;
                endif;
            endif;
        endif;
    }

    public function add_lead_point($id) {
        $user = User::findOrFail($id);

        if ($user->role_id != 7):
            $point = Point::where('type', '=', 'NN')
                ->where('type_detail', '=', 'NN')
                ->where('target_point', '=', 'Staff')
                ->first();

            if (!empty($point)):
                if (empty($user->user_staffpoint)):
                    $staff_point = new StaffPoint;
                    $staff_point->current_total_point = $point->point;
                    $staff_point->last_months_point = 0;
                    $staff_point->last_two_months_point = 0;
                    $staff_point->last_months_date = new Carbon('first day of '.Carbon::today()->subDays(15)->format('F Y'));
                    $staff_point->last_two_months_date = new Carbon('first day of '.Carbon::today()->subDays(45)->format('F Y'));
                    $staff_point->current_point = $point->point;
                else:
                    $staff_point = $user->user_staffpoint;
                    $staff_point->current_total_point += $point->point;
                    $staff_point->current_point += $point->point;
                endif;
                $user->user_staffpoint()->save($staff_point);

                $log_staff_point = new LogStaffPointAcquisition;
                $log_staff_point->acquire_date = Carbon::now();
                $log_staff_point->amount = $point->point;
                $log_staff_point->user_id = $user->id;
                $log_staff_point->point_id = $point->id;
                $log_staff_point->save();

                $target_category = TargetCategory::findOrFail($point->target_category_id);
                if(!empty($target_category)):
                    foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                        if ($target_detail->type == 'Staff'):
                            $target = CoachTarget::where('coach_id', '=', $user->id)
                                ->where('target_category_detail_id', '=', $target_detail->id)
                                ->where('status', '=', 'Active')
                                ->first();

                            if (!empty($target)):
                                $this->update_target($target->id, $point->point);
                            endif;
                        endif;
                    endforeach;
                endif;
            endif;
        endif;
    }

    public function call_lead_point($id, $status) {
        $user = User::findOrFail($id);

        if ($user->role_id != 7):
            if (($status == 'Success') || ($status == 'APO')):
                $point = Point::where('type', '=', 'NN')
                    ->where('target_point', '=', 'Staff');

                if ($status == 'Success'):
                    $point->where('type_detail', '=', 'Active');
                elseif ($status == 'APO'):
                    $point->where('type_detail', '=', 'APO');
                endif;

                $point = $point->first();

                if (!empty($point)):
                    if (empty($user->user_staffpoint)):
                        $staff_point = new StaffPoint;
                        $staff_point->current_total_point = $point->point;
                        $staff_point->last_months_point = 0;
                        $staff_point->last_two_months_point = 0;
                        $staff_point->last_months_date = new Carbon('first day of '.Carbon::today()->subDays(15)->format('F Y'));
                        $staff_point->last_two_months_date = new Carbon('first day of '.Carbon::today()->subDays(45)->format('F Y'));
                        $staff_point->current_point = $point->point;
                    else:
                        $staff_point = $user->user_staffpoint;
                        $staff_point->current_total_point += $point->point;
                        $staff_point->current_point += $point->point;
                    endif;
                    $user->user_staffpoint()->save($staff_point);

                    $log_staff_point = new LogStaffPointAcquisition;
                    $log_staff_point->acquire_date = Carbon::now();
                    $log_staff_point->amount = $point->point;
                    $log_staff_point->user_id = $user->id;
                    $log_staff_point->point_id = $point->id;
                    $log_staff_point->save();

                    $target_category = TargetCategory::findOrFail($point->target_category_id);
                    if(!empty($target_category)):
                        foreach ($target_category->targetcategory_targecategorydetail->where('status', 'Active') as $target_detail):
                            if ($target_detail->type == 'Staff'):
                                $target = CoachTarget::where('coach_id', '=', $user->id)
                                    ->where('target_category_detail_id', '=', $target_detail->id)
                                    ->where('status', '=', 'Active')
                                    ->first();

                                if (!empty($target)):
                                    $this->update_target($target->id, $point->point);
                                endif;
                            endif;
                        endforeach;
                    endif;
                endif;
            endif;
        endif;
    }

    public function update_target($id, $point) {
        $the_target = CoachTarget::findOrFail($id);

        if ($the_target->coachtarget_targetcategorydetail->target_interval == 'Daily'):
            if (Carbon::today()->format('dmy') != Carbon::parse($the_target->updated_at)->format('dmy')):
                // Save to log_coach_targets table
                $log_coach_target = new LogCoachTarget;
                $log_coach_target->coach_id = $the_target->coach_id;
                $log_coach_target->target_category_detail_id = $the_target->target_category_detail_id;
                $log_coach_target->acquired_date = $the_target->updated_at;
                $log_coach_target->target = $the_target->target;
                $log_coach_target->acquired = $the_target->acquired;
                $log_coach_target->save();

                $the_target->acquired = $point;
            else:
                $the_target->acquired = $the_target->acquired + $point;
            endif;
        elseif ($the_target->coachtarget_targetcategorydetail->target_interval == 'Weekly'):
            $this_week = new Carbon('this week');
            if (Carbon::parse($the_target->updated_at) <= Carbon::parse($this_week)->subDay()):
                // Save to log_coach_targets table
                $log_coach_target = new LogCoachTarget;
                $log_coach_target->coach_id = $the_target->coach_id;
                $log_coach_target->target_category_detail_id = $the_target->target_category_detail_id;
                $log_coach_target->acquired_date = $the_target->updated_at;
                $log_coach_target->target = $the_target->target;
                $log_coach_target->acquired = $the_target->acquired;
                $log_coach_target->save();

                $the_target->acquired = $point;
            else:
                $the_target->acquired = $the_target->acquired + $point;
            endif;
        elseif ($the_target->coachtarget_targetcategorydetail->target_interval == 'Monthly'):
            $this_month = new Carbon('first day of this month');
            if (Carbon::parse($the_target->updated_at) < Carbon::parse($this_month)):
                // Save to log_coach_targets table
                $log_coach_target = new LogCoachTarget;
                $log_coach_target->coach_id = $the_target->coach_id;
                $log_coach_target->target_category_detail_id = $the_target->target_category_detail_id;
                $log_coach_target->acquired_date = $the_target->updated_at;
                $log_coach_target->target = $the_target->target;
                $log_coach_target->acquired = $the_target->acquired;
                $log_coach_target->save();

                $the_target->acquired = $point;
            else:
                $the_target->acquired = $the_target->acquired + $point;
            endif;
        endif;
        $the_target->save();
    }
}
