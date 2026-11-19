<?php

namespace App\Http\Controllers;

use App\Model\MasterData\TargetCategory;
use App\Model\Staff\CoachTarget;
use App\Model\MasterData\TargetCategoryDetail;
use App\User;
use Illuminate\Http\Request;
use Session;

class CoachTargetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get_target_category() {
        $all_targets = TargetCategory::orderBy('category_name', 'asc')->get();

        return view('cf_lite.coach-target.index', compact('all_targets'));
    }

    // Ajax Target Category Status
    public function edit_status_target_category_ajax(Request $request) {
        $target_category = TargetCategory::find($request->target_id);

        if ($request->check == 'true'):
            $target_category->status = 'Active';
        elseif ($request->check == 'false'):
            $target_category->status = 'Inactive';
        endif;

        if ($target_category->save()):
            $detail = TargetCategoryDetail::where('target_category_id', '=', $target_category->id)->count();
            if ($detail == 0):
                $this->sync_detail($target_category->id, $target_category->status);
            else:
                $this->sync_detail($target_category->id, $target_category->status);
            endif;
        endif;

        return response()->json($target_category);
    }

    // Ajax Target Category Detail Status Change
    public function edit_status_target_category_detail_ajax(Request $request){
        $detail = TargetCategoryDetail::find($request->detail_id);

        if ($request->check == 'true'):
            $detail->status = 'Active';
        elseif ($request->check == 'false'):
            $detail->status = 'Inactive';
        endif;

        if ($detail->save()):
            $coach_targets = CoachTarget::where('target_category_detail_id', '=', $detail->id)->get();
            foreach ($coach_targets as $coach_target):
                $coach_target->status = $detail->status;
                $coach_target->save();
            endforeach;
        endif;

        return response()->json($detail);
    }

    // Ajax Target Category Detail Get Point Target
    public function edit_point_target_ajax(Request $request){
        $detail = TargetCategoryDetail::find($request->detail_id);
        return response()->json($detail);
    }

    public function edit_status_coach_target(Request $request) {
        $coach = CoachTarget::find($request->coach_target_id);

        if ($request->check == 'true'):
            $coach->status = 'Active';
        elseif ($request->check == 'false'):
            $coach->status = 'Inactive';
        endif;

        $coach->save();

        return response()->json($coach);
    }

    // Target Category Detail
    public function target_category_detail($id){
        $details = TargetCategoryDetail::where('target_category_id', '=', $id)
            ->groupBy('type', 'id')
            ->get();
        if ($details->first()):
            return view('cf_lite.coach-target.target-category-detail', compact('details'));
        else:
            Session::flash('flash_message',trans('messages.category_target_does_not_have_detail'));
            return redirect('cf-lite/coach-target');
        endif;
    }

    // Target Category Detail Update Point
    public function update_point_target($id, Request $request){
        $detail = TargetCategoryDetail::find($request->detail_id);
        $detail->target = $request->target;
        if ($detail->save()):
            $coach_targets = CoachTarget::where('target_category_detail_id', '=', $detail->id)->get();
            foreach ($coach_targets as $coach_target):
                $coach_target->target = $detail->target;
                $coach_target->save();
            endforeach;
            Session::flash('flash_message',trans('messages.target_point_successfully_updated'));
        else:
            Session::flash('flash_message',trans('messages.failed_to_update_target_point'));
        endif;

        return redirect('cf-lite/coach-target/'.$id.'/detail');
    }

    // Setting Coach Target
    public function setting_coach_target($id){
        $staff = User::find($id);
        $coach_target = CoachTarget::where('coach_id', '=', $id)
            ->whereHas('coachtarget_targetcategorydetail', function ($q){
                $q->where('type', '=', 'Staff');
            })
            ->groupBy('target_category_detail_id', 'id')
            ->get();
        if ($coach_target->first()):
            return view('cf_lite.coach-target.setting-coach-target', compact('staff','coach_target'));
        else:
            Session::flash('flash_message',trans('messages.set_coach_target_first'));
            return redirect('cf-lite/staff/'.$id.'/detail');
        endif;
    }

    // Generate Coach Target and Change Status Global to Detail
    public function sync_detail($id, $status){
        $cek_detail = TargetCategoryDetail::where('target_category_id', '=', $id)->count();
        if ($cek_detail == 0):
            $details = new TargetCategoryDetail();
            $details->target_category_id = $id;
            $details->target_interval = 'Daily';
            $details->target = 0;
            $details->type = 'Staff';
            $details->status = $status;
            $details->save();

            $details = new TargetCategoryDetail();
            $details->target_category_id = $id;
            $details->target_interval = 'Weekly';
            $details->target = 0;
            $details->type = 'Staff';
            $details->status = $status;
            $details->save();

            $details = new TargetCategoryDetail;
            $details->target_category_id = $id;
            $details->target_interval = 'Monthly';
            $details->target = 0;
            $details->type = 'Staff';
            $details->status = $status;
            $details->save();

            $details = new TargetCategoryDetail();
            $details->target_category_id = $id;
            $details->target_interval = 'Daily';
            $details->target = 0;
            $details->type = 'Club';
            $details->status = $status;
            $details->save();

            $details = new TargetCategoryDetail();
            $details->target_category_id = $id;
            $details->target_interval = 'Weekly';
            $details->target = 0;
            $details->type = 'Club';
            $details->status = $status;
            $details->save();

            $details = new TargetCategoryDetail;
            $details->target_category_id = $id;
            $details->target_interval = 'Monthly';
            $details->target = 0;
            $details->type = 'Club';
            $details->status = $status;
            $details->save();

            $coachs = User::where('role_id', '<>', 7)
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active');
                })
                ->get();
            $category_details = TargetCategoryDetail::where('target_category_id', '=', $id)
                ->where('status', '=', 'Active')
                ->where('type', '=', 'Staff')
                ->get();

            foreach ($coachs as $coach):
                foreach ($category_details as $category_detail):
                    $coach_target = new CoachTarget();
                    $coach_target->coach_id = $coach->id;
                    $coach_target->target_category_detail_id = $category_detail->id;
                    $coach_target->target = $category_detail->target;
                    $coach_target->acquired = 0;
                    $coach_target->status = $status;
                    $coach_target->save();
                endforeach;
            endforeach;

        elseif ($cek_detail > 0):
            $details = TargetCategoryDetail::where('target_category_id', '=', $id)->get();

            foreach ($details as $detail):
                $detail->status = $status;
                if ($detail->save()):
                    $coach_target = CoachTarget::where('target_category_detail_id', '=', $detail->id)->get();
                    foreach ($coach_target as $coach):
                        $coach->status = $detail->status;
                        $coach->save();
                    endforeach;
                endif;
            endforeach;
        endif;
    }
}
