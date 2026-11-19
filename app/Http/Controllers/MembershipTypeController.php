<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Model\MasterData\MembershipType;
use Illuminate\Support\Facades\Auth;

use App\AccurateAuth;
use App\Helpers\Accurate;
use App\Services\ApiClientService;

class MembershipTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function membership_type_list(){
    	$membershipTypes = MembershipType::paginate(100);

    	$optMembershipType = ['Regular'=> 'Regular','Student'=>'Student','Corporate'=>'Corporate','Staff'=>'Staff'];
    	$optDuration = ['1'=>'1', '3'=>'3', '6'=>'6','12'=>'12','18'=>'18','24'=>'24'];
    	$optPaymentType = ['Cash'=>'Cash','AD'=>'AD','Prepaid'=>'Prepaid','Staff'=>'Staff'];

			//$accurateItems = $this->accurate_items(Auth::user()->club_id);

    	//return view('cf_lite.member.membership-type-list',compact('membershipTypes','optMembershipType','optDuration','optPaymentType','accurateItems'));
    	return view('cf_lite.member.membership-type-list',compact('membershipTypes','optMembershipType','optDuration','optPaymentType'));
    }

		//ACCURATE
		public function accurate_items($club){
				//GET VARIABLES
				$accurate = AccurateAuth::find(1);
				//$databaseID = Accurate::accurateClub($club);
				//$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
				//$branchID = Accurate::accurateBranch($club);

				$accurateClub = Accurate::accurateClub($club);
				$openDB = '';
				$branchID = '';
				$databaseID = '';

				if($accurateClub){
						$databaseID = $accurateClub->accurate_id;
						$openDB = Accurate::AccurateOpenDB($databaseID, $accurate->access_token );
						$branchID = $accurateClub->accurate_branch;
				}
				/* $accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', 1, 100);
				if($accurateItems){
						//do nothing
				}else{
						//$accurateItem = Accurate::AccurateCreateItem($openDB, $userData, 'INVENTORY');
				} */

				$x = 1;
				$accurateItems = [];
				if($accurateClub){
					while($x <= 100) {
							$_accurateItems = Accurate::accurateCheckItems($openDB, 'SERVICE', $x, 100);

							if($_accurateItems){
									//do nothing
									$accurateItems = array_replace_recursive($accurateItems, $_accurateItems);
							}else{
									break;
							}
							$x++;
					}
				}

				return $accurateItems;
		}

    public function store_membership_type(Request $request){
    	$this->validate($request, [
            'membership_type_name' => 'required|string',
            'membership_type' => 'required',
            'duration' => 'required',
            'payment_type' => 'required',
            'accurate_code' => 'sometimes',
        ]);
			//dd($request);
    	if($request->addoredit == 'add'){

	    	$membershipType = new MembershipType;
	    	$membershipType->membership_type_name = $request->membership_type_name;
	    	$membershipType->membership_type = $request->membership_type;
	    	$membershipType->duration = $request->duration;
	    	$membershipType->payment_type = $request->payment_type;
	    	$membershipType->status = $request->status;
				if(isset($request->accurate_code)){
	    		$membershipType->accurate_code = $request->accurate_code;
				}

	    	$membershipType->save();


			// Jonviter DEV
			$membershipTypeData = $membershipType->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/membership-type", $membershipTypeData);
			// Jonviter DEV

	        Session::flash('flash_message', trans('messages.membership_type_has_been_added'));

    	}else if($request->addoredit == 'edit'){
    		$membershipType = MembershipType::findOrFail($request->editid);
	    	$membershipType->membership_type_name = $request->membership_type_name;
	    	$membershipType->membership_type = $request->membership_type;
	    	$membershipType->duration = $request->duration;
	    	$membershipType->payment_type = $request->payment_type;
        $membershipType->status = $request->status;
				if(isset($request->accurate_code)){
	    		$membershipType->accurate_code = $request->accurate_code;
				}
	      $membershipType->save();


		  	// Jonviter DEV
			$membershipTypeData = $membershipType->toArray();
			$apiClient = new ApiClientService();
			$mobile_curves_api = $apiClient->post("/mobile-curves/membership-type", $membershipTypeData);
			// Jonviter DEV

	        Session::flash('flash_message', trans('messages.membership_type_has_been_updated'));

	    }

    	return redirect('cf-lite/membership-types');
    }

    public function membership_type_list_filter(Request $request){
    	$membershipTypes = MembershipType::whereNotNull('membership_type_name');

        if (!empty($request->membership_type_name)):
            $membershipTypes->where('membership_type_name', 'LIKE', '%'.$request->membership_type_name.'%');
        endif;

        $membershipTypes = $membershipType->orderBy('id', 'asc')->paginate(100);

        return view('cf_lite.member.membership-type-list', compact('membershipTypes'));
    }
}
