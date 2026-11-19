<?php

namespace App\Http\Controllers;

use App\Model\Member\GlobalMessage;
use App\Model\Club\ClubInfo;
use Illuminate\Http\Request;
use Session;

class GlobalMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function global_message_list()
    {
        $global_messages = GlobalMessage::orderBy('from_date', 'asc')->paginate(10);

        $clubs = ClubInfo::all();

        return view('cf_lite.member.global-message-list', compact('global_messages', 'clubs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_global_message(Request $request)
    {
        $this->validate($request, [
            'clubs' => 'required',
            'message' => 'required|string',
            'from_date' => 'required|date',
            'thru_date' => 'required|date|after_or_equal:from_date',
        ]);
        $input = $request->all();

        if($request->addoredit == 'add'){

            $global_message = GlobalMessage::create($input);

            $global_message->globalmessage_club()->attach($request->input('clubs'));


            Session::flash('flash_message', trans('messages.global_message_create_message'));
        }else if($request->addoredit == 'edit'){
            $global_message = GlobalMessage::findOrFail($request->editid);
            $global_message->message = $request->message;
            $global_message->from_date = $request->from_date;
            $global_message->thru_date = $request->thru_date;
            $global_message->update();

            $global_message->globalmessage_club()->sync($request->input('clubs'));

            Session::flash('flash_message', trans('messages.global_message_update_message'));
        }
        return redirect()->route('cf-lite.member.global-message-list');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GlobalMessage  $globalMessage
     * @return \Illuminate\Http\Response
     */
    public function delete_global_message($id, Request $request)
    {
        $global_message = GlobalMessage::findOrFail($id);
        $global_message->globalmessage_club()->detach();
        $global_message->delete();

        Session::flash('flash_message', trans('messages.global_message_delete_message'));

        return redirect()->route('cf-lite.member.global-message-list');
    }
}
