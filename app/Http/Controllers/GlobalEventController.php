<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Event\GlobalEvent;
use App\Model\Club\ClubInfo;
use Carbon\Carbon;
use Session;

class GlobalEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

	public function global_event_club_list() {
        $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->paginate(12);

        return view('cf_lite.event.global-event-club-list', compact('clubs'));
    }

    //CF LITE
    public function global_events_list($id, Request $request) {
        //Menampilkan Appointment di kalender
        if (!empty($request->date_chosen)):
            $date = Carbon::parse($request->date_chosen);
        else:
            $date = Carbon::today();
        endif;

        $all_events = ClubInfo::find($id)->club_globalevent()->get();

        $global_events = ClubInfo::find($id)->club_globalevent()->whereRaw('? BETWEEN from_date AND thru_date',[$date])->get();

        $clubs = ClubInfo::all();

        return view('cf_lite.event.global-event-list', compact('global_events','all_events', 'clubs', 'date','id'));
    }

    public function global_events_date_list($date) {
    	if(empty($date)){
    		$date = Carbon::now();
    	}
        $global_events = GlobalEvent::whereBetween($date,['from_date','thru_date'])->orderBy('global_event', 'asc')->paginate(10);

        return view('cf_lite.event.global-event-list', compact('global_events'));
    }

    public function store_global_event(Request $request){

        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'max_taken' => 'required|string',
            'point' => 'required|string',
            'from_date' => 'required|date',
            'thru_date' => 'required|date|after_or_equal:from_date',
        ]);

    	$input = $request->all();

    	if($request->addoredit == 'add'){

	    	// $global_event = new GlobalEvent;
	     //    $global_event->title = $request->title;
	     //    $global_event->description = $request->description;
	     //    $global_event->max_taken = $request->max_taken;
	     //    $global_event->point = $request->point;
	     //    $global_event->from_date = $request->from_date;
	     //    $global_event->thru_date = $request->thru_date;

	     //    $global_event->save();

	    	$global_event = GlobalEvent::create($input);

	        $global_event->globalevent_club()->attach($request->input('clubs'));


	        Session::flash('flash_message', trans('messages.global_event_create_message'));
    	}else if($request->addoredit == 'edit'){
    		$global_event = GlobalEvent::findOrFail($request->editid);
	        $global_event->title = $request->title;
	        $global_event->description = $request->description;
	        $global_event->max_taken = $request->max_taken;
	        $global_event->point = $request->point;
	        $global_event->from_date = $request->from_date;
	        $global_event->thru_date = $request->thru_date;
	    	$global_event->update();

	    	$global_event->globalevent_club()->sync($request->input('clubs'));

	        Session::flash('flash_message', trans('messages.global_event_update_message'));
    	}
        return redirect()->route('cf-lite.event.global-event-list',['id' => $request->club_id]);
    }

    public function delete_global_event($id){
    	$global_event = GlobalEvent::findOrFail($id);
	    $global_event->globalevent_club()->detach();
    	$global_event->delete();

        Session::flash('flash_message', trans('messages.global_event_delete_message'));

        return redirect()->route('cf-lite.event.global-event-list',['id' => $request->club_id]);
    }
}
