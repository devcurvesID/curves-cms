<?php

namespace App\Model\Club;

use Illuminate\Database\Eloquent\Model;

class ClubInfo extends Model
{
    protected $table = 'club_infos';

    protected $fillable = [
        'club_name',
        /*'country_id',
        'province_id',
        'city_id',*/
        'postal',
        'address',
        'city',
        'province',
        'country',
        'phones',
        'email',
        'facebook',
        'photo',
        'longitude',
        'latitude',
        'status',
        'current_sequence',
        'current_lead_sequence',
        'jubilant_id',
        'hq',
        'image',
        'digital_product',
        'accurate_id', //added property for accurate linking
        'accurate_branch' //added property for accurate branch linking
    ];

    /*public function clubinfo_country() {
        return $this->belongsTo('App\Model\MasterData\Country', 'country_id');
    }

    public function clubinfo_province() {
        return $this->belongsTo('App\Model\MasterData\Province', 'province_id');
    }

    public function clubinfo_city() {
        return $this->belongsTo('App\Model\MasterData\City', 'city_id');
    }*/

    public function clubinfo_clublogin() {
        return $this->hasOne('App\Model\Club\ClubLogin', 'club_id');
    }

    public function club_costrevenuecenter() {
        return $this->hasOne('App\Model\Accounting\CostRevenueCenter', 'club_id');
    }

    public function club_user() {
        return $this->hasMany('App\User', 'club_id');
    }

    public function clubinfo_guestappointment() {
        return $this->hasMany('App\Model\Appointment\GuestAppointment', 'club_id');
    }

    public function clubinfo_clubservicefee() {
        return $this->hasMany('App\Model\Club\ClubServiceFee', 'club_id');
    }

    public function clubinfo_taxconfig() {
        return $this->hasMany('App\Model\Tax\Taxtype', 'club_id');
    }

    public function clubinfo_clubopenhour() {
        return $this->hasMany('App\Model\Club\ClubOpenHour', 'club_id');
    }

    public function club_buddyreferrallead() {
        return $this->hasMany('App\Model\Guest\BuddyReferralLead', 'club_id');
    }

    public function club_productinv() {
        return $this->hasMany('App\Model\Product\ProductInventory', 'club_id');
    }

    public function fromclub_logproductinvtransfer() {
        return $this->hasMany('App\Model\Log\LogProductInventoryTransfer', 'from_club');
    }

    public function club_productpayment() {
        return $this->hasMany('App\Model\Product\ProductPayment', 'club_id');
    }

    public function club_memberhold() {
        return $this->hasMany('App\Model\Member\MemberHold', 'club_id');
    }

    public function club_transferhistory() {
        return $this->hasMany('App\Model\Staff\TransferHistory', 'club_id');
    }

    public function club_lockerinfo(){
        return $this->hasMany('App\Model\Locker\LockerInfo','club_id');
    }

    public function club_locker() {
        return $this->hasMany('App\Model\MasterData\Locker', 'club_id');
    }

    public function club_productrequest(){
        return $this->hasMany('App\Model\Product\ProductRequest','club_id');
    }

    public function targetclub_productrequest(){
        return $this->hasMany('App\Model\Product\ProductRequest','target_club');
    }

    public function club_visitingmemberworkout(){
        return $this->hasMany('App\Model\Member\VisitingMemberWorkout','club_id');
    }

    public function club_camanagement() {
        return $this->hasMany('App\Model\Report\CAManagement', 'club_id');
    }

    public function club_workout() {
        return $this->hasMany('App\Model\Workout\Workout', 'club_id');
    }

    public function club_coachsequence() {
        return $this->hasMany('App\Model\Staff\CoachSequence', 'club_id');
    }

    public function club_coachleadsequence(){
        return $this->hasMany('App\Model\Guest\CoachLeadSequence', 'club_id');
    }

    public function club_accountingjournal(){
        return $this->hasMany('App\Model\Accounting\AccountingJournal','club_id');
    }

    public function club_memorialjournal(){
        return $this->hasMany('App\Model\Accounting\MemorialJournal','club_id');
    }

    public function club_logmanualtransaction(){
        return $this->hasMany('App\Model\Log\LogManualTransaction','club_id');
    }

    public function club_guestwo() {
        return $this->hasMany('App\Model\GuestMembership\GuestWO', 'club_id');
    }

//    public function club_clubgm() {
//        return $this->hasMany('App\Model\GuestMembership\ClubGuestMembership', 'club_id');
//    }

    public function club_purchasingbook(){
        return $this->hasMany('App\Model\Accounting\PurchasingBook','club_id');
    }

    public function club_co() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrder', 'club_dest_id');
    }

    public function hq_co() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrder', 'hq_from_id');
    }

//    public function club_cobill() {
//        return $this->hasMany('App\Model\HQ\ConsignmentOrderBill', 'club_dest_id');
//    }

    public function hq_cobill() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBill', 'hq_from_id');
    }

    public function club_accjournalv2() {
        return $this->hasMany('App\Model\Accounting\AccountingJournalV2', 'club_id');
    }

    public function club_tempamorjournal() {
        return $this->hasMany('App\Model\Accounting\TempAmortizationJournal', 'club_id');
    }

    public function club_amordetail() {
        return $this->hasMany('App\Model\Accounting\AmortizationDetail', 'club_id');
    }

    public function club_meb() {
        return $this->hasMany('App\Model\Accounting\MonthlyEndingBalance', 'club_id');
    }

    public function club_staffattendance(){
        return $this->hasMany('App\Model\Staff\StaffAttendance','club_id');
    }

    public function toclub_logproductinvtransfer() {
        return $this->belongsTo('App\Model\Log\LogProductInventoryTransfer', 'to_club');
    }

    public function club_jubilant() {
        return $this->belongsTo('App\Model\Jubilant\Jubilant', 'jubilant_id');
    }

    public function club_globalevent(){
        return $this->belongsToMany('App\Model\Event\GlobalEvent', 'club_global_event', 'club_id', 'global_event_id')->withTimestamps();
    }

    public function club_globalmessage(){
        return $this->belongsToMany('App\Model\Member\GlobalMessage', 'club_global_messages', 'club_id', 'global_message_id')->withTimestamps();
    }

    public function club_manager() {
        return $this->belongsToMany('App\User', 'manager_class_clubs', 'club_id', 'manager_id')->withTimestamps();
    }

    public function club_bank(){
        return $this->belongsToMany('App\Model\MasterData\Bank', 'bank_club', 'club_id', 'id_bank')->withTimestamps();
    }

    public function club_accounting() {
        return $this->belongsToMany('App\Model\Accounting\AccountingAccount', 'accounting_clubs', 'club_id', 'accounting_id')->withTimestamps();
    }

    public function clubgm_gm() {
        return $this->belongsToMany('App\Model\GuestMembership\GuestMembership', 'club_guest_memberships', 'club_id', 'gm_id')->withTimestamps();
    }

    public function SetClubNameAttribute($value) {
        $this->attributes['club_name'] = strtolower($value);
    }

    public function GetClubNameAttribute($value) {
        return ucwords($value);
    }

    public function club_tempjournal() {
        return $this->hasMany('App\Model\Accounting\TempJournal', 'club_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
