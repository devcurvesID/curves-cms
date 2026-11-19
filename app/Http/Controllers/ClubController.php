<?php

namespace App\Http\Controllers;

use App\DefaultSetting;
use App\Http\Requests\AddNewClubRequest;
use App\Http\Requests\ClubDetailRequest;
use App\Http\Requests\UpdateClubTaxConfigRequest;
use App\Model\Club\ClubInfo;
use App\Model\Club\ClubLogin;
use App\Model\Club\ClubOpenHour;
use App\Model\Club\ClubServiceFee;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\AgreementRenewalType;
use App\Model\MasterData\Bank;
use App\Model\MasterData\City;
use App\Model\MasterData\Country;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\Province;
use App\Model\Tax\TaxConfig;
use App\Model\Tax\Taxtype;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Http\Request;

use App\Helpers\Accurate;

class ClubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('club-password',['only'=>[
                'change_password_club_list',
                'change_club_password',
                'store_club_password',
                'update_club_password',
            ]]);
    }

    public function index() {
        $clubs = ClubInfo::all()->sortBy('club_name');
        $count = $clubs->count();
        $countries = Country::pluck('country', 'id');
        return view('cf_back.clubs.clubinfo', compact('clubs', 'count', 'countries'));
    }

    public function create() {
        $countries = Country::pluck('country', 'id');

        return view ('cf_back.clubs.tambah-clubinfo', compact('countries'));
    }

//    public function store(ClubRequest $request) {
//        $club = new ClubInfo;
//
//        $club->club_name = $request->club_name;
//        $club->country_id = $request->country_id;
//        $club->province_id = $request->province_id;
//        $club->city_id = $request->city_id;
//        $club->postal = $request->postal;
//        $club->address = $request->address;
//        $club->phones = $request->phones;
//        $club->email = $request->email;
//        $club->facebook = $request->facebook;
//        $club->status = 'Active';
//        $club->save();
//
//        Session::flash('flash_message', trans('messages.clubinfo_create_message'));
//
//        return redirect('cf_back/clubinfo');
//    }

    public function edit($id) {
        $club = ClubInfo::findOrFail($id);
        $countries = Country::pluck('country', 'id');
        $provinces = Province::where('country_id', $club->country_id)->pluck('province', 'id');
        $cities = City::where('province_id', $club->province_id)->pluck('city', 'id');
        return view('cf_back.clubs.edit-clubinfo', compact('club', 'countries', 'provinces', 'cities'));
    }

    public function update($id, ClubRequest $request) {
        $club = ClubInfo::findOrFail($id);

        $club->club_name = $request->club_name;
        $club->country_id = $request->country_id;
        $club->province_id = $request->province_id;
        $club->city_id = $request->city_id;
        $club->postal = $request->postal;
        $club->address = $request->address;
        $club->phones = $request->phones;
        $club->email = $request->email;
        $club->facebook = $request->facebook;
        $club->status = $request->status;
        $club->save();

        Session::flash('flash_message', trans('messages.clubinfo_update_message'));

        return redirect('cf_back/clubinfo');
    }

    public function destroy($id) {
        $club = ClubInfo::findOrFail($id);
        $club->delete();

        Session::flash('flash_message', trans('messages.clubinfo_delete_message'));

        return redirect('cf_back/clubinfo');
    }

    //Club Service Fees -> disingkat CSF
    public function csf_index() {
        if (Auth::check()):
            $clubsfs = ClubServiceFee::where('club_id', Auth::user()->club_id)->where('from_date', '<=', Carbon::now())->where('thru_date', null)->get();
            return view('cf_back.clubs.club-service-fee', compact('clubsfs'));
        endif;
    }


    //CF LITE
    public function club_list() {
        $open = 0;
        foreach (Auth::user()->user_accessmenu as $access):
            if ($access->id == 1):
                $open = 1;
            endif;
        endforeach;

        if ($open == 1):
            $clubs = ClubInfo::where('status', '=', 'Active')->orderBy('club_name', 'asc')->paginate(12);

            return view('cf_lite.club.club-list', compact('clubs'));
        elseif ($open == 0):
            return redirect('cf-lite/club/'.Auth::user()->club_id);
        endif;
    }

    public function add_new_club() {
        $default_settings = DefaultSetting::where('type', '=', 'club')->pluck('value', 'key_name');
        $membership_types = MembershipType::where('membership_type', '<>', 'Staff')->orderBy('id', 'asc')->pluck('membership_type_name', 'id');
        $agreement_renewals = AgreementRenewalType::pluck('agreement_renewal_type', 'id');
        $banks = Bank::where('id', '<>', 1)->with('bank_chartofaccount')->get();
        $jubilants = Jubilant::orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');

        //$accurateDB = Accurate::accurateGetDB();
        $accurateDB = false;

        return view('cf_lite.club.add-new-club', compact('default_settings', 'membership_types', 'agreement_renewals', 'banks', 'jubilants', 'accurateDB'));
    }

    public function store_new_club(AddNewClubRequest $request) {
        $club = new ClubInfo;

        //CLUB INFO
        $club->club_name = $request->club_name;

        //Added 20062018
        $club->club_code = $request->club_code;

        //Address
        $addresses = explode(',', strtolower($request->address));
        $address_depth = count($addresses);
        $club->country = trim($addresses[$address_depth - 1]);
        $club->province = trim($addresses[$address_depth - 2]);
        $club->city = trim($addresses[$address_depth - 3]);

        $address = $addresses[0];
        for ($i = 1; $i < ($address_depth - 3); $i++):
            $address.= ','.$addresses[$i];
        endfor;
        $club->address = $address;
        //End of Address

        $club->postal = $request->postal;
        $club->phones = $request->phones;
        $club->email = $request->email;
        $club->facebook = $request->facebook;
        $club->status = 'Active';

        // Added 06122019
        $club->jubilant_id = $request->jubilant;

        // Added 12122019
        if (!empty($request->hq)):
            $club->hq = true;
        endif;

        // Added 01302024
        $club->accurate_id = $request->accurate_id;
        $club->accurate_branch = $request->accurate_branch;

        $club->save();

        //add to bank_club
        $club->club_bank()->attach($request->input('bank'));

        //CLUB OPEN HOUR
        foreach ($request->schedule_id as $key => $value):
            $new_coh = new ClubOpenHour;
            $new_coh->club_id = $club->id;
            $new_coh->from_day = $request->from_day[$key];
            $new_coh->thru_day = $request->thru_day[$key];
            $new_coh->from_hour = $request->from_hour[$key];
            $new_coh->thru_hour = $request->thru_hour[$key];
            $new_coh->save();
        endforeach;

        //CLUB SERVICE FEE
        foreach ($request->csf_id as $key => $value):
            $csf_new = new ClubServiceFee;
            $csf_new->club_id = $club->id;
            $csf_new->membership_type_id = $request->membership_type[$key];
            $csf_new->sf_amount = $request->sf_amount[$key];
            $csf_new->sf_freq = 'One Time';
            $csf_new->mf_amount = $request->mf_amount[$key];
            $csf_new->mf_freq = 'Monthly';
            $csf_new->pre_pay_discount = $request->pre_pay_discount[$key];
            $csf_new->pre_pay_discount_value = $request->pre_pay_discount_value[$key];
            $csf_new->agreement_renewal_type_id = $request->agreement_renewal[$key];

            if (!empty($request->is_taxable[$key])):
                $csf_new->is_taxable = 'Yes';
            else:
                $csf_new->is_taxable = 'No';
            endif;

            $csf_new->from_date = Carbon::now();
            $csf_new->save();
        endforeach;

        //Tax Config
        $default_taxes = DefaultSetting::where('category', '=', 'tax_config')->where('type', '=', 'club')->get();

        foreach ($default_taxes as $default_tax):
            $tax_config = new TaxConfig;
            $tax_config->club_id = $club->id;

            if ($default_tax->key_name == 'ppn'):
                $tax_type = '1';
            endif;
            $tax_config->tax_type_id = $tax_type;

            $tax_config->rate = $default_tax->value;
            $tax_config->from_date = Carbon::now();
            $tax_config->save();
        endforeach;

        Session::flash('flash_message', trans('messages.new_club_has_been_added'));

        return redirect('cf-lite/club/'.$club->id);
    }

    public function club_detail($id) {
        $club = ClubInfo::findOrFail($id);

        $csfs = ClubServiceFee::where('club_id', '=', $club->id)
            ->where('thru_date', null)
            ->orderBy('membership_type_id', 'asc')
            ->get();

        $agreement_renewals = AgreementRenewalType::pluck('agreement_renewal_type', 'id');

        $club_id = $club->id;
        $membership_type_used = MembershipType::whereHas('membershiptype_clubservicefee', function($q) use ($club_id) {
            $q->where('thru_date', null);
            $q->where('club_id', '=', $club_id);
        })->get();

//        $mt_index = 0;
        $membership_types = MembershipType::whereNotNull('membership_type_name')/*->where('membership_type', '<>', 'Staff')*/;
        foreach ($membership_type_used as $used):
//            if ($mt_index == 0):
//                $membership_types->where('id', '<>', $used->id);
//            else:
//                $membership_types->orWhere('id', '<>', $used->id);
//            endif;
//            $mt_index++;
            $membership_types->where('id', '<>', $used->id);
        endforeach;
        $membership_types = $membership_types->pluck('membership_type_name', 'id');

        $clubbanks = $club->club_bank;
        $banks = Bank::where('id', '<>', 1)->get();

        $jubilants = Jubilant::orderBy('jubilant_name', 'asc')->pluck('jubilant_name', 'id');

        //$accurateDB = Accurate::accurateGetDB();
        $accurateDB = false;

        return view('cf_lite.club.club-detail', compact('club', 'csfs', 'agreement_renewals', 'membership_types', 'clubbanks', 'banks', 'jubilants', 'accurateDB'));
    }

    public function update_club_detail($id, ClubDetailRequest $request) {
        $club = ClubInfo::findOrFail($id);
        //dd($request);
        if ($request->attribute == 'info'):
            $club_index_start = 0;
            $club_index_end = 1;
        elseif ($request->attribute == 'schedule'):
            $club_index_start = 1;
            $club_index_end = 2;
        elseif ($request->attribute == 'csf'):
            $club_index_start = 2;
            $club_index_end = 3;
        else:
            $club_index_start = 0;
            $club_index_end = 4;
        endif;

        for ($j = $club_index_start; $j < $club_index_end; $j++):
            if ($j == 0):
                $club->club_name = $request->club_name;

                //Added 20062018
                if (!empty($request->club_code)):
                    $club->club_code = $request->club_code;
                endif;

                //Address
                $addresses = explode(',', strtolower($request->address));
                $address_depth = count($addresses);
                $club->country = trim($addresses[$address_depth - 1]);
                $club->province = trim($addresses[$address_depth - 2]);
                $club->city = trim($addresses[$address_depth - 3]);

                $address = $addresses[0];
                for ($i = 1; $i < ($address_depth - 3); $i++):
                    $address.= ','.$addresses[$i];
                endfor;
                $club->address = $address;
                //End of Address

                $club->postal = $request->postal;
                $club->phones = $request->phones;
                $club->email = $request->email;
                $club->facebook = $request->facebook;

                // Added 01302024
                $club->accurate_id = $request->accurate_id;
                $club->accurate_branch = $request->accurate_branch;

                // Added 06122019
                $club->jubilant_id = $request->jubilant;

                // Added 12122019
                if (!empty($request->hq)):
                    $club->hq = true;
                else:
                    $club->hq = false;
                endif;

                if ($club->save()):
                    $club->club_bank()->sync($request->input('bank'));
                    Session::flash('flash_message', trans('messages.clubinfo_has_been_updated'));
                endif;

            elseif ($j == 1):
                $club_open_hour_list = ClubOpenHour::where('club_id', '=', $club->id)->orderBy('id', 'asc')->get();
                if (count($request->schedule_id) > 0):
                    //Tahap 1
                    foreach ($club_open_hour_list as $coh):
                        $coh_edit = 0;
                        foreach ($request->schedule_id as $key => $value):
                            if ($coh->id == $value):
                                $coh_edit = 1;
                                $coh_temp = ClubOpenHour::findOrFail($coh->id);
                                $coh_temp->from_day = $request->from_day[$key];
                                $coh_temp->thru_day = $request->thru_day[$key];
                                $coh_temp->from_hour = Carbon::createFromFormat('H:i', Carbon::parse($request->from_hour[$key])->format('H:i'), session('timezone'))->setTimezone('UTC');
                                $coh_temp->thru_hour = Carbon::createFromFormat('H:i', Carbon::parse($request->thru_hour[$key])->format('H:i'), session('timezone'))->setTimezone('UTC');
                                $coh_temp->save();
                            endif;
                        endforeach;

                        if ($coh_edit == 0):
                            $coh_temp = ClubOpenHour::findOrFail($coh->id);
                            $coh_temp->delete();
                        endif;
                    endforeach;

                    //Tahap 2
                    foreach ($request->schedule_id as $key => $value):
                        if(!is_numeric($value)):
                            $new_coh = new ClubOpenHour;
                            $new_coh->club_id = $club->id;
                            $new_coh->from_day = $request->from_day[$key];
                            $new_coh->thru_day = $request->thru_day[$key];
                            $new_coh->from_hour = $request->from_hour[$key];
                            $new_coh->thru_hour = $request->thru_hour[$key];
                            $new_coh->save();
                        endif;
                    endforeach;
                endif;

                Session::flash('flash_message', trans('messages.club_open_hour_has_been_updated'));
            elseif ($j == 2):
                $csf_list = ClubServiceFee::where('club_id', '=', $club->id)->where('thru_date', null)->orderBy('membership_type_id', 'asc')->get();
                if (count($request->csf_id) > 0):
                    //Tahap 1
                    foreach ($csf_list as $csf):
                        $csf_temp = ClubServiceFee::findOrFail($csf->id);
                        $csf_temp->thru_date = Carbon::now();
                        $csf_temp->save();
                        foreach ($request->csf_id as $key => $value):
                            if ($csf->id == $value):
                                $csf_new = new ClubServiceFee;
                                $csf_new->club_id = $club->id;
                                $csf_new->membership_type_id = $request->membership_type[$key];
                                $csf_new->sf_amount = $request->sf_amount[$key];
                                $csf_new->sf_freq = 'One Time';
                                $csf_new->mf_amount = $request->mf_amount[$key];
                                $csf_new->mf_freq = 'Monthly';
                                $csf_new->pre_pay_discount = $request->pre_pay_discount[$key];
                                $csf_new->pre_pay_discount_value = $request->pre_pay_discount_value[$key];
                                $csf_new->agreement_renewal_type_id = $request->agreement_renewal[$key];

                                if (!empty($request->is_taxable[$key])):
                                    $csf_new->is_taxable = 'Yes';
                                else:
                                    $csf_new->is_taxable = 'No';
                                endif;

                                $csf_new->from_date = Carbon::now();
                                $csf_new->save();
                            endif;
                        endforeach;
                    endforeach;

                    //Tahap 2
                    foreach ($request->csf_id as $key => $value):
                        if(!is_numeric($value)):
                            $csf_new = new ClubServiceFee;
                            $csf_new->club_id = $club->id;
                            $csf_new->membership_type_id = $request->membership_type[$key];
                            $csf_new->sf_amount = $request->sf_amount[$key];
                            $csf_new->sf_freq = 'One Time';
                            $csf_new->mf_amount = $request->mf_amount[$key];
                            $csf_new->mf_freq = 'Monthly';
                            $csf_new->pre_pay_discount = $request->pre_pay_discount[$key];
                            $csf_new->pre_pay_discount_value = $request->pre_pay_discount_value[$key];
                            $csf_new->agreement_renewal_type_id = $request->agreement_renewal[$key];

                            if (!empty($request->is_taxable[$key])):
                                $csf_new->is_taxable = 'Yes';
                            else:
                                $csf_new->is_taxable = 'No';
                            endif;

                            $csf_new->from_date = Carbon::now();
                            $csf_new->save();
                        endif;
                    endforeach;
                endif;

                Session::flash('flash_message', trans('messages.club_service_fee_has_been_updated'));
            elseif ($j == 3):
                Session::flash('flash_message', trans('messages.club_detail_has_been_updated'));
            endif;
        endfor;

        return redirect('cf-lite/club/'.$id);
    }

    public function club_tax_config($id) {
        $club = ClubInfo::findOrFail($id);
        $club_id = $club->id;

        $tax_configs = TaxConfig::where('club_id', '=', $club->id)
            ->where('thru_date', null)
            ->orderBy('from_date', 'asc')
            ->get();

        $tax_types = Taxtype::whereDoesntHave('taxtype_taxconfig')
            ->orWhere(function ($r) use ($club) {
                $r->whereHas('taxtype_taxconfig', function ($s) use ($club) {
                    $s->where('club_id', '=', $club->id)
                        ->whereNotNull('thru_date');
                });
            })
            ->orderBy('tax_type', 'asc')
            ->pluck('tax_type', 'id');

        return view('cf_lite.club.club-tax-config', compact('club', 'tax_configs', 'tax_types'));
    }

    public function store_club_tax_config($id, Request $request) {
        $this->validate($request, [
            'tax_type' => 'required',
            'rate' => 'required|between:0,100.00',
            'from_date' => 'required|date',
        ]);

        $club = ClubInfo::findOrFail($id);

        $tax_config = new TaxConfig;
        $tax_config->club_id = $club->id;
        $tax_config->tax_type_id = $request->tax_type;
        $tax_config->rate = $request->rate;
        $tax_config->from_date = $request->from_date;
        $tax_config->save();

        Session::flash('flash_message', trans('messages.club_tax_config_has_been_added'));
        return redirect('cf-lite/club/'.$club->id.'/tax-config');
    }

    public function update_club_tax_config($id, UpdateClubTaxConfigRequest $request) {
        $club = ClubInfo::findOrFail($id);

        $rates = $request->rate;
        $from_dates = $request->from_date;
        $inactives = $request->inactive;

        foreach ($request->tax_config as $key => $value):
            if (!empty($value)):
                $old_tax_config = TaxConfig::findOrFail($value);
                $old_tax_config->thru_date = $from_dates[$key];
                $old_tax_config->save();

                if (empty($inactives[$key])):
                    $tax_config = new TaxConfig;
                    $tax_config->club_id = $club->id;
                    $tax_config->tax_type_id = $old_tax_config->tax_type_id;
                    $tax_config->rate = $rates[$key];
                    $tax_config->from_date = $from_dates[$key];
                    $tax_config->save();
                endif;
            endif;
        endforeach;

        Session::flash('flash_message', trans('messages.club_tax_config_has_been_updated'));
        return redirect('cf-lite/club/'.$club->id.'/tax-config');
    }

    public function change_password_club_list(){
        $clubs = ClubInfo::get();

        return view('cf_lite.club.change-password-club-list',compact('clubs'));
    }

    public function change_club_password($id, Request $request){
        $club = ClubInfo::findOrFail($id);
        $new_or_update = 'new';
        if(!empty($club->clubinfo_clublogin)){
            $new_or_update = 'update';
        }

        return view('cf_lite.club.change-password-club-detail',compact('club','new_or_update'));
    }

    public function store_club_password($id, Request $request){
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);

        $clublogin = new ClubLogin;
        $clublogin->username = $request->username;
        $clublogin->password = $request->password;
        $clublogin->encrypted_password = bcrypt($request->password);

        $club = ClubInfo::findOrFail($id);
        $club->clubinfo_clublogin()->save($clublogin);

        Session::flash('flash_message', trans('messages.club_password_has_been_set'));

        return redirect('cf-lite/change-password-club-detail/'.$club->id);
    }

    public function update_club_password($id, Request $request){
        $this->validate($request, [
            'old_password' => 'required|string|min:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $club = ClubInfo::findOrFail($id);
        if($club->clubinfo_clublogin->password == $request->old_password){
            $club->clubinfo_clublogin->username = $request->username;
            $club->clubinfo_clublogin->password = $request->password;
            $club->clubinfo_clublogin->encrypted_password = bcrypt($request->password);
            $club->clubinfo_clublogin->save();

            Session::flash('flash_message', trans('messages.club_password_has_been_updated'));
        }else{
            Session::flash('flash_message', trans('messages.old_password_not_match'));
        }

        return redirect('cf-lite/change-password-club-detail/'.$club->id);
    }

    public function member_scan_image() {
        $club = ClubInfo::findOrFail(Auth::user()->club_id);

        return view('cf_lite.club.member-scan-img', compact('club'));
    }

    public function store_member_scan_image($id, Request $request) {
        if ($request->hasFile('photo')) {
//            $club = ClubInfo::findOrFail($id);
//            if (!empty($club->image)):
//                $this->deletePhoto($club);
//            endif;
//
//            $path = 'images/club_images';
//            $club->image = $this->uploadPhoto($path ,$request);
//            $club->save();

            $all_club = ClubInfo::where('jubilant_id', '=', $id)->where('status', '=', 'Active')->get();
            $path = 'images/club_images';

//            if (!empty($all_club)) {
//                $this->deletePhoto($all_club[0]);
//            }
            $string_img = $this->uploadPhoto($path ,$request);

            foreach ($all_club as $club) {
                $club->image = $string_img;
                $club->save();
            }

            Session::flash('flash_message', trans('messages.image_has_been_save'));
            return redirect('cf-lite/member-scan-image');
        } else {
            Session::flash('flash_message', trans('messages.file_not_found'));
            return redirect('cf-lite/member-scan-image');
        }

    }

    private function uploadPhoto($path, Request $request)
    {
        $photo = $request->file('photo');
        $ext = $photo->getClientOriginalExtension();

        if ($request->file('photo')->isValid()) {
            $photo_name = 'Jubilant-' . Auth::user()->user_club->jubilant_id . ".$ext";
            $upload_path = $path;
            $request->file('photo')->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }

    private function deletePhoto(ClubInfo $club)
    {
        $exist = Storage::disk('club_images')->exists($club->image);

        if (isset($club->image) && $exist) {
            $delete = Storage::disk('club_images')->delete($club->image);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    public function accurate_branches($accuId){
        return ;
        //return Accurate::accurateBranchList($accuId);
    }
}
