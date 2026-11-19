<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getPrettyNameClubAttribute(){
        return $this->name.' ('.$this->user_club->club_name.')';
    }

    public function user_memberpersonal() {
        return $this->hasOne('App\Model\Member\MemberPersonal', 'user_id');
    }

    public function user_memberstatus() {
        return $this->hasOne('App\Model\Member\MemberStatus', 'user_id');
    }

    public function user_staffpersonal() {
        return $this->hasOne('App\Model\Staff\StaffPersonal', 'user_id');
    }

    public function user_staffstatus() {
        return $this->hasOne('App\Model\Staff\StaffStatus', 'user_id');
    }

    public function user_club() {
        return $this->belongsTo('App\Model\Club\ClubInfo', 'club_id');
    }

    /*public function user_role() {
        return $this->hasOne('App\Role', 'id');
    }*/

    public function user_emergencycontact() {
        return $this->hasOne('App\Model\Member\EmergencyContact', 'user_id');
    }

    public function user_memberhealthhistory() {
        return $this->hasOne('App\Model\Member\MemberHealthHistory', 'user_id');
    }

    public function user_membercorporation() {
        return $this->hasOne('App\Model\Corporation\MemberCorporation', 'user_id');
    }

    public function user_membermessagenote() {
        return $this->hasOne('App\Model\Member\MemberMessageNote', 'user_id');
    }

    public function memberuser_coachmember() {
        return $this->hasOne('App\Model\Member\CoachMember', 'member_id');
    }

    public function user_coachsequence() {
        return $this->hasOne('App\Model\Staff\CoachSequence', 'coach_id');
    }

    public function user_coachleadsequence(){
        return $this->hasOne('App\Model\Guest\CoachLeadSequence', 'coach_lead_id');
    }

    public function user_staffpoint(){
        return $this->hasOne('App\Model\Staff\StaffPoint','user_id');
    }

    public function user_staffcode(){
        return $this->hasOne('App\Model\Staff\StaffCode', 'user_id');
    }

    public function sales_memberpersonal() {
        return $this->hasMany('App\Model\Member\MemberPersonal', 'sales_person_id');
    }

    public function createdby_memberpersonal() {
        return $this->hasMany('App\Model\Member\MemberPersonal', 'created_by');
    }

    public function user_buddyreferrallead() {
        return $this->hasMany('App\Model\Guest\BuddyReferralLead', 'staff_id');
    }

    public function user_referrer() {
        return $this->hasMany('App\Model\Guest\Referrer', 'user_id');
    }

    public function user_rescheduledappointment() {
        return $this->hasMany('App\Model\Appointment\RescheduledAppointment', 'rescheduled_by');
    }

    public function user_appointment() {
        return $this->hasMany('App\Model\Appointment\MemberAppointment', 'user_id');
    }

    public function user_memberpayment() {
        return $this->hasMany('App\Model\Payment\MemberPayment', 'user_id');
    }

    public function user_logcontactedlead() {
        return $this->hasMany('App\Model\Log\LogContactedLead', 'staff_id');
    }

    public function member_weighmeasure() {
        return $this->hasMany('App\Model\WM\WeighMeasure', 'user_id');
    }

    public function measuredby_weighmeasure() {
        return $this->hasMany('App\Model\WM\WeighMeasure', 'measured_by');
    }

    public function member_weighmeasuregoal() {
        return $this->hasMany('App\Model\WM\WeighMeasureGoal', 'user_id');
    }

    public function member_xfactor() {
        return $this->hasMany('App\Model\WM\Xfactor', 'user_id');
    }

    public function user_workout() {
        return $this->hasMany('App\Model\Workout\Workout', 'user_id');
    }

    public function user_staffworkinghour() {
        return $this->hasMany('App\Model\Staff\StaffWorkingHour', 'user_id');
    }

    public function user_staffattendance() {
        return $this->hasMany('App\Model\Staff\StaffAttendance', 'user_id');
    }

    public function user_logcontactednewlead() {
        return $this->hasMany('App\Model\Log\LogContactedNewLead', 'staff_id');
    }

    public function user_refundsubmission() {
        return $this->hasMany('App\Model\Refund\RefundSubmission', 'user_id');
    }

    public function submitter_refundsubmission() {
        return $this->hasMany('App\Model\Refund\RefundSubmission', 'submit_by');
    }

    public function user_rewardhistory() {
        return $this->hasMany('App\Model\Log\RewardHistory', 'user_id');
    }

    public function cashier_productpayment() {
        return $this->hasMany('App\Model\Product\ProductPayment', 'cashier_id');
    }

    public function user_memberhold() {
        return $this->hasMany('App\Model\Member\MemberHold', 'user_id');
    }

    public function user_memberthirtyday() {
        return $this->hasMany('App\Model\Member\MemberThirtyDay', 'user_id');
    }

    public function staff_memberthirtyday() {
        return $this->hasMany('App\Model\Member\MemberThirtyDay', 'staff_id');
    }

    public function user_transferhistory() {
        return $this->hasMany('App\Model\Staff\TransferHistory', 'user_id');
    }

	public function user_memberphoto() {
        return $this->hasMany('App\Model\Member\MemberPhoto', 'member_id');
    }

    public function user_memberlocker() {
        return $this->hasMany('App\Model\Locker\MemberLocker', 'user_id');
    }

    public function user_productrefund() {
        return $this->hasMany('App\Model\Product\ProductRefund', 'staff_id');
    }

    public function user_guestappointment(){
        return $this->hasMany('App\Model\Appointment\GuestAppointment','staff_id');
    }

	public function user_continuemembership() {
        return $this->hasMany('App\Model\Member\ContinueMembership', 'user_id');
    }

    public function staff_continuemembership() {
        return $this->hasMany('App\Model\Member\ContinueMembership', 'staff_id');
    }

    public function staff_continuemembershiprefund() {
        return $this->hasMany('App\Model\Refund\ContinueMembershipRefund', 'submit_by');
    }

    public function user_membermonitoring(){
        return $this->hasMany('App\Model\Member\MemberMonitoring','user_id');
    }

    public function staff_membermonitoring(){
        return $this->hasMany('App\Model\Member\MemberMonitoring','staff_id');
    }

    public function user_memberdeposit() {
        return $this->hasMany('App\Model\Member\MemberDeposit', 'user_id');
    }

    public function user_productpayment() {
        return $this->hasMany('App\Model\Product\ProductPayment', 'member_id');
    }

    public function user_logproductinventorycorrection() {
        return $this->hasMany('App\Model\Log\LogProductInventoryCorrection', 'staff_id');
    }

    public function user_productshipment(){
        return $this->hasMany('App\Model\Product\ProductShipment','staff_id');
    }

    public function user_staffcertification(){
        return $this->hasMany('App\Model\Staff\StaffCertification','staff_id');
    }

	public function user_staffinactive() {
        return $this->hasMany('App\Model\Staff\StaffInactive', 'staff_id');
    }

    public function user_staffnote() {
        return $this->hasMany('App\Model\Staff\StaffNote', 'user_id');
    }

    public function user_travelpass(){
        return $this->hasMany('App\Model\Member\TravelPass','user_id');
    }

    public function user_logmemberstatus() {
        return $this->hasMany('App\Model\Log\LogMemberStatus', 'user_id');
    }

    public function user_inactivereason() {
        return $this->hasMany('App\Model\Member\InactiveMembershipReason', 'user_id');
    }

    public function user_brokenproduct(){
        return $this->hasMany('App\Model\Product\BrokenProduct','staff_id');
    }

    public function coachuser_coachmember() {
        return $this->hasMany('App\Model\Member\CoachMember', 'coach_id');
    }

    public function user_logstaffpointacquisition(){
        return $this->hasMany('App\Model\Log\LogStaffPointAcquisition','user_id');
    }

    public function user_mainaccount(){
        return $this->hasMany('App\Model\Account\MainAccount','user_id');
    }

    public function user_chartofaccount(){
        return $this->hasMany('App\Model\Account\ChartOfAccount','user_id');
    }

    public function user_wmrank(){
        return $this->hasMany('App\Model\WM\WMRank','user_id');
    }

    public function staff_miamonitoring() {
        return $this->hasMany('App\Model\MIAMonitoring', 'caller_id');
    }

    public function member_miamonitoring() {
        return $this->hasMany('App\Model\MIAMonitoring', 'member_id');
    }

    public function user_dailyhydration(){
        return $this->hasMany('App\Model\Hydration\DailyHydration','user_id');
    }

    public function user_loghydration(){
        return $this->hasMany('App\Model\Hydration\LogHydration','user_id');
    }

    public function user_memberhydration(){
        return $this->hasOne('App\Model\Hydration\MemberHydration','user_id');
    }

    public function user_logcoachtarget() {
        return $this->hasMany('App\Model\Log\LogCoachTarget', 'coach_id');
    }

    public function staff_gmdetail() {
        return $this->hasMany('App\Model\Guest\GuestMonitoringDetail', 'staff_id');
    }

    public function user_hqcredit() {
        return $this->hasMany('App\Model\HQ\HQCredit', 'staff_id');
    }

    public function staff_hqcreditpayment() {
        return $this->hasMany('App\Model\HQ\HQCreditPayment', 'staff_id');
    }

    public function staff_co() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrder', 'staff_id');
    }

    public function staff_cobill() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBill', 'staff_id');
    }

    public function staff_cobillpayment() {
        return $this->hasMany('App\Model\HQ\ConsignmentOrderBillPayment', 'staff_id');
    }

    public function user_amorheader() {
        return $this->hasMany('App\User', 'user_id');
    }

    public function user_cdrheader() {
        return $this->hasMany('App\Model\CoachDailyReport\CoachDailyReportHeader', 'coach_id');
    }

    public function member_staffreport() {
        return $this->belongsTo('App\Model\Member\StaffReport', 'member_id');
    }

    public function staff_staffreport() {
        return $this->belongsTo('App\Model\Member\StaffReport', 'staff_id');
    }

    public function user_role() {
        return $this->belongsTo('App\Model\Role\Role', 'role_id');
    }

    public function member_attribute() {
        return $this->belongsToMany('App\Model\MasterData\MemberAttribute', 'member_attributes', 'user_id', 'attribute_id')->withTimestamps();
    }

    public function staff_functionalrole() {
        return $this->belongsToMany('App\Model\Role\FunctionalRole', 'staff_functional_roles', 'user_id', 'functional_role_id')->withTimestamps();
    }

    public function user_accessmenu() {
        return $this->belongsToMany('App\Model\MasterData\AccessMenu', 'user_access', 'user_id', 'menu_id')->withTimestamps();
    }

    public function manager_club() {
        return $this->belongsToMany('App\Model\Club\ClubInfo', 'manager_class_clubs', 'manager_id', 'club_id')->withTimestamps();
    }

    public function user_clcoachpass(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClCoachPas', 'user_id');
    }

    public function user_clcoachanswer(){
        return $this->hasMany('App\Model\Curves\CurvesLearning\ClCoachAnswer', 'user_id');
    }

    public function user_logadditionalbonus() {
        return $this->hasMany('App\Model\Log\LogAdditionalBonus', 'user_id');
    }

    public function user_coachtarget() {
        return $this->hasMany('App\Model\Staff\CoachTarget', 'coach_id');
    }

    public function user_logdigitalproduct() {
        return $this->hasMany('App\Model\Log\LogDigitalProductInventory', 'user_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
