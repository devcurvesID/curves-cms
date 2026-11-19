<?php

namespace App\Http\Controllers;

use App\Model\Curves\Advertise\Publicity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Services\ApiClientService;

class PublicityController extends Controller
{
    public function index(){
        $publicity = Publicity::orderBy('from_date', 'desc')->get();
        return view('cf_lite.publicity.index', compact('publicity'));
    }

    public function new_publicity(){
        return view('cf_lite.publicity.new-publicity');
    }

    public function store_publicity(Request $request){
        $this->validate($request, [
            'photo' => 'required',
            'headline' => 'required',
            'from_date' => 'required',
            'thru_date' => 'required',
        ]);

        $publish = new Publicity();

        if ($request->hasFile('photo')) {
            $path = 'images/publicities';
            $publish->photo = $this->uploadPhoto($path ,$request);
        }

        $publish->headline = $request->headline;
        $publish->from_date = $request->from_date;
        $publish->thru_date = $request->thru_date;
        if (!empty($request->note)):
            $publish->note = $request->note;
        endif;

        if ($request->status == true):
            $publish->status = 'publish';
        else:
            $publish->status = 'draft';
        endif;

        if ($publish->save()):

            // Jonviter DEV
			$publicityData = $publish->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/publicity", $publicityData);
			// Jonviter DEV

            Session::flash('flash_message', trans('messages.publicity_successfully_added'));
            return redirect('cf-lite/publicity');
        else:
            Session::flash('error_message', trans('messages.failed_to_add_publicity'));
            return redirect('cf-lite/publicity/new-publicity');
        endif;
    }

    public function edit_publicity($id){
        $publicity = Publicity::find($id);
        return view('cf_lite.publicity.edit-publicity', compact('publicity'));
    }

    public function update_publicity($id, Request $request){
        $this->validate($request, [
            'headline' => 'required',
            'from_date' => 'required',
            'thru_date' => 'required',
        ]);

        $publicity = Publicity::find($id);

        $name = explode(".", $publicity->photo);

        if (!empty($request->photo)):
            if ($request->hasFile('photo')):
                $path = 'images/publicities';
                $publicity->photo = $this->updatePhoto($path ,$request, $name[0]);
            endif;
        endif;

        $publicity->headline = $request->headline;
        $publicity->from_date = $request->from_date;
        $publicity->thru_date = $request->thru_date;
        if (!empty($request->note)):
            $publicity->note = $request->note;
        endif;

        if ($request->status == true):
            $publicity->status = 'publish';
        else:
            $publicity->status = 'draft';
        endif;

        if ($publicity->save()):
            // Jonviter DEV
			$publicityData = $publicity->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/publicity", $publicityData);
			// Jonviter DEV
            Session::flash('flash_message', trans('messages.publicity_successfully_updated'));
//            return redirect('cf-lite/publicity');
        else:
            Session::flash('error_message', trans('messages.failed_to_update_publicity'));
//            return redirect('cf-lite/publicity');
        endif;

        return redirect('cf-lite/publicity');
    }

    public function delete_publicity($id){
        $publicity = Publicity::findOrFail($id);
        try{
            $publicity->delete();
            Session::flash('flash_message',trans('messages.publicity_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_publicity'));
        }

        return redirect('cf-lite/publicity');
    }

    public function ajax_status(Request $request){
        $publicity = Publicity::find($request->publicity_id);

        if ($publicity->status == 'draft'):
            $publicity->status = 'publish';
            $publicity->save();
        elseif ($publicity->status == 'publish'):
            $publicity->status = 'draft';
            $publicity->save();
        endif;

        return response()->json($publicity);
    }

    private function uploadPhoto($path, Request $request)
    {
        $photo = $request->file('photo');
        $ext = $photo->getClientOriginalExtension();

        if ($request->file('photo')->isValid()) {
            $photo_name = date('YmdHis') . ".$ext";
            $upload_path = $path;
            $request->file('photo')->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }

    private function updatePhoto($path, Request $request, $name)
    {
        $photo = $request->file('photo');
        $ext = $photo->getClientOriginalExtension();

        if ($request->file('photo')->isValid()) {
            $photo_name = $name . ".$ext";
            $upload_path = $path;
            $request->file('photo')->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }
}
