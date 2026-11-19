<?php

namespace App\Http\Controllers;

use App\Model\MasterData\Certification;
use App\Model\Staff\StaffCertification;
use App\User;
use Illuminate\Http\Request;
use Session;

class CertificationController extends Controller
{
    public function certification_list(){
        $certifications = Certification::get();

        return view('cf_lite.staff.certification-list',compact('certifications'));
    }

    public function store_certification(Request $request){

        $this->validate($request, [
            'notes' => 'sometimes',
            'certification_name' => 'required',
        ]);

        if($request->addoredit == 'add'){

            $certification = new Certification;
            $certification->certification_name = $request->certification_name;
            if(!empty($request->notes)){
                $certification->notes = $request->notes;
            }
            $certification->save();

            Session::flash('flash_message', trans('messages.certification_has_been_added'));
        }else if($request->addoredit == 'edit'){

            $certification = Certification::findOrFail($request->editid);
            $certification->certification_name = $request->certification_name;
            if(!empty($request->notes)){
                $certification->notes = $request->notes;
            }
            $certification->update();

            Session::flash('flash_message', trans('messages.certification_has_been_updated'));
        }

        return redirect('cf-lite/certification/certification-list');

    }

    public function delete_certification($id, Request $request){
        $certification = Certification::findOrFail($id);
        if(!empty($certification)){
            $count = 0;
            $staffcertification = $certification->certification_staffcertification;
            foreach ($staffcertification as $sc ) {
                $count++;
            }
            if($count == 0){
                $certification->delete();
                Session::flash('flash_message', trans('messages.certification_has_been_deleted'));
            }
        }

        return redirect('cf-lite/certification/certification-list');
    }

    //Staff Certification
    public function staff_certification($id, Request $request){
        $staff_certifications = StaffCertification::where('staff_id','=',$id)->get();
        $staff = User::findOrFail($id);
        $certifications = Certification::pluck('certification_name','id');

        return view('cf_lite/staff/staff-certification-detail', compact('staff_certifications','staff','certifications'));
    }

    public function store_staff_certification($id, Request $request){
        $this->validate($request, [
            'from_date' => 'required_with:thru_date',
            'thru_date' => 'required_with:from_date',
            'certification_number' => 'required',
            'certification' => 'required',
        ]);

        $staff_certificate = new StaffCertification;
        $staff_certificate->staff_id = $id;
        $staff_certificate->certification_id = $request->certification;
        $staff_certificate->certification_number = $request->certification_number;
        $staff_certificate->level = '1';
        if(!empty($request->from_date) && !empty($request->thru_date)){
            $staff_certificate->start_date = $request->from_date;
            $staff_certificate->thru_date = $request->thru_date;
        }
        $staff_certificate->save();
        Session::flash('flash_message', trans('messages.staff_certification_has_been_added'));

        return redirect('cf-lite/certification/staff-certification-detail/'.$id);
    }

    public function delete_staff_certification($id, Request $request){
        $certification = StaffCertification::findOrFail($id);
        $certification->delete();
        Session::flash('flash_message', trans('messages.staff_certification_has_been_deleted'));

        return redirect('cf-lite/certification/staff-certification-detail/'.$request->staff_id);
    }
}
