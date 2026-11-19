<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\MasterData\Challenge;
use Session;
use App\Services\ApiClientService;

class ChallengesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function challenges_list(){
    	$challenges = Challenge::paginate(100);

    	return view('cf_lite.challenges.challenges-list', compact('challenges'));
    }

    public function challenges_list_filter(Request $request) {
        $challenges = Challenge::whereNotNull('challenge');

        if (!empty($request->challenge_name)):
            $challenges->where('challenge', 'LIKE', '%'.$request->challenge_name.'%');
        else:
            $counter = 0;
            if (!empty($request->day)):
                $challenges->where('type', '=', $request->day);
                $counter++;
            endif;

            if (!empty($request->workout)):
                if ($counter > 0):
                    $challenges->orWhere('type', '=', $request->workout);
                else:
                    $challenges->where('type', '=', $request->workout);
                endif;
                $counter++;
            endif;

            if (!empty($request->br)):
                if ($counter > 0):
                    $challenges->orWhere('type', '=', $request->br);
                else:
                    $challenges->where('type', '=', $request->br);
                endif;
            endif;
        endif;

        $challenges = $challenges->orderBy('id', 'asc')->paginate(100);

        return view('cf_lite.challenges.challenges-list', compact('challenges'));
    }

    public function store_challenges(Request $request){
        $this->validate($request, [
            'challenge' => 'required|string',
            'type' => 'required',
            'sequence' => 'required',
            'variable' => 'required|integer',
            'variable_target' => 'required|integer',
            'status' => 'required',
        ]);
        
    	if($request->addoredit == 'add'){
	    	$challenge = new Challenge;
	        $challenge->challenge = $request->challenge;
	        $challenge->type = $request->type;
	        $challenge->variable = $request->variable;
	        if($request->sequence == 'true'){
	        	$challenge->sequencable = 'yes';
	        	$challenge->sequence = $this->findSequence($challenge->type , $challenge->variable);
	        }else{
	        	$challenge->sequencable = 'no';
	        	$challenge->sequence = 1;
	        }
	        $challenge->variable_target = $request->variable_target;
	        $challenge->status = $request->status;

	        // Added 16102019
            if ($request->hasFile('photo')) {
                $path = 'images/rewards';
                $challenge->picture = $this->uploadPhoto($path ,$request);
            }

	        $challenge->save();
			// Jonviter DEV
			$challengeData = $challenge->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/challenge", $challengeData);
			// Jonviter DEV

	        Session::flash('flash_message', trans('messages.challenge_has_been_added'));

	        return redirect('cf-lite/challenges/challenges-list');
    	}else if($request->addoredit == 'edit'){
    		$prevChallenge = Challenge::findOrFail($request->editid);
    		$newChallenge = Challenge::findOrFail($prevChallenge->id);
    		$newChallenge->challenge = $request->challenge;
    		$newChallenge->type = $request->type;
    		$newChallenge->variable = $request->variable;
    		$newChallenge->variable_target = $request->variable_target;
    		
    		if($request->sequence == 'true' && $prevChallenge->sequencable == 'yes'){
	        	$newChallenge->sequencable = 'yes';
    			if($newChallenge->type != $prevChallenge->type){
	        		$newChallenge->sequence = $this->findSequence($newChallenge->type , $newChallenge->variable);
	        		$this->reduceRestSequence($prevChallenge->type, $prevChallenge->sequence);
    			}
	        }else if($request->sequence == 'true' && $prevChallenge->sequencable == 'no'){
	        	$newChallenge->sequencable = 'yes';
	        	$newChallenge->sequence = $this->findSequence($newChallenge->type , $newChallenge->variable);
	        }else if($request->sequence == 'false' && $prevChallenge->sequencable == 'yes'){
	        	$newChallenge->sequencable = 'no';
	        	$newChallenge->sequence = 1;
	        	$this->reduceRestSequence($prevChallenge->type, $prevChallenge->sequence);
	        }else if($request->sequence == 'false' && $prevChallenge->sequencable == 'no'){
	        	$newChallenge->sequencable = 'no';
	        	$newChallenge->sequence = 1;
	        }

	        $newChallenge->status = $request->status;

            // Added 16102019
            if ($request->hasFile('photo')) {
                $path = 'images/rewards';
                $newChallenge->picture = $this->uploadPhoto($path ,$request);
            }

	        $newChallenge->save();

			// Jonviter DEV
			$challengeData = $newChallenge->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/challenge", $challengeData);
			// Jonviter DEV
			
	        Session::flash('flash_message', trans('messages.challenge_has_been_updated'));

	        return redirect('cf-lite/challenges/challenges-list');
    	}
    }

    public function findSequence($type, $variable){
    	$sequenceResult = 0;

    	$challengeList = Challenge::where('type','=',$type)->where('variable','>',$variable)->where('sequencable','=','yes')->orderBy('sequence','asc');

    	if(count($challengeList->get()) > 0){
    		echo "jumlah : ".count($challengeList->get());
    		echo $challengeList->get();
    		foreach($challengeList->get() as $challenge){
    			$newChallenge = Challenge::findOrFail($challenge->id);
    			$newChallenge->sequence = $challenge->sequence +1;
    			$newChallenge->save();
    		}
    		$sequenceResult = $challengeList->first()->sequence - 1;
    	}else{
    		$challengeList = Challenge::where('type','=',$type)->orderBy('sequence','asc');
    		foreach($challengeList->get() as $challenge){
    			$sequenceResult = $challenge->sequence;
    		}
    		$sequenceResult = $sequenceResult + 1;
    	}

    	return $sequenceResult;
    }

    public function reduceRestSequence($type, $sequence){
    	$challengeList = Challenge::where('type','=',$type)->where('sequence','>',$sequence)->where('sequencable','=','yes')->orderBy('sequence','asc');

    	if(count($challengeList->get()) > 0){
    		echo "jumlah : ".count($challengeList->get());
    		echo $challengeList->get();
    		foreach($challengeList->get() as $challenge){
    			$newChallenge = Challenge::findOrFail($challenge->id);
    			$newChallenge->sequence = $challenge->sequence - 1;
    			$newChallenge->save();
    		}
    	}
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
}
