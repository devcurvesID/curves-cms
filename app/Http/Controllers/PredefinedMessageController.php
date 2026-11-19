<?php

namespace App\Http\Controllers;

use Session;
use App\Model\MasterData\PredefinedMessage;
use Illuminate\Http\Request;

class PredefinedMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function predefined_message_list() {
        $messages = PredefinedMessage::orderBy('created_at', 'desc')->paginate(25);

        return view('cf_lite.message.predefined-message-list', compact('messages'));
    }

    public function store_predefined_message(Request $request) {
        $this->validate($request, [
            'predefined_message' => 'required|string|max:255|unique:mst_predefined_messages,message'
        ]);

        $predefined_message = new PredefinedMessage;
        $predefined_message->message = $request->predefined_message;
        $predefined_message->save();

        Session::flash('flash_message', trans('messages.predefined_message_has_been_added'));
        return redirect('cf-lite/predefined-message');
    }

    public function update_predefined_message(Request $request) {
        $this->validate($request, [
            'predefined_message_edit' => 'required|string|max:255|unique:mst_predefined_messages,message,'.$request->m_attribute,
            'm_attribute' => 'required',
        ]);

        $predefined_message = PredefinedMessage::findOrFail($request->m_attribute);
        $predefined_message->message = $request->predefined_message_edit;
        $predefined_message->save();

        Session::flash('flash_message', trans('messages.predefined_message_has_been_updated'));
        return redirect('cf-lite/predefined-message');
    }

    public function delete_predefined_message($id) {
        $predefined_message = PredefinedMessage::findOrFail($id);

        $predefined_message->delete();

        Session::flash('flash_message', trans('messages.predefined_message_has_been_deleted'));
        return redirect('cf-lite/predefined-message');
    }
}
