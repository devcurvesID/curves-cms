<?php

namespace App\Http\Controllers;

use App\Events\julioTrigger;
use App\Http\Requests\MemberPersonalRequest;
use App\Model\Club\ClubInfo;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\GuestDetail;
use App\Model\Log\LogMemberStatus;
use App\Model\Log\LogMemberTShirtSize;
use App\Model\MasterData\AccessMenu;
use App\Model\MasterData\City;
use App\Model\MasterData\Country;
use App\Model\MasterData\MemberAttribute;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\MemberSource;
use App\Model\MasterData\PredefinedMessage;
use App\Model\MasterData\Province;
use App\Model\MasterData\Size;
use App\Model\MasterData\TargetCategoryDetail;
use App\Model\Member\EmergencyContact;
use App\Model\Member\MemberHealthHistory;
use App\Model\Member\MemberMessageNote;
use App\Model\Member\MemberPhoto;
use App\Model\Payment\MemberPayment;
use App\Model\Refund\RefundSubmission;
use App\Model\Staff\CoachTarget;
use App\Model\Staff\StaffPersonal;
use App\Model\Staff\TransferHistory;
use App\Model\Workout\Workout;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Services\ApiClientService;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
        $this->middleware('member-status-setting', ['only' => [
            'member_status_setting_list',
            'bulk_update_member_status_setting',
        ]]);
    }

    public function index()
    {
        $members = User::where('role_id', '7')->orderBy('name', 'asc')->get();

        return view('cf_back.member.member', compact('members'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $clubs = ClubInfo::pluck('club_name', 'id');
        $countries = Country::pluck('country', 'id');
        $provinces = Province::where('country_id', $user->user_memberpersonal->country_id)->pluck('province', 'id');
        $cities = City::where('province_id', $user->user_memberpersonal->province_id)->pluck('city', 'id');
//        $members = User::where('role_id', '7')->where('id', '<>', $id)->orderBy('name', 'asc')->pluck('name', 'id');

        return view('cf_back.member.edit-member', compact('user', 'clubs', 'countries', 'provinces', 'cities', 'mktprefs', 'members'));
    }

    public function update($id, MemberRequest $request)
    {
        $user = User::findOrFail($id);

        if ($request->has('personal_data')):
            if (!empty($request->middle_name)):
                $user->name = $request->first_name . ' ' . $request->middle_name . ' ' . $request->sur_name;
            else:
                $user->name = $request->first_name . ' ' . $request->sur_name;
            endif;

            $user->club_id = $request->club_id;
            $user->save();

            $member = $user->user_memberpersonal;
            $member->first_name = $request->first_name;
            $member->sur_name = $request->sur_name;

            if (!empty($request->middle_name)):
                $member->middle_name = $request->middle_name;
            endif;

            if ($request->hasFile('photo')) {
                if (!empty($user->user_memberpersonal->photo)):
                    $this->deletePhoto($user);
                endif;

                $path = 'memberphotos';
                $member->photo = $this->uploadPhoto($path, $request);
            }

            $member->birth = $request->birth;
            $member->sex = $request->sex;
            $member->nik = $request->nik;
            $member->country_id = $request->country_id;
            $member->province_id = $request->province_id;
            $member->city_id = $request->city_id;
            $member->address = $request->address;
            $member->postal = $request->postal;
            $member->phone = $request->phone;
            $member->cellphone = $request->cellphone;
            $member->key_tag_id = $request->key_tag_id;
            $user->user_memberpersonal()->save($member);

            Session::flash('flash_message', trans('messages.usermember_update_message'));
        elseif ($request->has('marketing_preference')):
            $user->member_mktpref()->sync($request->mktpref);

            Session::flash('flash_message', trans('messages.usermember_mktpref_update_message'));
        elseif ($request->has('family_addon')):
            $check_family = FamilyAddon::where('user_id', $user->id)->get();

            if (count($check_family) > 0):
                $family = $user->user_familyaddon;
                $fam_msg = 'messages.usermember_family_addon_update_message';
            else:
                $family = new FamilyAddon;
                $fam_msg = 'messages.familyaddon_added_message';
            endif;

            $family->family_id = $request->family_id;
            $family->siblings = $request->siblings;
            $user->user_familyaddon()->save($family);

            Session::flash('flash_message', trans($fam_msg));
        endif;

        return redirect('member/' . $id . '/edit');
    }

    // Member Attribute

    public function index_attribute()
    {
        $attributes = MemberAttribute::all();
        return view('cf_back.member.member_attribute', compact('attributes'));
    }

    public function store_attribute(Request $request) {
        $input = $request->all();
        MemberAttribute::create($input);
        Session::flash('cf_back.flash_message', trans('messages.attribute_create_message'));
        return redirect('cf-back/member-attribute');
    }

    public function update_attribute($id, Request $request) {
        $x = MemberAttribute::findOrFail($id);
        $x->attribute = $request->input('attribute');
        $x->save();
        Session::flash('cf_back.flash_message', trans('messages.attribute_update_message'));
        return redirect('cf-back/member-attribute');
    }

    public function destroy_attribute($id) {
        $x = MemberAttribute::findOrFail($id);
        $x->delete();

        Session::flash('cf_back.flash_message', trans('messages.role_delete_message'));

        return redirect('cf-back/member-attribute');
    }

    public function update_emergency_contact($user, EmergencyContactRequest $request)
    {
        $fullName = explode(' ', $request->input('full_name'), '2');

        $emergencyContact = EmergencyContact::findOrFail($user);
        $emergencyContact->user_id = $user;
        $emergencyContact->first_name = $fullName[0];
        $emergencyContact->sur_name = !empty($fullName[1]) ? $fullName[1] : ' ';
        $emergencyContact->relation = $request->input('relation');
        $emergencyContact->phone = $request->input('phone');
        $emergencyContact->save();

        $memberAttributes = User::findOrFail($user);
        $memberAttributes->member_attribute()->sync($request->input('memberattribute'));

        Session::flash('flash_message', trans('messages.emergency_contact_update_message'));

        return redirect('member/' . $user . '/edit');
    }

    //NEW MEMBER
    public function new_member() {
        if (Auth::check()):
            $club = Auth::user()->user_club;
            $new_members = User::where('club_id', '=', Auth::user()->club_id)
                ->whereHas('user_memberpersonal', function ($q) {
                    $q->whereDate('joined', '=', Carbon::today()->toDateString());
                })->orderBy('created_at', 'desc')
                ->get();

            $date = Carbon::today();

            $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

            //Staff Added 22012019
            $staffs = User::where('club_id', '=', Auth::user()->club_id)
                ->where('role_id', '<>', '7')
                ->where('role_id', '>', '2')
                ->orderBy('name', 'asc')
                ->pluck('name', 'id');

            return view('cf_lite.member.new-member-list', compact('new_members', 'date', 'club', 'sources', 'staffs'));
        else:
            Session::flash('flash_message', trans('messages.please_login_first'));
            return redirect('cf-lite/dashboard');
        endif;
    }

    public function new_member_date(Request $request) {
        if (Auth::check()):
            $club = Auth::user()->user_club;
            $date = $request->joined_date;
            $new_members = User::where('club_id', '=', Auth::user()->club_id)
                ->whereHas('user_memberpersonal', function ($q) use ($date) {
                    $q->whereDate('joined', '=', $date);
                })->orderBy('created_at', 'desc')
                ->get();

            $sources = MemberSource::orderBy('source', 'asc')->pluck('source', 'id');

            return view('cf_lite.member.new-member-list', compact('new_members', 'date', 'club', 'sources'));
        else:
            Session::flash('flash_message', trans('messages.please_login_first'));
            return redirect('cf-lite/dashboard');
        endif;
    }
    //End of NEW MEMBER


    //MEMBER PERSONAL
    public function member_edit($id) {
        $member = User::findOrFail($id);

        $member_attributes = MemberAttribute::orderBy('attribute', 'asc')->pluck('attribute', 'id');
        $tshirt_sizes = Size::orderBy('id', 'asc')->pluck('size', 'id');

        return view('cf_lite.member.member-detail', compact('member', 'member_attributes', 'tshirt_sizes'));
    }

    public function member_update($id, MemberPersonalRequest $request) {
        $member = User::findOrFail($id);

        //Edit Data User
        $member->name = $request->name;
        $member->email = $request->email;

        //Added 30072018
        if ($member->role_id == '7'):
            $member->username = $request->key_tag_id;
        endif;

        $member->save();

        //Edit Data Member Personal
        $member_personal = $member->user_memberpersonal;
        $member_personal->birth = $request->birth;
        $member_personal->address = $request->address;
        $member_personal->postal = $request->postal;

        if (empty($request->phone)):
            $member_personal->phone = $request->cellphone;
        else:
            $member_personal->phone = $request->phone;
        endif;

        $member_personal->cellphone = $request->cellphone;

        if (!empty($member_personal->office)):
            $member_personal->office = $request->office;
        endif;

        $member_personal->nik = $request->nik;

        if ($request->hasFile('photo')) {
//            if (!empty($member_personal->photo)):
//                $this->deletePhoto($member);
//            endif;

            $path = 'images/member_photos';
            $member_personal->photo = $this->uploadPhoto($path ,$request);

            //Symfony Process added 23032018
            //$wow = getcwd();
            //$process = new Process('chmod 664 images/member_photos/*');
            //$process->setWorkingDirectory($wow);
        }

        $member_personal->key_tag_id = $request->key_tag_id;

        //Added 27032018
        $member_personal->tshirt_size = $request->tshirt_size;
        $member->user_memberpersonal()->save($member_personal);

        $memberstatus = $member->user_memberstatus;
        if(!empty($request->ignore_wa)){
            $memberstatus->ignore_wa = true;
        }else{
            $memberstatus->ignore_wa = false;
        }
        $member->user_memberstatus()->save($memberstatus);

        //Added 27032018 LogMemberTShirtSize
        $log_member_tshirt = new LogMemberTShirtSize;
        $log_member_tshirt->member_id = $member->id;
        $log_member_tshirt->tshirt_size = $request->tshirt_size;
        $log_member_tshirt->size_date = Carbon::today();
        $log_member_tshirt->save();


        //Added 28032018 WA Number
        $whatsapp = BuddyReferralLead::findOrFail($member->user_memberpersonal->guest_id);
        if (!empty($request->wa_number)):
            if ($whatsapp->wa_number != $request->wa_number):
                $whatsapp->wa_number = $request->wa_number;
            endif;
        else:
            $whatsapp->wa_number = null;
        endif;
        $whatsapp->save();


        //Add or Edit Emergency Contact
        if (!empty($member->user_emergencycontact)):
            $emergency_contact = $member->user_emergencycontact;
        else:
            $emergency_contact = new EmergencyContact;
        endif;

        $emergency_contact->family_name = $request->family_name;
        $emergency_contact->relation = $request->relation;
        $emergency_contact->emergency_contact_number = $request->emergency_contact_number;
        $member->user_emergencycontact()->save($emergency_contact);


        //Add or Edit Member Health History
        if (!empty($request->doctor_name) || !empty($request->doctor_contact_number) || !empty($request->health_comment)):
            if (!empty($member->user_memberhealthhistory)):
                $health_history = $member->user_memberhealthhistory;
            else:
                $health_history = new MemberHealthHistory;
            endif;

            $health_history->doctor_name = $request->doctor_name;
            $health_history->doctor_contact_number = $request->doctor_contact_number;
            $health_history->comment = $request->health_comment;
            $member->user_memberhealthhistory()->save($health_history);
        endif;

        //Sync Member Attributes
        if (count($member->member_attribute) > 0):
            $member->member_attribute()->sync($request->input('member_attribute'));
        else:
            $member->member_attribute()->attach($request->input('member_attribute'));
        endif;


        //Add or Edit Guest Detail
        if (!empty($member->user_memberpersonal->guest_id)):
            $guest = BuddyReferralLead::findOrFail($member->user_memberpersonal->guest_id);
        else:
            $guest = new BuddyReferralLead;
            $guest->fullname = $member->name;
            $guest->phone = $member->user_memberpersonal->phone;
            $guest->is_br = 'NotBR';
            $guest->source_id = '7';
            $guest->staff_id = $member->user_memberpersonal->sales_person_id;
            $guest->club_id = $member->club_id;
            $guest->status_gm = 'Member';
            $guest->save();
        endif;

        if (!empty($guest->buddyreferrallead_guestdetail)):
            $guest_detail = $guest->buddyreferrallead_guestdetail;
        else:
            $guest_detail = new GuestDetail;
        endif;
        $guest_detail->birth = $request->birth;
        $guest_detail->email = $request->email;
        $guest_detail->occupation = $request->occupation;
        $guest_detail->status = $request->marriage_status;

        $fa_date = $member->user_memberpersonal->joined;
        if (!empty($guest->buddyreferrallead_guestappointment)):
            $last = count($guest->buddyreferrallead_guestappointment);
            $counter = 0;
            foreach ($guest->buddyreferrallead_guestappointment as $guest_appointment):
                if ($counter == ($last - 1)):
                    $fa_date = $guest_appointment->fa_date;
                endif;
                $counter++;
            endforeach;
        endif;

        $guest_detail->fa_date = $fa_date;
        $guest->buddyreferrallead_guestdetail()->save($guest_detail);


        //Sync Guest Attributes
        if (count($guest->lead_attribute) > 0):
            $guest->lead_attribute()->sync($request->input('member_attribute'));
        else:
            $guest->lead_attribute()->attach($request->input('member_attribute'));
        endif;

        //Symfony Process added 23032018
        if (!empty($process)):
            $process->run();
        endif;

        $payload = $request->input();
        unset($payload['_token'], $payload['_method']);
        if ($request->hasFile('photo')) {
            $payload['photo'] = $member_personal->photo;
        }

        app(\App\Services\EzypayMembershipSubscriptionService::class)->syncToUpdateUserMobile($member->id, $payload);
        Session::flash('flash_message', trans('messages.member_personal_has_been_updated'));
        return redirect('cf-lite/member/'.$id.'/edit');
    }
    //End of MEMBER PERSONAL


    //Data Anggota
    private function memberListQuery()
    {
        return User::where('club_id', Auth::user()->club_id)
            ->addSelect([
                'ezypay_installment_remaining' => MemberPayment::query()
                    ->select('purchase.installment_remaining')
                    ->leftJoin('ezypay_membership_purchases as purchase', 'purchase.member_payment_id', '=', 'member_payments.id')
                    ->whereColumn('member_payments.user_id', 'users.id')
                    ->where('member_payments.payment_category', 'First')
                    ->orderByDesc('member_payments.created_at')
                    ->orderByDesc('member_payments.id')
                    ->limit(1),
            ]);
    }

    public function member_list() {
        if (Auth::check()):
            $club = Auth::user()->user_club;
            $members = $this->memberListQuery()
                ->whereHas('user_memberstatus', function ($q) {
                    $q->where('status', '=', 'Active')
                        ->orWhere('status', '=', 'Hold');
                })->orderBy('created_at', 'desc')
                ->paginate(100);

            return view('cf_lite.member.member-list', compact('members', 'club'));
        else:
            Session::flash('flash_message', trans('messages.please_login_first'));
            return redirect('cf-lite/dashboard');
        endif;
    }

    public function member_list_search(Request $request) {
        if (Auth::check()):
            $club = Auth::user()->user_club;
            $search = $request->s;
            $query = $this->memberListQuery();

            if ($request->search_option == 'name'):
                $query->where('name', 'ILIKE', '%'.ucwords($search).'%')->where('role_id', '<>', 1);
            elseif ($request->search_option == 'keytag'):
                $query->whereHas('user_memberpersonal', function ($q) use ($search) {
                    $q->where('key_tag_id', 'ILIKE', '%'.$search.'%');
                });
            endif;

            $members = $query->orderBy('name', 'asc')->paginate(100);

            return view('cf_lite.member.member-list', compact('members', 'club'));
        else:
            Session::flash('flash_message', trans('messages.please_login_first'));
            return redirect('cf-lite/dashboard');
        endif;
    }
    //End of Data Anggota


    //Member Message Note
    public function member_message_note_main() {
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Collective')
                    ->orWhere('status', '=', 'Hold');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        $predefined_message_list = PredefinedMessage::orderBy('message', 'asc')->pluck('message', 'id');

        return view('cf_lite.member.member-message-note-main', compact('members', 'predefined_message_list'));
    }

    public function member_message_note_filter(Request $request) {
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '=', '7')
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Collective')
                    ->orWhere('status', '=', 'Hold');
            });

        if (!empty($request->s)):
            $members->where('name', 'LIKE', '%'.strtolower($request->s).'%')
                ->orWhere('name', 'LIKE', '%'.ucwords($request->s).'%');
        else:
            if ($request->search_option == 'empty_note'):
                $members->whereDoesntHave('user_membermessagenote');
            elseif ($request->search_option == 'ongoing'):
                $members->whereHas('user_membermessagenote', function ($r) {
                    $r->whereDate('from_date', '<=', Carbon::now()->toDateString())
                        ->whereDate('thru_date', '>=', Carbon::now()->toDateString());
                });
            elseif ($request->search_option == 'expired'):
                $members->whereHas('user_membermessagenote', function ($r) {
                    $r->whereDate('thru_date', '<', Carbon::now()->toDateString());
                });
            endif;
        endif;

        $members = $members->orderBy('name', 'asc')->paginate(100);

        $predefined_message_list = PredefinedMessage::orderBy('message', 'asc')->pluck('message', 'id');

        return view('cf_lite.member.member-message-note-main', compact('members', 'predefined_message_list'));
    }

    public function update_member_message_note(Request $request) {
        $this->validate($request, [
            'attribute' => 'required',
            'from_date' => 'required|date',
            'thru_date' => 'required|date',
            'notes' => 'sometimes',
            'predefined_message' => 'sometimes',
        ]);

        $member = User::findOrFail($request->attribute);

        if (!empty($member->user_membermessagenote)):
            $message_note = $member->user_membermessagenote;
        else:
            $message_note = new MemberMessageNote;
        endif;

        if (!empty($request->notes)):
            $message_note->notes = $request->notes;
        endif;

        if (!empty($request->predefined_message)):
            $message_note->predefined_message = $request->predefined_message;
        endif;
        
        $message_note->from_date = $request->from_date;
        $message_note->thru_date = $request->thru_date;
        $member->user_membermessagenote()->save($message_note);
        // Jonviter DEV
        if ($request->predefined_message):
            $predifined_message = PredefinedMessage::find($request->predefined_message);
            $message_note->notes = $predifined_message->message;
        endif;
        $workoutsJson = $message_note->toArray();
        $apiClient = new ApiClientService();
        $mobile_curves_api = $apiClient->post("/mobile-curves/member-message-notes", $workoutsJson);
        // Jonviter DEV

        Session::flash('flash_message', trans('messages.member_message_note_has_been_updated'));
        return redirect('cf-lite/member-message-note');
    }
    //End of Member Message Note


    //MEMBER PHOTO ALBUM
    public function member_album($id) {
        $member = User::findOrFail($id);

        if (count($member->user_memberphoto) > 0):
            $member_photos = MemberPhoto::where('member_id', '=', $member->id)
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            return view('cf_lite.member.member-photo-album', compact('member', 'member_photos'));
        endif;

        return view('cf_lite.member.member-photo-album', compact('member'));
    }

    public function add_member_photo($id, Request $request) {
        $this->validate($request, [
            'photo' => 'required|image|mimes:jpeg,jpg,bmp,png',
            'description' => 'sometimes',
            'date_taken' => 'required|before_or_equal:today'
        ]);

        $member = User::findOrFail($id);
        $member_photo = new MemberPhoto;

        $member_photo->member_id = $member->id;

        if ($request->hasFile('photo')) {
            $path = 'images/member_photos';
            $member_photo->photo = $this->uploadPhoto($path ,$request);
        }

        $member_photo->description = $request->description;
        $member_photo->date_taken = $request->date_taken;

        $member_photo->save();

        Session::flash('flash_message', trans('messages.member_photo_has_been_added'));
        return redirect('cf-lite/member/'.$member->id.'/album');
    }

    public function delete_member_photo($id) {
        $member_photo = MemberPhoto::findOrFail($id);
        $member = $member_photo->member_id;
        if (!empty($member_photo->photo)):
            $this->deleteMemberPhoto($member_photo);
        endif;

        $member_photo->delete();

        Session::flash('flash_message', trans('messages.member_photo_has_been_deleted'));
        return redirect('cf-lite/member/'.$member.'/album');
    }
    //End of MEMBER PHOTO ALBUM


    //MEMBER STATUS SETTING
    public function member_status_setting_list(Request $request) {
        $club = Auth::user()->user_club;
        $members = User::where('club_id', '=', Auth::user()->club_id)
            ->whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Hold');
            });

        if (!empty($request->s)):
            $members->where('name', 'ILIKE', '%'.$request->s.'%');
        endif;

        $members = $members->orderBy('created_at', 'desc')->paginate(100);

        $fa_coaches = User::where('club_id', '=', Auth::user()->club_id)
            ->where('role_id', '<>', '7')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('cf_lite.member.member-status-setting-list', compact('members', 'club', 'fa_coaches'));
    }

    public function bulk_update_member_status_setting(Request $request) {
        if (!empty($request->member)):
            $vips = $request->vip;
            $from_dates = $request->from_date;
            $thru_dates = $request->thru_date;
            $fa_coaches = $request->fa_coach;

            //Added 07052018
            $unsaved = 0;
            $name_collection = '';

            foreach ($request->member as $key => $value):
                $member = User::findOrFail($value);

                //Check if Has Pending Refund Submission Added 07052018
                $refund_submission = RefundSubmission::where('user_id', '=', $member->id)
                    ->where('refund_status', '=', 'Pending')
                    ->get()
                    ->count();

                if ($refund_submission == 0):
                    //Edit Log Member Membership added 10042018
                    $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                        ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                        ->whereDate('from_date', '<=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                        ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Added 18102018
                    if (empty($log_member_status)):
                        $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                            ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                            ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                            ->orderBy('created_at', 'desc')
                            ->first();

                        //Added 31102018
                        if (empty($log_member_status)):
                            $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                                ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                                ->whereDate('thru_date', '<=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                                ->orderBy('created_at', 'desc')
                                ->first();
                        endif;
                    endif;

                    $new_log_member_status = LogMemberStatus::findOrFail($log_member_status->id);
                    $new_log_member_status->thru_date = $thru_dates[$key];
                    $new_log_member_status->save();

                    //Member Status
                    $member_status = $member->user_memberstatus;

                    $old_from_date = $member_status->from_date;

                    if ($old_from_date != $from_dates[$key]):
                        $member_status->from_date = $from_dates[$key];
                    endif;

                    if (!empty($vips[$key])):
                        $member_status->thru_date = $thru_dates[$key];
                        $member_status->member_vip_status = 'VIP';
                    else:
                        $duration = $member_status->memberstatus_membershiptype->duration;
//                    $member_status->thru_date = Carbon::parse($from_dates[$key])->addMonths($duration);
                        $member_status->thru_date = $thru_dates[$key];

                        $member_status->member_vip_status = 'General';
                    endif;

                    $member->user_memberstatus()->save($member_status);


                    //Member Personal
                    $member_personal = $member->user_memberpersonal;

                    if ($member_personal->sales_person_id != $fa_coaches[$key]):
                        $member_personal->sales_person_id = $fa_coaches[$key];
                        $member->user_memberpersonal()->save($member_personal);
                    endif;
                else:
                    $unsaved++;

                    if ($unsaved == 1):
                        $name_collection .= $member->name;
                    else:
                        $name_collection .= ', '.$member->name;
                    endif;
                endif;
            endforeach;

            if ($unsaved == 0):
                Session::flash('flash_message', trans('messages.member_status_setting_has_been_updated'));
            else:
                Session::flash('flash_message', $name_collection.' '.trans('messages.could_not_be_saved_due_to_pending_cancellation'));
            endif;
        else:
            Session::flash('flash_message', trans('messages.nothing_changed'));
        endif;

        return redirect('cf-lite/member/status-setting');
    }

    public function add_membership_duration_to_clubs(Request $request){
        DB::beginTransaction();

        try{
            $user = Auth::user();

            $members = User::where('club_id',$user->club_id)->whereHas('user_memberstatus', function($q){
                $q->where('status','Active');
            })->with('user_memberstatus')->get();

            foreach($members as $member){
                $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                    ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                    ->whereDate('from_date', '<=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                    ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                    ->orderBy('created_at', 'desc')
                    ->first();

                //Added 18102018
                if (empty($log_member_status)) :
                    $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                        ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                        ->whereDate('thru_date', '=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //Added 31102018
                    if (empty($log_member_status)) :
                        $log_member_status = LogMemberStatus::where('user_id', '=', $member->id)
                            ->where('membership_type_id', '=', $member->user_memberstatus->membership_type_id)
                            ->whereDate('thru_date', '<=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                            ->orderBy('created_at', 'desc')
                            ->first();
                    endif;
                endif;

                $member_status = $member->user_memberstatus;
                $member_status->thru_date = Carbon::parse($member_status->thru_date)->addDays($request->duration);
                $member_status->save();

                $log_member_status-> thru_date = Carbon::parse($member_status->thru_date);
                $log_member_status->save();
            }
            DB::commit();
        } catch(Exception $e){
            Log::error($e);
            DB::rollBack();
        }

        return redirect('cf-lite/member/status-setting');
    }
    //End of MEMBER STATUS SETTING


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

    private function deletePhoto(User $user)
    {
        //$user = User::findOrFail($id);
        $exist = Storage::disk('member_photos')->exists($user->user_memberpersonal->photo);

        if (isset($user->user_memberpersonal->photo) && $exist) {
            $delete = Storage::disk('member_photos')->delete($user->user_memberpersonal->photo);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    private function deleteMemberPhoto(MemberPhoto $user)
    {
        //$user = User::findOrFail($id);
        $exist = Storage::disk('member_photos')->exists($user->photo);

        if (isset($user->photo) && $exist) {
            $delete = Storage::disk('member_photos')->delete($user->photo);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    //Transfer member
    public function transfer_member_detail($id, Request $request){
        $member = User::findOrFail($id);
        $allClub = ClubInfo::pluck('club_name','id');
        $club = ClubInfo::where('id','<>',$member->club_id)->pluck('club_name','id');

        return view('cf_lite.member.transfer-member-detail',compact('member','club','allClub'));
    }

    public function transfer_member($id, Request $request){
        $this->validate($request,[
            'member_id' => 'required',
            'club' => 'required',
        ]);

        $member = User::findOrFail($request->member_id);

        if($member->user_memberstatus->payment_status == 'NotSet'){
            Session::flash('flash_message', trans('messages.please_set_installment_first'));
            return redirect('cf-lite/member/'.$member->id.'/transfer-member');

        }else{
            //Transfer Member History
            $transferHistory = new TransferHistory;
            $transferHistory->user_id = $member->id;
            $transferHistory->club_id = $member->club_id;
            $transferHistory->role_id = $member->role_id;
            $transferHistory->from_date = \Carbon\Carbon::now();
            $transferHistory->save();

            $member->club_id = $request->club;
            $member->save();

            //Update log_member_membership 18062019
            $last_log = LogMemberStatus::where('user_id', '=', $member->id)
                ->orderBy('from_date', 'desc')
                ->first();

            // Added 03022020
            // Create new log
            $new_log = new LogMemberStatus;
            $new_log->user_id = $last_log->user_id;
            $new_log->membership_type_id = $last_log->membership_type_id;
            $new_log->from_date = Carbon::today();
            $new_log->thru_date = $last_log->thru_date;
            $new_log->club_id = $request->club;
            $new_log->save();

            // Update old log
            $update_log = LogMemberStatus::findOrFail($last_log->id);
//            $update_log->club_id = $request->club;
            if (Carbon::parse($last_log->from_date) < Carbon::yesterday()):
                $update_log->thru_date = Carbon::yesterday();
            else:
                $update_log->thru_date = $last_log->from_date;
            endif;
            $update_log->save();

            // Amortization
            app('App\Http\Controllers\AccountingAmortizationController')->transfer_amortization($member, Carbon::now());

            return redirect('cf-lite/member/list');
        }
    }

    //CONVERT MEMBER
    public function convert_member_into_staff($id, Request $request) {
        if ((Auth::user()->role_id < 6) || (Auth::user()->role_id == 8)):
            $this->validate($request, [
                'convert_date' => 'required|date',
            ]);

            $user = User::findOrFail($id);

            //Create staff personal
            $staff = new StaffPersonal;
            $staff->email_personal = $user->email;
            $user->user_staffpersonal()->save($staff);

            //Check Member Status
            $member_status = $user->user_memberstatus;
            $payment_type = false;
            if ($member_status->memberstatus_membershiptype->payment_type == 'AD'):
                $payment_type = true;
            endif;

            if ($payment_type):
                //Delete Unpaid Payment
                $member_payments = MemberPayment::where('user_id', '=', $user->id)
                    ->where('payment_status', '=', 'Unpaid')
                    ->whereDate('payment_date', '>=', Carbon::parse($request->convert_date)->toDateString())
                    ->get();

                foreach ($member_payments as $member_payment):
                    $payment = MemberPayment::findOrFail($member_payment->id);
                    $payment->delete();
                endforeach;
            endif;

            //Update Member Status
            $membership_staff = MembershipType::where('membership_type', '=', 'Staff')->first();
            $member_status->membership_type_id = $membership_staff->id;
            $member_status->from_date = Carbon::parse($request->convert_date);
            $member_status->thru_date = Carbon::parse($request->convert_date)->addYears(99);
            $user->user_memberstatus()->save($member_status);

            //Log Member Status
            $last_log_member_status = LogMemberStatus::where('user_id', '=', $user->id)
                ->where('club_id', '=', $user->club_id)
                ->orderBy('thru_date', 'desc')
                ->first();

            //Update Log Member Status
            $last_lms = LogMemberStatus::findOrFail($last_log_member_status->id);
            $last_lms->thru_date = $request->convert_date;
            $last_lms->save();

            //Create New Log Member Status
            $log_member_status = new LogMemberStatus;
            $log_member_status->user_id = $user->id;
            $log_member_status->membership_type_id = $membership_staff->id;
            $log_member_status->from_date = $request->convert_date;
            $log_member_status->thru_date = Carbon::parse($request->convert_date)->addYears(99);
            $log_member_status->save();

            //Transfer Staff History
            $transferHistory = new TransferHistory;
            $transferHistory->user_id = $user->id;
            $transferHistory->club_id = $user->club_id;
            $transferHistory->role_id = '6';
            $transferHistory->from_date = $request->convert_date;
            $transferHistory->save();

            //Access Menu Default
            $access_list = AccessMenu::where('default_role', '>=', '6')->get();
            if ($access_list->count() > 0):
                foreach ($access_list as $access):
                    $user->user_accessmenu()->attach($access->id);
                endforeach;
            endif;

            //Update User
            $user->role_id = '6';
            $user->save();

            // Adding coach target detail
            $target_details = TargetCategoryDetail::where('type', '=', 'Staff')->get();
            if ($target_details->count() > 0):
                foreach ($target_details as $detail):
                    $coach_target = new CoachTarget();
                    $coach_target->coach_id = $user->id;
                    $coach_target->target_category_detail_id = $detail->id;
                    $coach_target->target = $detail->target;
                    $coach_target->acquired = 0;
                    $coach_target->status = $detail->status;
                    $coach_target->save();
                endforeach;
            endif;

            Session::flash('flash_message', trans('messages.staff_has_been_added'));
            return redirect('cf-lite/staff/'.$user->id.'/detail');
        else:
            Session::flash('flash_message', trans('messages.you_dont_have_permission'));
            return redirect('cf-lite/member/'.$id.'/edit');
        endif;
    }

    //Developer Only
    public function auto_generate_member_flag() {
        if (Auth::user()->role_id == 1):
            $members = User::whereHas('user_memberstatus', function ($q) {
                $q->where('status', '=', 'Active')
                    ->orWhere('status', '=', 'Hold');
                })
                ->get();

            foreach ($members as $member):
                $workouts = Workout::where('user_id', '=', $member->id)
                    ->whereDate('workout_date', '>=', Carbon::parse($member->user_memberstatus->from_date)->toDateString())
                    ->whereDate('workout_date', '<=', Carbon::parse($member->user_memberstatus->thru_date)->toDateString())
                    ->orderBy('workout_date', 'asc')
                    ->get();

                $flag = 'E';
                if (count($workouts) > 0):
                    $fwo = $workouts[0];

                    //Hitung lama join
                    $fwo_date = Carbon::parse($fwo->workout_date);
                    $diffdays = Carbon::today()->diffInDays($fwo_date);
                    $diffweeks = floor($diffdays / 7);

                    if ($diffweeks == 0):
                        $diffweeks = 1;
                    endif;

                    if ((count($workouts) / $diffweeks) >= 3):
                        $flag = 'A';
                    elseif (((count($workouts) / $diffweeks) < 3) && ((count($workouts) / $diffweeks) >= 2)):
                        $flag = 'B';
                    elseif (((count($workouts) / $diffweeks) < 2) && ((count($workouts) / $diffweeks) >= 1)):
                        $flag = 'C';
                    elseif (((count($workouts) / $diffweeks) < 1) && ((count($workouts) / $diffweeks) >= 0.5)):
                        $flag = 'D';
                    elseif ((count($workouts) / $diffweeks) < 0.5):
                        $flag = 'E';
                    endif;
                endif;

                $the_member = User::findOrFail($member->id);
                $profile = $the_member->user_memberpersonal;
                $profile->flag = $flag;
                $the_member->user_memberpersonal()->save($profile);
            endforeach;

            Session::flash('flash_message', trans('messages.auto_generate_flag_has_been_successful'));
            return redirect('cf-lite/member/list');
        else:
            return redirect('cf-lite/dashboard');
        endif;
    }

    public function qr_broadcast_ajax(Request $request) {
        $member = User::where('id', '=', $request->member_id)
            ->where('club_id', '=', $request->club_id)
            ->whereHas('user_memberpersonal')
            ->first();

        $qrcode = 'http://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=159.89.208.182/curves/api/registration-page?club='.$member->user_club->club_code.'_'.$member->user_memberpersonal->key_tag_id;

        event(new julioTrigger($qrcode, $member->club_id));

        return response()->json($member->user_memberstatus->qr_generate);
    }
}
