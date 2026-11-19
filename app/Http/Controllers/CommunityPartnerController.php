<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Model\CommunityPartner\CommunityPartner;
use App\Model\CommunityPartner\CommunityPartnerMessage;

class CommunityPartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function choose_cp_menu()
    {
        return view('cf_lite.community-partner.community-partner-choose-menu');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cp_list()
    {
        $cps = CommunityPartner::paginate(100);

        return view('cf_lite.community-partner.community-partner-list',compact('cps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cp_list_filter(Request $request)
    {
        $cps = CommunityPartner::where('cp_name','like','%'.$request->cp_name.'%')->paginate(100);

        return view('cf_lite.community-partner.community-partner-list',compact('cps'));
    }

    public function store_cp(Request $request)
    {
        $this->validate($request, [
            'cp_name' => 'required|string',
            'pic_name' => 'required|string',
            'address' => 'required|string',
            'status' => 'required',
        ]);
        
        if($request->addoredit == 'add'){
            $cp = new CommunityPartner;
            $cp->cp_name = $request->cp_name;
            $cp->pic_name = $request->pic_name;
            $cp->address = $request->address;
            $cp->phone_number = $request->phone_number;
            $cp->email = $request->email;
            $cp->status = $request->status;
            $cp->save();

            Session::flash('flash_message', trans('messages.community_partner_has_been_added'));

        }else if($request->addoredit == 'edit'){
            $newCP = CommunityPartner::findOrFail($request->editid);
            $newCP->cp_name = $request->cp_name;
            $newCP->pic_name = $request->pic_name;
            $newCP->address = $request->address;
            $newCP->phone_number = $request->phone_number;
            $newCP->email = $request->email;
            $newCP->status = $request->status;

            $newCP->save();

            Session::flash('flash_message', trans('messages.community_partner_has_been_updated'));

        }

        return redirect('cf-lite/community-partner/community-partner-list');
    }

    // Community Partner Message
    public function cpm_list()
    {
        $cpms = CommunityPartnerMessage::paginate(100);
        $cps  = CommunityPartner::get();

        return view('cf_lite.community-partner.community-partner-message-list',compact('cpms','cps'));
    }

    public function delete_cpm($id)
    {
        $global_message = CommunityPartnerMessage::findOrFail($id);
        $global_message->delete();

        Session::flash('flash_message', trans('messages.community_partner_message_has_been_deleted'));

        return redirect('cf-lite/community-partner/community-partner-message-list');
    }

    public function store_cpm(Request $request)
    {
        $this->validate($request, [
            'cps' => 'required',
            'message' => 'required|string',
            'from_date' => 'required|date',
            'thru_date' => 'required|date|after_or_equal:from_date',
        ]);
        
        if($request->addoredit == 'add'){
            $cpm = new CommunityPartnerMessage;
            $cpm->cp_id = $request->cps;
            $cpm->message = $request->message;
            $cpm->from_date = $request->from_date;
            $cpm->thru_date = $request->thru_date;
            $cpm->save();

            Session::flash('flash_message', trans('messages.community_partner_message_has_been_added'));

        }else if($request->addoredit == 'edit'){
            $newCPM = CommunityPartnerMessage::findOrFail($request->editid);
            $newCPM->cp_id = $request->cps;
            $newCPM->message = $request->message;
            $newCPM->from_date = $request->from_date;
            $newCPM->thru_date = $request->thru_date;
            $newCPM->save();

            Session::flash('flash_message', trans('messages.community_partner_message_has_been_updated'));

        }

        return redirect('cf-lite/community-partner/community-partner-message-list');
    }
}
