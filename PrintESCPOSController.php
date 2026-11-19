<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Appointment\GuestAppointment;
use App\Model\Appointment\GuestAppointmentStatus;
use App\Model\Club\ClubInfo;
use App\Model\DigitalProduct\DigitalProduct;
use App\Model\DigitalProduct\DigitalProductInventory;
use App\Model\DigitalProduct\DigitalProductPayment;
use App\Model\Guest\BuddyReferralLead;
use App\Model\Guest\Referrer;
use App\Model\GuestMembership\GuestMemberPayment;
use App\Model\GuestMembership\GuestMembership;
use App\Model\GuestMembership\GuestMembershipMember;
use App\Model\Locker\LockerInfo;
use App\Model\Locker\LockerPayment;
use App\Model\Locker\MemberLocker;
use App\Model\Log\LogMemberStatus;
use App\Model\MasterData\Bank;
use App\Model\MasterData\Locker;
use App\Model\MasterData\MembershipType;
use App\Model\MasterData\PaymentMethod;
use App\Model\Member\CoachMember;
use App\Model\Member\ContinueMembership;
use App\Model\Member\MemberPersonal;
use App\Model\Member\MemberStatus;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use App\Model\Product\Product;
use App\Model\Staff\CoachSequence;
use App\Model\WM\WeighMeasure;
use App\Model\Workout\Workout;
use App\User;
use App\Model\Product\ProductInventory;
use App\Model\Product\ProductPayment;
use App\Model\Product\ProductPaymentDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Keygen\Keygen;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ApiClientService;

//*********************************
// IMPORTANT NOTE
// ==============
// If your website requires user authentication, then
// THIS FILE MUST be set to ALLOW ANONYMOUS access!!!
//
//*********************************

//Includes WebClientPrint classes
//include_once(app_path() . '\WebClientPrint\WebClientPrint.php');
require_once __DIR__ . '/../../WebClientPrint/WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\DefaultPrinter;
use Neodynamic\SDK\Web\InstalledPrinter;
use Neodynamic\SDK\Web\ClientPrintJob;
use Throwable;

class PrintESCPOSController extends Controller
{
    public function cashierProduct(Request $request)
    {
        // Normalize Item List
        $item_list = $this->normalizeItemList($request);

        DB::beginTransaction();

        try {
            // Update auto number
            $payment_number = $this->getAutoNumber('1', $request->idprint);
            $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

            // Adding product payment
            $product_payment = new ProductPayment;
            $product_payment->club_id = $request->clubid;
            $product_payment->cashier_id = $request->staff;
            $product_payment->payment_method_id = $request->paymenttype;
            $product_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $request->clubid) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
            $product_payment->subtotal = round(10 / 11 * $request->subtotal);
            $product_payment->tax_type_id = 1;
            $product_payment->tax_value = $request->subtotal - $product_payment->subtotal;
            $product_payment->voucher_code = null;
            $product_payment->voucher_value = 0;
            $product_payment->total = $request->subtotal;
            $product_payment->pay = $request->pay;
            $product_payment->change = $request->pay - ($request->subtotal - $request->invoice_discount);
            $product_payment->is_reward = $request->status ? true : false;
            $product_payment->payment_date = $request->payment_date ?? Carbon::now();
            $product_payment->member_id = $request->buyer ?? null;
            $product_payment->bank_id = $request->paymenttype != 1 ? $request->bank : 1;
            $product_payment->invoice_discount = $request->invoice_discount;

            // Check if club is a franchise
            $club = ClubInfo::findOrFail($request->clubid);
            if ($club->club_jubilant->type == 'Franchise') :
                $product_payment->is_billed = false;
            endif;

            if ($request->idprint == 1) :
                $product_payment->save();

                
         

                //Accounting
                app('App\Http\Controllers\TempJournalController')->product_payment($product_payment, false);
            endif;

            // // Jonviter DEV
            //     $product_payment->payment_date = $product_payment->payment_date->toDateTimeString(); 
            //     $paymentData = $product_payment->toArray();
            //     $apiClient = new ApiClientService();
            //     $mobile_curves_api = $apiClient->post("/mobile-curves/payment-product/{$product_payment->member_id}", $paymentData);
            //     // Jonviter DEV
            // Add to product payment detail
            $this->insertProductPaymentDetail($item_list,$request->idprint,$product_payment->id,$request->status);

            DB::commit();

                // Jonviter DEV
                // $product_payment->payment_date = $product_payment->payment_date->toDateTimeString(); 
                // $paymentData = $product_payment->toArray();
                // $apiClient = new ApiClientService();
                // $mobile_curves_api = $apiClient->post("/mobile-curves/payment-product/{$request->buyer}", $paymentData);
                // Jonviter DEV
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $user = User::findOrFail($request->staff);
                if (!empty($request->buyer)) :
                    $pembeli = User::findOrFail($request->buyer);
                endif;

                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($user->user_club->club_name);

                if (!empty($pembeli)) :
                    $cmds .= "Terima Dari : " . $pembeli->name;
                else :
                    $cmds .= "Terima Dari : Guest";
                endif;
                $cmds .= $newLine;

                $cmds .= "No : " . $product_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($product_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                foreach ($item_list as $il) {
                    $cmds .= " " . $il->name;
                    $cmds .= $newLine;
                    $cmds .= new Item(" " . $il->qty . ' X ' . number_format((int)$il->price, 0, '', '.'), $il->qty * $il->price, 2);
                }
                $cmds .= $newLine;
                $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
                $cmds .= new Item(" Diskon : ", $request->invoice_discount, 2);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;
                $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
                $cmds .= new Item(" Total : ", $request->subtotal - $request->invoice_discount, 2);

                if ($request->paymenttype != 1) :
                    $cmds .= new Item(" " . $product_payment->productpayment_paymentmethod->payment_method . " " . $product_payment->productpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->paymenttype == 1) :
                    $cmds .= new Item(" " . $product_payment->productpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
                endif;

			
				$cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
                $cmds .= $newLine . $newLine;
				
				
                //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
                $cpj = new ClientPrintJob();
                //set ESCPOS commands to print...
                $cpj->printerCommands = $cmds;
                $cpj->formatHexValues = true;

                if ($useDefaultPrinter || $printerName === 'null') {
                    $cpj->clientPrinter = new DefaultPrinter();
                } else {
                    $cpj->clientPrinter = new InstalledPrinter($printerName);
                }

                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
    }

    private function normalizeItemList($request)
    {
        $itemlist = str_replace('%5B', '[', $request->itemlist);
        $itemlist = str_replace('%7B', '{', $itemlist);
        $itemlist = str_replace('%2522', '"', $itemlist);
        $itemlist = str_replace('%7D', '}', $itemlist);
        $itemlist = str_replace('%5D', ']', $itemlist);
        $itemlist = str_replace('%3F', '&', $itemlist);
        $itemlist = str_replace('%20', ' ', $itemlist);
        $item_list = json_decode(str_replace('%22', '"', $itemlist));
        
        return $item_list;
    }

    private function normalizeString($str){
        $itemlist = str_replace('%5B', '[', $str);
        $itemlist = str_replace('%7B', '{', $itemlist);
        $itemlist = str_replace('%2522', '"', $itemlist);
        $itemlist = str_replace('%7D', '}', $itemlist);
        $itemlist = str_replace('%5D', ']', $itemlist);
        $itemlist = str_replace('%3F', '&', $itemlist);
        $itemlist = str_replace('%20', ' ', $itemlist);
        $itemlist = str_replace('%22', '"', $itemlist);

        return $itemlist;
    }

    private function insertProductPaymentDetail($item_list,$no_reprint,$product_payment_id,$status){
        foreach ($item_list as $item) :
            $product_inv = ProductInventory::findOrFail($item->prid);
            //Add Product Payment Detail
            $product_payment_detail = new ProductPaymentDetail;
            $product_payment_detail->product_payment_id = $product_payment_id;
            $product_payment_detail->product_id = $product_inv->product_id;
            $product_payment_detail->price = $item->price;
            $product_payment_detail->qty = $item->qty;

            if ($status) {
                $product_payment_detail->discount_percent = 100;
                $product_payment_detail->discount_value = $product_payment_detail->price * $product_payment_detail->qty;
            } else {
                $product_payment_detail->discount_percent = $item->price == 0 ? 0 : round(($item->discount * 100) / $item->price, 2);
                $product_payment_detail->discount_value = $item->discount;
            }

            if ($no_reprint == 1) :
                $product_payment_detail->save();
            endif;

            //Update Product Inventory Stock
            $product_inv->qty = $product_inv->qty - $item->qty;
            if ($no_reprint == 1) :
                $product_inv->save();
            endif;

            // Update Product Inventory Stock if Product is_promo Added 06042020
            if ($product_inv->productinv_product->is_promo) :
                $product_promo = Product::findOrFail($product_inv->product_id);
                foreach ($product_promo->product_promoproduct as $promo) :
                    $check_promo_inv = ProductInventory::where('product_id', '=', $promo->product_id)
                        ->where('club_id', '=', $product_inv->club_id)
                        ->first();

                    if (!empty($check_promo_inv)) :
                        $promo_inv = ProductInventory::findOrFail($check_promo_inv->id);
                        $promo_inv->qty = $promo_inv->qty - ($item->qty * $promo->qty);
                        $promo_inv->save();
                    endif;
                endforeach;
            endif;
        endforeach;
    }

    public function cashierMembership(Request $request){
        DB::beginTransaction();

        try{
            $membership_type = MembershipType::findOrFail($request->membership_type);

            // Insert BuddyReferralead if not exist
            $lead = $this->updateBuddyReferralLead($request);

            // Add Guest Appointment and GuestAppointmentStatus
            $this->insertGuestAppointmentAndStatus($lead, $request->fa_date, $request->id, 'Member');

            //Add Member
            $user = $this->insertUser($lead,$request->club_id,$request->id);

            //Update Auto Number
            $payment_number = $this->getAutoNumber('1', $request->id);

            //Adding Member Payment
            $member_payment = $this->insertMemberPayment($payment_number, $request, $user->id, $request->id);

            // Adding Digital Product for membership purchase more than designated value
            $digital_product_inventory = $this->insertDigitalProduct($request->club_id, $member_payment, $request);

            //Adding MemberStatus
            $member_status = $this->insertMemberStatus($request, $user);

            //Adding LogMemberStatus (Log for Member's Membership History)
            $this->insertLogMemberStatus($user->id, $member_status, $request);

            //Adding MemberPersonal (member_personals table)
            $this->insertMemberPersonal($request, $lead->id, $user);

            //Adding Member to Coach based on Sequence
            $club = ClubInfo::findOrFail($request->club_id);
            // TODO ini harus di fix jika coach sequence belum dibuat
            $coach_sequences = CoachSequence::where('club_id', '=', $club->id)
                ->orderBy('sequence', 'desc')
                ->get();

            $max_seq = $coach_sequences[0]->sequence;
            foreach ($coach_sequences->where('sequence', $club->current_sequence) as $coach_seq) :
                //Insert to coach member table
                $coach_member = new CoachMember;
                $coach_member->coach_id = $coach_seq->coach_id;
                if ($request->id == 1 && !$user->memberuser_coachmember()->exists()) :
                    $user->memberuser_coachmember()->save($coach_member);
                endif;

                //Update club current sequence
                $club->current_sequence = $club->current_sequence < $max_seq ? $club->current_sequence + 1 : 1;
                
                if ($request->id == 1) :
                    $club->save();
                endif;
            endforeach;

            //Staff Point
            if ($request->id == 1) :
                // Coach Point
                app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
                app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');
                app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);
                app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);
                
                // Amortisasi
                app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, null);

                //Akunting
                app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
                app('App\Http\Controllers\AccountingJournalController')->membership_journal($member_payment, $membership_type);

            endif;

            DB::commit();

            //Print
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $club = ClubInfo::findOrFail($request->club_id);
                $user = User::findOrFail($request->fa_coach);
                $member_type = MembershipType::findOrFail($request->membership_type);

                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($club->club_name);

                $cmds .= "Terima Dari : " . $lead->fullname;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $member_type->membership_type_name;
                $cmds .= $newLine;

                $harga_digital_product_bool = false;
                if (($request->digital_product_check == 'true') || ($request->digital_product_check == true)) {
                    if (!empty($member_payment->id)) :
                        $member_payment = MemberPayment::findOrFail($member_payment->id);
                    endif;

                    if ($request->for == "club") {
                        if (!empty($member_payment->memberpayment_digitalproductpayment)) :
                            $harga_digital_product = $member_payment->memberpayment_digitalproductpayment->price;
                            if ($request->pay >= $harga_digital_product) {
                                $harga_digital_product_bool = true;
                                $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount - $harga_digital_product, 2);
                                $cmds .= new Item(" " . $digital_product_inventory->digitalproductinventory_digitalproduct->item_name . " : ", $member_payment->memberpayment_digitalproductpayment->price, 2);
                            } else {
                                $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                                $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                            }
                        else :
                            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                        endif;
                    } else {
                        $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount, 2);
                    }
                } else {
                    $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                    $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                if ($harga_digital_product_bool) {
                    $cmds .= new Item(" Total : ", $request->total /*+ $harga_digital_product*/, 2);
                } else {
                    $cmds .= new Item(" Total : ", $request->total, 2);
                }

                if ($request->payment_method != 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->payment_method == 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
                $cmds .= $newLine;

                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "Curves Indonesia     ";
                // $cmds .= $newLine . $newLine;
                // $cmds .= '';
                // $cmds .= $newLine . $newLine;
                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "(________________________)";

                // $cmds .= $newLine;
                // $cmds .= $esc . 'a' . '0';
                // $cmds .= "[ ] Update Data";

                $cmds .= $newLine . $newLine;
                $cmds .= '';
                $cmds .= $newLine . $newLine;

                //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
                $cpj = new ClientPrintJob();
                //set ESCPOS commands to print...
                $cpj->printerCommands = $cmds;
                $cpj->formatHexValues = true;

                if ($useDefaultPrinter || $printerName === 'null') {
                    $cpj->clientPrinter = new DefaultPrinter();
                } else {
                    $cpj->clientPrinter = new InstalledPrinter($printerName);
                }

                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
    }



    public function cashierMembershipWithEzypay(Request $request){
        DB::beginTransaction();

        try{
            $membership_type = MembershipType::findOrFail($request->membership_type);

            // Insert BuddyReferralead if not exist
            $lead = $this->updateBuddyReferralLead($request);

            // Add Guest Appointment and GuestAppointmentStatus
            $this->insertGuestAppointmentAndStatus($lead, $request->fa_date, $request->id, 'Member');

            //Add Member
            $user = $this->insertUser($lead,$request->club_id,$request->id);

            //Update Auto Number
            $payment_number = $this->getAutoNumber('1', $request->id);

            //Adding Member Payment
            $member_payment = $this->insertMemberPayment($payment_number, $request, $user->id, $request->id);

            // Adding Digital Product for membership purchase more than designated value
            $digital_product_inventory = $this->insertDigitalProduct($request->club_id, $member_payment, $request);

            //Adding MemberStatus
            $member_status = $this->insertMemberStatus($request, $user);

            //Adding LogMemberStatus (Log for Member's Membership History)
            $this->insertLogMemberStatus($user->id, $member_status, $request);

            //Adding MemberPersonal (member_personals table)
            $this->insertMemberPersonal($request, $lead->id, $user);

            //Adding Member to Coach based on Sequence
            $club = ClubInfo::findOrFail($request->club_id);
            // TODO ini harus di fix jika coach sequence belum dibuat
            $coach_sequences = CoachSequence::where('club_id', '=', $club->id)
                ->orderBy('sequence', 'desc')
                ->get();

            $max_seq = $coach_sequences[0]->sequence;
            foreach ($coach_sequences->where('sequence', $club->current_sequence) as $coach_seq) :
                //Insert to coach member table
                $coach_member = new CoachMember;
                $coach_member->coach_id = $coach_seq->coach_id;
                if ($request->id == 1 && !$user->memberuser_coachmember()->exists()) :
                    $user->memberuser_coachmember()->save($coach_member);
                endif;

                //Update club current sequence
                $club->current_sequence = $club->current_sequence < $max_seq ? $club->current_sequence + 1 : 1;
                
                if ($request->id == 1) :
                    $club->save();
                endif;
            endforeach;

            //Staff Point
            if ($request->id == 1) :
                // Coach Point
                app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
                app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');
                app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);
                app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);
                
                // Amortisasi
                app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, null);

                //Akunting
                app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
                app('App\Http\Controllers\AccountingJournalController')->membership_journal($member_payment, $membership_type);

            endif;

            DB::commit();

            //Print
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $club = ClubInfo::findOrFail($request->club_id);
                $user = User::findOrFail($request->fa_coach);
                $member_type = MembershipType::findOrFail($request->membership_type);

                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($club->club_name);

                $cmds .= "Terima Dari : " . $lead->fullname;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $member_type->membership_type_name;
                $cmds .= $newLine;

                $harga_digital_product_bool = false;
                if (($request->digital_product_check == 'true') || ($request->digital_product_check == true)) {
                    if (!empty($member_payment->id)) :
                        $member_payment = MemberPayment::findOrFail($member_payment->id);
                    endif;

                    if ($request->for == "club") {
                        if (!empty($member_payment->memberpayment_digitalproductpayment)) :
                            $harga_digital_product = $member_payment->memberpayment_digitalproductpayment->price;
                            if ($request->pay >= $harga_digital_product) {
                                $harga_digital_product_bool = true;
                                $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount - $harga_digital_product, 2);
                                $cmds .= new Item(" " . $digital_product_inventory->digitalproductinventory_digitalproduct->item_name . " : ", $member_payment->memberpayment_digitalproductpayment->price, 2);
                            } else {
                                $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                                $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                            }
                        else :
                            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                        endif;
                    } else {
                        $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount, 2);
                    }
                } else {
                    $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                    $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                if ($harga_digital_product_bool) {
                    $cmds .= new Item(" Total : ", $request->total /*+ $harga_digital_product*/, 2);
                } else {
                    $cmds .= new Item(" Total : ", $request->total, 2);
                }

                if ($request->payment_method != 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->payment_method == 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
                $cmds .= $newLine;

                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "Curves Indonesia     ";
                // $cmds .= $newLine . $newLine;
                // $cmds .= '';
                // $cmds .= $newLine . $newLine;
                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "(________________________)";

                // $cmds .= $newLine;
                // $cmds .= $esc . 'a' . '0';
                // $cmds .= "[ ] Update Data";

                $cmds .= $newLine . $newLine;
                $cmds .= '';
                $cmds .= $newLine . $newLine;

                //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
                $cpj = new ClientPrintJob();
                //set ESCPOS commands to print...
                $cpj->printerCommands = $cmds;
                $cpj->formatHexValues = true;

                if ($useDefaultPrinter || $printerName === 'null') {
                    $cpj->clientPrinter = new DefaultPrinter();
                } else {
                    $cpj->clientPrinter = new InstalledPrinter($printerName);
                }

                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
    }

    private function updateBuddyReferralLead($request) : BuddyReferralLead{
        $prename = str_replace('%20', ' ', $request->premember_name);
        $lead = BuddyReferralLead::find($request->lead);

        if (empty($lead)) :
            //Buddy Referral Lead
            $lead = new BuddyReferralLead;
            $lead->fullname = $prename;
            $lead->phone = $request->phone;
            $lead->source_id = $request->source;

            if ($request->source == '1') :
                $lead->is_br = 'BR';
            else :
                $lead->is_br = 'NotBR';
            endif;

            if (!empty($request->login_user)) :
                $lead->staff_id = $request->fa_coach;
            endif;

            $lead->club_id = $request->club_id;
        endif;

        //Update status brl
        $lead->status_gm = 'Member';
        if ($request->id == 1) :
            $lead->save();
        endif;
        
        if ($request->source == '1') {
            $check = Referrer::where('referral_id', '=', $lead->id)->first();

            if (empty($check)) {
                $referrer = new Referrer;
            } else {
                $referrer = $check;
            }

            $referrer->user_id = !empty($request->source_detail) ? $request->source_detail : 0;
            $referrer->status = 'Pending';
            // $lead->buddyreferrallead_referrer()->save($referrer);

        } else if (in_array($request->source, ['5', '14', '15'])) {
            //Facebook IG Papan Nama
            $lead->source_detail = $this->normalizeString($request->source_detail);
            if ($request->id == 1) :
                $lead->save();
            endif;
        } else if (in_array($request->source, ['10', '12', '13', '17'])) {
            $lead->location = $this->normalizeString($request->source_detail);
            if ($request->id == 1) :
                $lead->save();
            endif;
        }

        return $lead;
    }

    private function insertGuestAppointmentAndStatus($lead, $fa_date, $no_reprint, $status_after){
        //Guest Appointment
        $guest_appt = new GuestAppointment;
        $guest_appt->guest_id = $lead->id;
        $guest_appt->staff_id = $lead->staff_id;
        $guest_appt->fa_status = 'NonFA';

        $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($fa_date . ' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

        $guest_appt->fa_date = $datetime;
        $guest_appt->fa_hour = date('H:i', strtotime($datetime));
        $guest_appt->guest_status_after = $status_after;
        $guest_appt->club_id = $lead->club_id;
        if ($no_reprint == 1) :
            $guest_appt->save();
        endif;

        //Guest Appointment Status
        $guest_appt_status = new GuestAppointmentStatus;
        $guest_appt_status->status_id = '5';
        $guest_appt_status->c_date = $datetime;
        $guest_appt_status->s_date = $datetime;
        $guest_appt_status->sa_date = $datetime;
        if ($no_reprint == 1) :
            $guest_appt->guestappointment_gastatus()->save($guest_appt_status);
        endif;
    }

    private function insertUser($lead, $club_id, $no_reprint = 0){
        $user = new User;
        $user->name = $lead->fullname;
        $user->email = date('dmYhis', strtotime(Carbon::now())) . '@curvesmember.com';
        $user->password = bcrypt('curvesindonesia');
        $user->role_id = '7';
        $user->club_id = $club_id;
        $user->username = date('dnygi', strtotime(Carbon::now())) . '.cm.' . $lead->id;
        if ($no_reprint == 1) :
            $user->save();
        endif;

        return $user;
    }

    private function insertMemberPayment($payment_number, $request, $user_id, $no_reprint){
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $request->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $member_payment->user_id = $user_id;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;
        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_payment->payment_date = $request->fa_date;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;
        $member_payment->rest_of_bill = ($request->total - $request->pay) <= 0 ? 0 : $request->total - $request->pay;
        $member_payment->payment_status = $member_payment->rest_of_bill <= 0 ? 'Paid' : 'Halfpaid';
        $member_payment->bank_id = $request->payment_method != 1 ? $request->bank : 1;

        if($no_reprint == 1){
            $member_payment->save();
        }

        return $member_payment;
    }

    private function insertMemberStatus($request, $user){
        $membership_type = MembershipType::findOrFail($request->membership_type);

        $member_status = new MemberStatus;
        $member_status->status = 'Active';
        $member_status->membership_type_id = $request->membership_type;

        if ($membership_type->is_vip == 'general') :
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip') :
            $the_duration = 0;
        endif;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_status->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($member_status->from_date)));
        else :
            $member_status->from_date = $request->fa_date;
            $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($request->fa_date)));
        endif;

        // Added 19032020
        if (!empty($request->bonus_type) && !empty($request->bonus_duration)) :
            if ($request->bonus_type == 'day') :
                $thru_date_temp = Carbon::parse($thru_date_temp)->addDays($request->bonus_duration);
            elseif ($request->bonus_type == 'week') :
                $thru_date_temp = Carbon::parse($thru_date_temp)->addWeeks($request->bonus_duration);
            elseif ($request->bonus_type == 'month') :
                $thru_date_temp = Carbon::parse($thru_date_temp)->addMonths($request->bonus_duration);
            endif;
        endif;
        // Added 19032020

        $member_status->thru_date = $thru_date_temp;

        if ($membership_type->payment_type == 'Prepaid') :
            if (($request->total - $request->pay) == 0) :
                $member_status->payment_status = 'Set';
            endif;
        elseif ($membership_type->payment_type == 'Cash') :
            $member_status->payment_status = 'Set';
        endif;

        if ($request->id == 1) :
            $user->user_memberstatus()->save($member_status);
        endif;

        return $member_status;
    }

    private function insertLogMemberStatus($user_id, $member_status, $request){
        $log_member = new LogMemberStatus;
        $log_member->user_id = $user_id;
        $log_member->membership_type_id = $request->membership_type;
        $log_member->from_date = $member_status->from_date;
        $log_member->thru_date = $member_status->thru_date;
        $log_member->club_id = $request->club_id;
        if ($request->id == 1) :
            $log_member->save();
        endif;
    }

    private function insertMemberPersonal($request, $lead_id, $user){
        $member_personal = new MemberPersonal;
        $member_personal->phone = $request->phone;
        $member_personal->key_tag_id = '99' . Keygen::numeric(7)->generate();

        if (!empty($request->fa_coach)) :
            $member_personal->sales_person_id = $request->fa_coach;
        endif;

        $member_personal->guest_id = $lead_id;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_personal->joined = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_personal->joined = $request->fa_date;
        endif;

        $member_personal->created_by = $request->login_user;
        if ($request->id == 1) :
            $user->user_memberpersonal()->save($member_personal);
        endif;
    }

    private function insertDigitalProduct($club_id, $member_payment, $request){
        $digital_product_inventory = DigitalProductInventory::where('club_id', '=', $club_id)
            ->where('status', '=', true)->first();

        if ($request->id != 1) return; 
        if (($request->digital_product_check == 'false') || ($request->digital_product_check == false)) return;
        if (empty($digital_product_inventory)) return;

        $harga_digital_product = $digital_product_inventory->selling_price;
        if ($request->pay >= $harga_digital_product) {
            $digital_product_payment = new DigitalProductPayment;
            $digital_product_payment->member_payment_id = $member_payment->id;
            $digital_product_payment->digital_product_inventory_id = $digital_product_inventory->id;
            $digital_product_payment->price = $harga_digital_product;
            $digital_product_payment->qty = 1;
            $member_payment->memberpayment_digitalproductpayment()->save($digital_product_payment);

            // Update Digital Product Inventory Stock
            $digital_product_inventory->qty = $digital_product_inventory->qty - 1;
            $digital_product_inventory->save();
        }

        return $digital_product_inventory;
    }

    public function cashierLocker(Request $request)
    {
        DB::beginTransaction();

        try{
            //Update Auto Number
            $payment_number = $this->getAutoNumber('1', $request->id);

            //if the customer wants to rent a new locker
            $memberlocker = new MemberLocker;
            $memberlocker->user_id = $request->member;
            $memberlocker->from_date = $request->start_date;

            //check what package did the customer bought
            $choosenlocker = Locker::findOrFail($request->packet);
            $memberlocker->thru_date = $this->setLockerDuration($choosenlocker, $request->start_date, $request->amount);
            $memberlocker->locker_info_id = $request->locker;
            $memberlocker->is_key_returned = 'No';
            if ($request->id == 1) :
                $memberlocker->save();
            endif;

            //Add to locker payment
            $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $locker_payment = new LockerPayment;
            $locker_payment->memberlocker_id = $memberlocker->id;
            $locker_payment->locker_id = $request->packet;
            $locker_payment->payment_number = sprintf('%08d', $payment_number) . $request->payment_number . '/PY/CIND/' . sprintf('%03d', $request->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
            $locker_payment->staff_id = $request->staff;
            $locker_payment->price = $choosenlocker->price;
            $locker_payment->quantity = $request->amount;
            $locker_payment->subtotal = round(10 / 11 * $request->billingtotal);
            $locker_payment->pay = $request->pay;
            $locker_payment->paymentmethod_id = $request->payment_method;
            $locker_payment->payment_date = $request->payment_date;
            $locker_payment->taxtype_id = 1;
            $locker_payment->tax_amount = $request->billingtotal - $locker_payment->subtotal;
            $locker_payment->bank_id = $request->payment_method == 1 ? 1 : $request->bank;

            if ($request->id == 1) :
                $locker_payment->save();

                
                // Jonviter DEV
                // $locker_payment->payment_date = $locker_payment->payment_date->toDateTimeString(); 
                // $paymentData = $locker_payment->toArray();
                // $apiClient = new ApiClientService();
                // $mobile_curves_api = $apiClient->post("/mobile-curves/locker-payment/{$locker_payment->id}", $paymentData);
                // Jonviter DEV
                app('App\Http\Controllers\TempJournalController')->locker_payment($locker_payment, false);
                //            app('App\Http\Controllers\AccountingJournalController')->locker_payment_journal($locker_payment);

                // Added 25102019
                app('App\Http\Controllers\PointStaffController')->locker_point($request->staff, $locker_payment->id);
            endif;

            DB::commit();

            //Print
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $club = ClubInfo::findOrFail($request->club_id);
                $user = User::findOrFail($request->staff);

                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($club->club_name);

                $cmds .= "Terima Dari : " . $memberlocker->memberlocker_user->name;
                $cmds .= $newLine;
                $cmds .= "No : " . $locker_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($locker_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $memberlocker->memberlocker_lockerinfo->locker_name . " - " . $choosenlocker->name;
                $cmds .= $newLine;

                $cmds .= new Item(" Mulai Pinjaman : ", Carbon::parse($request->start_date)->format('d-m-Y'), 1);
                $cmds .= new Item(" Selesai Pinjaman : ", Carbon::parse($memberlocker->thru_date)->format('d-m-Y'), 1);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= new Item(" Total : ", $request->billingtotal, 2);

                if ($request->payment_method != 1) :
                    $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " " . $locker_payment->lockerpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->payment_method == 1) :
                    $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
                endif;

                $cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
                $cmds .= $newLine . $newLine;
 

                //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
                $cpj = new ClientPrintJob();
                //set ESCPOS commands to print...
                $cpj->printerCommands = $cmds;
                $cpj->formatHexValues = true;

                if ($useDefaultPrinter || $printerName === 'null') {
                    $cpj->clientPrinter = new DefaultPrinter();
                } else {
                    $cpj->clientPrinter = new InstalledPrinter($printerName);
                }

                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
    }

    private function setLockerDuration($choosenlocker, $start_date, $amount){
        if ($choosenlocker->duration_type == 'monthly') {
            return Carbon::parse($start_date)->addMonths($choosenlocker->duration * $amount);
        } else if ($choosenlocker->duration_type == 'daily') {
            return Carbon::parse($start_date)->addDays($choosenlocker->duration * $amount);
        } else if ($choosenlocker->duration_type == 'yearly') {
            return Carbon::parse($start_date)->addYears($choosenlocker->duration * $amount);
        }
    }

    public function cashierRenewal(Request $request)
    {
        $payment_number = $this->getAutoNumber('1', $request->id);

        $user = User::findOrFail($request->selectedmember);
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

        //Adding Member Payment
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $request->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $member_payment->user_id = $request->selectedmember;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;

        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_payment->payment_date = $request->fa_date;
        endif;

        if ($request->payment_method != 1) :
            $member_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1) :
            $member_payment->bank_id = 1;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;

        if (($request->total - $request->pay) > 0) :
            $member_payment->rest_of_bill = ($request->total - $request->pay);
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) <= 0) :
            $member_payment->payment_status = 'Paid';
        endif;

        // Renewal Status Added 20022020
        $member_payment->renewal = true;

        if ($request->id == 1) {
            $member_payment->save();
        }

        //Check if this member has Pending Continued Membership
        $check_continue = ContinueMembership::where('user_id', '=', $request->selectedmember)
            ->whereDate('from_date', '>=', Carbon::parse($user->user_memberstatus->thru_date))
            ->get();

        $start_from_date = Carbon::parse($user->user_memberstatus->thru_date)->addDay();

        if (count($check_continue) > 0) :
            foreach ($check_continue as $the_date) :
                if (strtotime($the_date->thru_date) > strtotime($start_from_date)) :
                    $start_from_date = Carbon::parse($the_date->thru_date)->addDay();
                endif;
            endforeach;
        endif;

        //If Reactivation added 05042018
        // NOT YET
        /*
         * if (!empty(session('reactivation'))):
            if (session('reactivation') == $user->id):
                $start_from_date = Carbon::parse($request->pay_date);
            endif;
           endif;
         */

        //Adding ContinueMembership (continue_memberships table)
        $membership_type = MembershipType::findOrFail($request->membership_type);

        $continue = new ContinueMembership;
        $continue->user_id = $user->id;
        $continue->membership_type_id = $membership_type->id;

        if ($membership_type->is_vip == 'general') :
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip') :
            $the_duration = 0;
        endif;

        //Cek Jika Perpanjangan dilakukan paling lambat 2 minggu sebelum keanggotaan berakhir Added 16022019
        if (($membership_type->duration == '12') || ($membership_type->duration == '18') || ($membership_type->duration == '24')) :
            $payment_diff_days = Carbon::parse($request->pay_date)->diffInDays(Carbon::parse($user->user_memberstatus->thru_date));

            if ($payment_diff_days >= 14) :
                if ($membership_type->is_vip == 'general') :
                    $the_duration += 1;
                endif;
            endif;
        endif;
        //End of Added 16022019

        $continue->from_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($start_from_date)->format('Y-m-d H:i'), session('timezone'));
        $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($continue->from_date)));

        //VIP Member added 02032018
        //NO VIP in CASHIER
        /*
         * if (!empty($request->use_vip) && !empty($request->vip_duration)):
            if ($request->vip_option == 'day'):
                $vip_option = 'days';
            elseif ($request->vip_option == 'month'):
                $vip_option = 'months';
            elseif ($request->vip_option == 'year'):
                $vip_option = 'years';
            endif;

            $continue->thru_date = date('Y-m-d', strtotime("+".$request->vip_duration." ".$vip_option, strtotime($thru_date_temp)));
            $continue->member_vip_status = 'VIP';
           else:
         */
        $continue->thru_date = $thru_date_temp;
        // endif;

        $continue->status = 'Pending';
        $continue->staff_id = $request->fa_coach;

        if (($membership_type->payment_type == 'Prepaid') || ($membership_type->payment_type == 'Cash')) :
            if (($request->total - $request->pay) == 0) :
                $continue->payment_status = 'Set';
            else :
                $continue->payment_status = 'NotSet';
            endif;
        endif;

        //Jika current membership = MM, unset payment_status
        /*
         *  $member_status = $user->user_memberstatus;
            $is_mm = MembershipType::findOrFail($member_status->membership_type_id);
            if (($is_mm->payment_type == 'Cash') && ($is_mm->is_vip == 'general')):
                $member_status->payment_status = 'NotSet';
                $user->user_memberstatus()->save($member_status);
            endif;
         */

        if ($request->id == 1) {
            $user->user_continuemembership()->save($continue);
        }

        //Adding LogMemberStatus (Log for Member's Membership History)
        $log_member = new LogMemberStatus;
        $log_member->user_id = $user->id;
        $log_member->membership_type_id = $membership_type->id;
        $log_member->from_date = $continue->from_date;
        $log_member->thru_date = $continue->thru_date;
        $log_member->club_id = $request->club_id;
        if ($request->id == 1) {
            $log_member->save();
        }

        if ($request->id == 1) :
            // Amortization
            app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, Carbon::parse($user->user_memberstatus->thru_date)->addDay(1));

            // Accounting
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
            app('App\Http\Controllers\AccountingJournalController')->membership_renewal_journal($member_payment, $membership_type);
        endif;

        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $club = ClubInfo::findOrFail($request->club_id);
            $staff = User::findOrFail($request->fa_coach);
            $member_type = MembershipType::findOrFail($request->membership_type);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            $cmds .= "Terima Dari : " . $user->name;
            $cmds .= $newLine;
            $cmds .= "No : " . $member_payment->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($staff->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= " " . $member_type->membership_type_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
            $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->total, 2);

            if ($request->payment_method != 1) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
            elseif ($request->payment_method == 1) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
            endif;

            $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
            $cmds .= $newLine;

            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "Curves Indonesia     ";
            // $cmds .= $newLine . $newLine;
            // $cmds .= '';
            // $cmds .= $newLine . $newLine;
            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "(________________________)";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Update Data";

            $cmds .= $newLine . $newLine;
            $cmds .= '';
            $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
        }
    }

    public function cashierInstallment(Request $request)
    {
        //Print KEANGGOTAAN
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {

            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $member_payment = MemberPayment::findOrFail($request->id);
            $club = ClubInfo::findOrFail($request->club_id);
            //            $user = User::findOrFail($request->staff);
            $user = User::findOrFail($member_payment->memberpayment_user->user_memberpersonal->sales_person_id);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            if (($request->digital_product_check == 'true') || ($request->digital_product_check == true)) {

                $member_type = MembershipType::findOrFail($request->membership_type);
                $prename = str_replace('%20', ' ', $request->premember_name);

                $cmds .= "Terima Dari : " . $prename;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $member_type->membership_type_name;
                $cmds .= $newLine;

                $harga_digital_product_bool = false;
                if ($request->for == "club" and isset($member_payment->memberpayment_digitalproductpayment)) {
                    $harga_digital_product = $member_payment->memberpayment_digitalproductpayment->price;
                    if ($member_payment->amount_paid >= $harga_digital_product) {
                        $harga_digital_product_bool = true;
                        $cmds .= new Item(" Biaya : ", $member_payment->sf_amount + $member_payment->mf_amount - $harga_digital_product, 2);
                        $cmds .= new Item(" Harga Digital Product :", $member_payment->memberpayment_digitalproductpayment->price, 2);
                    } else {
                        $cmds .= new Item(" Biaya : ", $member_payment->sf_amount + $member_payment->mf_amount, 2);
                    }
                } else {
                    $cmds .= new Item(" Biaya : ", $member_payment->sf_amount + $member_payment->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $member_payment->dc_amount, 2);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                if ($harga_digital_product_bool) {
                    $cmds .= new Item(" Total : ", $member_payment->amount_due /*+ $harga_digital_product */, 2);
                } else {
                    $cmds .= new Item(" Total : ", $member_payment->amount_due, 2);
                }

                if ($member_payment->bank_id != 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $member_payment->amount_paid, 2);
                elseif ($member_payment->bank_id == 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $member_payment->amount_paid, 2);
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $member_payment->amount_due - $member_payment->amount_paid, 2);
                $cmds .= $newLine;

                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "Curves Indonesia     ";
                // $cmds .= $newLine . $newLine;
                // $cmds .= '';
                // $cmds .= $newLine . $newLine;
                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "(________________________)";

                // $cmds .= $newLine;
                // $cmds .= $esc . 'a' . '0';
                // $cmds .= "[ ] Update Data";
            } else {

                $cmds .= "Terima Dari : " . $member_payment->memberpayment_user->name;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                if ($member_payment->payment_category == 'First') {
                    $cmds .= " Pembayaran Pertama";
                    $cmds .= $newLine;
                    $cmds .= new Item("  " . Carbon::parse($member_payment->payment_date)->format('d-m-Y'), $member_payment->amount_paid, 2);
                } else {
                    $cmds .= new Item(" Cicilan " . Carbon::parse($member_payment->payment_date)->format('d-m-Y'), $member_payment->amount_paid, 2);
                }

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= new Item(" Total : ", $member_payment->amount_paid, 2);

                if ($member_payment->payment_method_id != 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $member_payment->amount_paid, 2);
                elseif ($member_payment->payment_method_id == 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $member_payment->amount_paid, 2);
                endif;


				
				$cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
          
            }

                $cmds .= $newLine . $newLine;
            
			
			

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function cashierMembershipDummy(Request $request){

        try {
            // Insert BuddyReferralead if not exist
            $prename = str_replace('%20', ' ', $request->premember_name);
            
            //Buddy Referral Lead
            $lead = new BuddyReferralLead;
            $lead->fullname = $prename;
            $lead->phone = $request->phone;
            $lead->source_id = $request->source;

            if ($request->source == '1') :
                $lead->is_br = 'BR';
            else :
                $lead->is_br = 'NotBR';
            endif;

            if (!empty($request->login_user)) :
                $lead->staff_id = $request->fa_coach;
            endif;

            $lead->club_id = $request->club_id;

            //Update status brl
            $lead->status_gm = 'Member';

            // Add Guest Appointment and GuestAppointmentStatus
            //Guest Appointment
            $guest_appt = new GuestAppointment;
            $guest_appt->guest_id = $lead->id;
            $guest_appt->staff_id = $lead->staff_id;
            $guest_appt->fa_status = 'NonFA';

            $datetime = Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->fa_date . ' 09:01')->format('Y-m-d H:i'), session('timezone'))->setTimezone('UTC');

            $guest_appt->fa_date = $datetime;
            $guest_appt->fa_hour = date('H:i', strtotime($datetime));
            $guest_appt->guest_status_after = 'Member';
            $guest_appt->club_id = $lead->club_id;

            //Guest Appointment Status
            $guest_appt_status = new GuestAppointmentStatus;
            $guest_appt_status->status_id = '5';
            $guest_appt_status->c_date = $datetime;
            $guest_appt_status->s_date = $datetime;
            $guest_appt_status->sa_date = $datetime;

            //Add Member
            $user = new User;
            $user->name = $lead->fullname;
            $user->email = date('dmYhis', strtotime(Carbon::now())) . '@curvesmember.com';
            $user->password = bcrypt('curvesindonesia');
            $user->role_id = '7';
            $user->club_id = $lead->club_id;
            $user->username = date('dnygi', strtotime(Carbon::now())) . '.cm.' . '1';

            //Update Auto Number
            $payment_number = $request->payment_number;

            //Adding Member Payment
            $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $member_payment = new MemberPayment;
            $member_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $request->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
            $member_payment->user_id = 1;
            $member_payment->sf_amount = $request->sf_amount;
            $member_payment->mf_amount = $request->mf_amount;
            $member_payment->dc_amount = $request->discount;
            $member_payment->tax_type_id = '1';
            $member_payment->tax_amount = round($request->total / 11);
            $member_payment->tax_percentage = 10;
            $member_payment->amount_due = $request->total;
            $member_payment->payment_category = 'First';

            if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
                $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
            else :
                $member_payment->payment_date = $request->fa_date;
            endif;

            $member_payment->payment_method_id = $request->payment_method;
            $member_payment->amount_paid = $request->pay;
            $member_payment->rest_of_bill = ($request->total - $request->pay) <= 0 ? 0 : $request->total - $request->pay;
            $member_payment->payment_status = $member_payment->rest_of_bill <= 0 ? 'Paid' : 'Halfpaid';
            $member_payment->bank_id = $request->payment_method != 1 ? $request->bank : 1;

            //Print
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $club = ClubInfo::findOrFail($request->club_id);
                $user = User::findOrFail($request->fa_coach);
                $member_type = MembershipType::findOrFail($request->membership_type);
                $digital_product = DigitalProduct::find($request->digital_product);

                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($club->club_name);

                $cmds .= "Terima Dari : " . $lead->fullname;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $member_type->membership_type_name;
                $cmds .= $newLine;

                if ($request->for == "club") {
                    if (!empty($digital_product)):
                        $harga_digital_product = $digital_product->default_price;
                        if ($request->pay >= $harga_digital_product) {
                            $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount - $harga_digital_product, 2);
                            $cmds .= new Item(" " . $digital_product->item_name . " : ", $harga_digital_product, 2);
                        } else {
                            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                        }
                    else :
                        $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                        $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                    endif;
                } else {
                    $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= new Item(" Total : ", $request->total, 2);

                if ($request->payment_method != 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->payment_method == 1) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
                $cmds .= $newLine;

                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "Curves Indonesia     ";
                // $cmds .= $newLine . $newLine;
                // $cmds .= '';
                // $cmds .= $newLine . $newLine;
                // $cmds .= $esc . 'a' . '2';
                // $cmds .= "(________________________)";

                // $cmds .= $newLine;
                // $cmds .= $esc . 'a' . '0';
                // $cmds .= "[ ] Update Data";

                $cmds .= $newLine . $newLine;
                $cmds .= '';
                $cmds .= $newLine . $newLine;

                //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
                $cpj = new ClientPrintJob();
                //set ESCPOS commands to print...
                $cpj->printerCommands = $cmds;
                $cpj->formatHexValues = true;

                if ($useDefaultPrinter || $printerName === 'null') {
                    $cpj->clientPrinter = new DefaultPrinter();
                } else {
                    $cpj->clientPrinter = new InstalledPrinter($printerName);
                }

                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }
        } catch (Exception $e) {
            Log::error($e);
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    public function promoMembership(Request $request)
    {
        $prename = str_replace('%20', ' ', $request->premember_name);
        $lead = BuddyReferralLead::find($request->lead);

        if (empty($lead)) :
            if ($request->check == 1) :
                //Check if lead already exist
                $check_lead = BuddyReferralLead::where('fullname', '=', $prename)
                    ->where('phone', '=', $request->phone)
                    ->get();

                if (count($check_lead) > 0) :
                    Session::flash('cf_lite.includes.flash-message', trans('messages.lead_already_exist'));
                    return redirect('cf-lite/cashier/promo');
                endif;
            endif;

            //Buddy Referral Lead
            $lead = new BuddyReferralLead;
            $lead->fullname = $prename;
            $lead->phone = $request->phone;
            $lead->source_id = $request->source;

            if ($request->source == '1') :
                $lead->is_br = 'BR';
            else :
                $lead->is_br = 'NotBR';
            endif;

            if (!empty($request->login_user)) :
                $lead->staff_id = $request->fa_coach;
            endif;

            $lead->club_id = $request->club_id;
            $lead->status_gm = 'Guest';
            if ($request->id == 1) :
                $lead->save();
            endif;
        endif;

        //Guest Appointment
        $this->insertGuestAppointmentAndStatus($lead, $request->fa_date, $request->id, 'Lead');

        //Add Guest Membership Member
        $gm_member = new GuestMembershipMember;
        $gm_member->guest_id = $lead->id;
        $gm_member->from_date = $request->fa_date;

        //Check Promo Duration to Set Thru Date
        $promo = GuestMembership::find($request->promo);
        if (!empty($promo)) :
            $gm_member->gm_id = $request->promo;
            if ($promo->duration_type == 'Day') :
                $gm_member->thru_date = Carbon::parse($request->fa_date)->addDays($promo->duration_long);
            elseif ($promo->duration_type == 'Week') :
                $gm_member->thru_date = Carbon::parse($request->fa_date)->addWeeks($promo->duration_long);
            elseif ($promo->duration_type == 'Month') :
                if ($promo->duration_long == 1) :
                    $gm_member->thru_date = Carbon::parse($request->fa_date)->addDays(30);
                else :
                    $gm_member->thru_date = Carbon::parse($request->fa_date)->addMonths($promo->duration_long);
                endif;
            elseif ($promo->duration_type == 'Year') :
                $gm_member->thru_date = Carbon::parse($request->fa_date)->addYears($promo->duration_long);
            endif;
        endif;

        if ($request->id == 1) :
            $gm_member->save();
        endif;

        //Update Auto Number
        $payment_number = $this->getAutoNumber('1', $request->id);

        //Adding Guest Payment
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $gm_payment = new GuestMemberPayment;
        $gm_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $request->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $gm_payment->guest_membership_member_id = $gm_member->id;

        $gm_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));

        $gm_payment->payment_type_id = $request->payment_method;
        $gm_payment->amount_due = $request->price;
        $gm_payment->amount_paid = ($request->pay > $request->price) ? $request->price : $request->pay;

        if (($request->total - $request->pay) > 0) :
            $gm_payment->rest_of_bill = ($request->total - $request->pay);
            $gm_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) <= 0) :
            $gm_payment->payment_status = 'Paid';
        endif;

        if ($request->payment_method != 1) :
            $gm_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1) :
            $gm_payment->bank_id = 1;
        endif;

        if ($request->id == 1) :
            $gm_payment->save();
        endif;

        //Staff Point
        if ($request->id == 1) :
            app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
            app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');
            app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);
            //app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);

            //Akunting
            app('App\Http\Controllers\TempJournalController')->promo_membership_payment($gm_payment, false);
        //app('App\Http\Controllers\AccountingJournalController')->promo_journal($gm_payment);
        endif;

        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $club = ClubInfo::findOrFail($request->club_id);
            $user = User::findOrFail($request->fa_coach);
            $promo = GuestMembership::findOrFail($request->promo);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            $cmds .= "Terima Dari : " . $prename;
            $cmds .= $newLine;
            $cmds .= "No : " . $gm_payment->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($request->fa_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);

            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= " " . $promo->gm_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Biaya : ", $request->price, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->total, 2);

            if ($request->payment_method != 1) :
                $cmds .= new Item(" " . $gm_payment->gmpayment_paymentmethod->payment_method . " " . $gm_payment->gmpayment_bank->bank_abv . " : ", $request->pay, 2);
            elseif ($request->payment_method == 1) :
                $cmds .= new Item(" " . $gm_payment->gmpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
            endif;

            $cmds .= $newLine;

            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "Curves Indonesia     ";
            // $cmds .= $newLine . $newLine;
            // $cmds .= '';
            // $cmds .= $newLine . $newLine;
            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "(________________________)";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Update Data";

            $cmds .= $newLine . $newLine;
            $cmds .= '';
            $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function promoConvertMembership(Request $request)
    {
        $prename = str_replace('%20', ' ', $request->premember_name);
        $lead = BuddyReferralLead::find($request->lead);

        //Guest Membership Member
        $guest_member = null;
        foreach ($lead->guest_gmmember as $gm_member) :
            $guest_member = GuestMembershipMember::findOrFail($gm_member->id);
            break;
        endforeach;

        //Update status brl
        $lead->status_gm = 'Member';
        if ($request->id == 1) :
            $lead->save();
        endif;

        //Guest Appointment
        $guest_apo_id = null;
        foreach ($lead->buddyreferrallead_guestappointment->where('guest_status_after', 'Lead') as $guest_apo) :
            $guest_apo_id = $guest_apo->id;
        endforeach;

        if ($guest_apo_id != null) :
            $guest_appt = GuestAppointment::find($guest_apo_id);
            $guest_appt->guest_status_after = 'Member';
            if ($request->id == 1) :
                $guest_appt->save();
            endif;

            //Guest Appointment Status
            $guest_appt_status = $guest_appt->guestappointment_gastatus;
            $guest_appt_status->status_id = '5';
            $guest_appt_status->sa_date = $request->fa_date;
            if ($request->id == 1) :
                $guest_appt->guestappointment_gastatus()->save($guest_appt_status);
            endif;
        endif;

        //Add Member
        $user = new User;
        $user->name = $lead->fullname;
        $user->email = date('dmYhis', strtotime(Carbon::now())) . '@curvesmember.com';
        $user->password = bcrypt('curvesindonesia');
        $user->role_id = '7';
        $user->club_id = $lead->club_id;
        $user->username = date('dnygi', strtotime(Carbon::now())) . '.cm.' . $lead->id;
        if ($request->id == 1) :
            $user->save();
        endif;

        //Update Auto Number
        $payment_number = $this->getAutoNumber('1', $request->id);

        //Adding Member Payment
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $member_payment = new MemberPayment;
        $member_payment->payment_number = sprintf('%08d', $payment_number) . '/PY/CIND/' . sprintf('%03d', $lead->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');
        $member_payment->user_id = $user->id;
        $member_payment->sf_amount = $request->sf_amount;
        $member_payment->mf_amount = $request->mf_amount;
        $member_payment->dc_amount = $request->discount + $request->dp_deposit;
        $member_payment->tax_type_id = '1';
        $member_payment->tax_amount = round($request->total / 11);
        $member_payment->tax_percentage = 10;
        $member_payment->amount_due = $request->total;
        $member_payment->payment_category = 'First';
        $member_payment->deposit_amount = $request->dp_deposit;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_payment->payment_date = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_payment->payment_date = $request->fa_date;
        endif;

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;

        if (($request->total - $request->pay) > 0) :
            $member_payment->rest_of_bill = ($request->total - $request->pay);
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) == 0) :
            $member_payment->payment_status = 'Paid';
        endif;

        if ($request->payment_method != 1) :
            $member_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1) :
            $member_payment->bank_id = 1;
        endif;

        // Conversion Status Added 20022020
        $member_payment->conversion = true;

        if ($request->id == 1) :
            $member_payment->save();
        endif;

        //Adding MemberStatus (member_status table)
        $membership_type = MembershipType::findOrFail($request->membership_type);

        $member_status = new MemberStatus;
        $member_status->status = 'Active';
        $member_status->membership_type_id = $request->membership_type;

        if ($membership_type->is_vip == 'general') :
            $the_duration = $membership_type->duration;
        elseif ($membership_type->is_vip == 'vip') :
            $the_duration = 0;
        endif;

        $member_status->from_date = $guest_member->from_date;
        $thru_date_temp = date('Y-m-d', strtotime("+" . $the_duration . " months", strtotime($guest_member->from_date)));
        $member_status->thru_date = $thru_date_temp;

        if ($membership_type->payment_type == 'Prepaid') :
            if (($request->total - $request->pay) == 0) :
                $member_status->payment_status = 'Set';
            endif;
        elseif ($membership_type->payment_type == 'Cash') :
            $member_status->payment_status = 'Set';
        endif;

        if ($request->id == 1) :
            $user->user_memberstatus()->save($member_status);
        endif;

        //Adding LogMemberStatus (Log for Member's Membership History)
        $log_member = new LogMemberStatus;
        $log_member->user_id = $user->id;
        $log_member->membership_type_id = $membership_type->id;
        $log_member->from_date = $member_status->from_date;
        $log_member->thru_date = $member_status->thru_date;
        $log_member->club_id = $request->club_id;
        if ($request->id == 1) :
            $log_member->save();
        endif;

        //Adding MemberPersonal (member_personals table)
        $member_personal = new MemberPersonal;
        $member_personal->phone = $request->phone;
        $member_personal->key_tag_id = '9999' . Keygen::numeric(5)->generate();

        if (!empty($request->fa_coach)) :
            $member_personal->sales_person_id = $request->fa_coach;
        //        else:
        //            if (!empty(session('staff_session'))):
        //                $member_personal->sales_person_id = session('staff_session');
        //            else:
        //                $member_personal->sales_person_id = Auth::id();
        //            endif;
        endif;

        $member_personal->guest_id = $lead->id;

        if (strtotime($request->fa_date) == strtotime(Carbon::today())) :
            $member_personal->joined = Carbon::createFromFormat('Y-m-d H:i', Carbon::now()->format('Y-m-d H:i'), session('timezone'));
        else :
            $member_personal->joined = $request->fa_date;
        endif;

        $member_personal->created_by = $request->login_user;
        if ($request->id == 1) :
            $user->user_memberpersonal()->save($member_personal);
        endif;

        //Adding Member to Coach based on Sequence
        $club = ClubInfo::findOrFail($request->club_id);
        $coach_sequences = CoachSequence::where('club_id', '=', $club->id)
            ->orderBy('sequence', 'desc')
            ->get();

        $max_seq = $coach_sequences[0]->sequence;
        foreach ($coach_sequences->where('sequence', $club->current_sequence) as $coach_seq) :
            //Insert to coach member table
            $coach_member = new CoachMember;
            $coach_member->coach_id = $coach_seq->coach_id;
            if ($request->id == 1) :
                $user->memberuser_coachmember()->save($coach_member);
            endif;

            //Update club current sequence
            if ($club->current_sequence < $max_seq) :
                $club->current_sequence = $club->current_sequence + 1;
            elseif ($club->current_sequence == $max_seq) :
                $club->current_sequence = 1;
            endif;
            if ($request->id == 1) :
                $club->save();
            endif;
        endforeach;

        //Staff Point
        if ($request->id == 1) :
            app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);

            // Amortization
            app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, Carbon::parse($user->user_memberstatus->from_date));

            //Akunting
            app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
            app('App\Http\Controllers\AccountingJournalController')->membership_journal($member_payment, $membership_type);
        endif;

        //If Has WM
        if (!empty($lead->guest_guestwm)) :
            foreach ($lead->guest_guestwm as $guest_wm) :
                $wm = new WeighMeasure;
                $wm->user_id = $user->id;
                $wm->wm_date = $guest_wm->wm_date;
                $wm->age = $guest_wm->age;
                $wm->height = $guest_wm->height;
                $wm->bp_high = $guest_wm->bp_high;
                $wm->bp_low = $guest_wm->bp_low;
                $wm->rhr = $guest_wm->rhr;
                $wm->chest = $guest_wm->chest;
                $wm->waist = $guest_wm->waist;
                $wm->abdomen = $guest_wm->abdomen;
                $wm->hip = $guest_wm->hip;
                $wm->thigh = $guest_wm->thigh;
                $wm->arm = $guest_wm->arm;
                $wm->weight = $guest_wm->weight;
                $wm->body_fat = $guest_wm->body_fat;
                $wm->muscle_mass = $guest_wm->muscle_mass;
                $wm->bone_mass = $guest_wm->bone_mass;
                $wm->bmi = $guest_wm->bmi;
                $wm->dci = $guest_wm->dci;
                $wm->metabolic = $guest_wm->metabolic;
                $wm->body_water = $guest_wm->body_water;
                $wm->visceral = $guest_wm->visceral;
                $wm->three_times_a_week = $guest_wm->three_times_a_week;
                $wm->total_wo_per_month = $guest_wm->total_wo_per_month;
                $wm->total_measurement = $guest_wm->total_measurement;
                $wm->total_body_fat = $guest_wm->total_body_fat;
                $wm->total_hydration = $guest_wm->total_hydration;
                $wm->measured_by = $guest_wm->measured_by;
                $wm->pro_workout = $guest_wm->pro_workout;
                $wm->recommendation = $guest_wm->recommendation;
                $wm->save();
            endforeach;
        endif;

        //If Has WO
        if (!empty($lead->guest_guestwo)) :
            foreach ($lead->guest_guestwo as $guest_wo) :
                $wo = new Workout;
                $wo->user_id = $user->id;
                $wo->workout_date = $guest_wo->wo_date;
                $wo->club_id = $guest_wo->club_id;
                $wo->save();
            endforeach;
        endif;

        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $club = ClubInfo::findOrFail($request->club_id);
            $user = User::findOrFail($request->fa_coach);
            $member_type = MembershipType::findOrFail($request->membership_type);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            $cmds .= "Terima Dari : " . $prename;
            $cmds .= $newLine;
            $cmds .= "No : " . $member_payment->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= "Konversi Keanggotaan " . $member_type->membership_type_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
            $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);
            $cmds .= new Item(" Simpanan : (-) ", $request->dp_deposit, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->total, 2);

            if ($request->payment_method != 1) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
            elseif ($request->payment_method == 1) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
            endif;

            $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
            $cmds .= $newLine;

            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "Curves Indonesia     ";
            // $cmds .= $newLine . $newLine;
            // $cmds .= '';
            // $cmds .= $newLine . $newLine;
            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "(________________________)";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Input System";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Update Data";

            $cmds .= $newLine . $newLine;
            $cmds .= '';
            $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function firstPaymentInstallment(Request $request)
    {
        $member_payment = MemberPayment::findOrFail($request->payment);

        //Update Auto Number
        $temp_auto_number = AutoNumber::findOrFail('1');

        if ($request->id == 1) :
            $payment_number = $temp_auto_number->current_number;
            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();
        else :
            //Cek Member Debt Payment
            $check_mdp = MemberDebtPayment::where('member_payment_id', '=', $member_payment->id)
                ->orderBy('payment_date', 'desc')
                ->first();

            if (!empty($check_mdp)) :
                $payment_number = $check_mdp->payment_number;
            else :
                $payment_number = $member_payment->payment_number;
            endif;
        endif;

        $member_payment->amount_paid = $member_payment->amount_paid + $request->total_pay;

        if (($request->total_bill - $request->total_pay) == $request->rest_of_bill) :
            $member_payment->rest_of_bill = $request->rest_of_bill;
        else :
            $member_payment->rest_of_bill = $request->total_bill - $request->total_pay;
        endif;

        if (($request->total_bill - $request->total_pay) == '0') :
            $member_payment->payment_status = 'Paid';
        endif;

        if ($request->id == 1) :
            $member_payment->save();
        endif;

        //Member Debt Payment
        if ($request->id == 1) :
            $member_debt_payment = new MemberDebtPayment;
            $member_debt_payment->member_payment_id = $member_payment->id;
            $member_debt_payment->payment_number = sprintf('%08d', $payment_number) . $request->payment_number;
            $member_debt_payment->amount_due = $request->total_bill;
            $member_debt_payment->amount_paid = $request->total_pay;

            if (($request->total_bill - $request->total_pay) == $request->rest_of_bill) :
                $member_debt_payment->rest_of_bill = $request->rest_of_bill;
            else :
                $member_debt_payment->rest_of_bill = $request->total_bill - $request->total_pay;
            endif;

            if (!empty($request->payment_date)) :
                $member_debt_payment->payment_date = $request->payment_date;
            else :
                $member_debt_payment->payment_date = Carbon::now();
            endif;
            $member_debt_payment->payment_method_id = $request->payment_method;

            //Added 20032019
            if ($request->payment_method == 1) :
                $member_debt_payment->bank_id = 1;
            else :
                $member_debt_payment->bank_id = $request->bank;
            endif;

            $member_debt_payment->save();
        endif;

        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $user = User::findOrFail($request->login_user);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($member_payment->memberpayment_user->user_club->club_name);

            $cmds .= "Terima Dari : " . $member_payment->memberpayment_user->name;
            $cmds .= $newLine;
            $cmds .= "No : " . sprintf('%08d', $payment_number) . $request->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= "Cicilan " . $member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->membership_type_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Tagihan : ", $request->total_bill, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->total_bill, 2);

            $payment_method = PaymentMethod::findOrFail($request->payment_method);
            if ($request->payment_method != 1) :
                $bank = Bank::findOrFail($request->bank);
                $cmds .= new Item(" " . $payment_method->payment_method . " " . $bank->bank_abv . " : ", $request->total_pay, 2);
            elseif ($request->payment_method == 1) :
                $cmds .= new Item(" " . $payment_method->payment_method . " : ", $request->total_pay, 2);
            endif;

            $cmds .= new Item(" Sisa Hutang Member : ", $request->total_bill - $request->total_pay, 2);
            $cmds .= $newLine;

            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "Curves Indonesia     ";
            // $cmds .= $newLine . $newLine;
            // $cmds .= '';
            // $cmds .= $newLine . $newLine;
            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "(________________________)";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Input System";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Update Data";

            $cmds .= $newLine . $newLine;
            $cmds .= '';
            $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function guestMembershipPayment(Request $request)
    {
        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $club = ClubInfo::findOrFail($request->club_id);
            $user = User::findOrFail($request->staff);
            $gmp = GuestMemberPayment::findOrFail($request->id);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            $cmds .= "Terima Dari : " . $gmp->gmpayment_gmmember->gmmember_guest->fullname;
            $cmds .= $newLine;
            $cmds .= "No : " . $gmp->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($gmp->payment_date)->format('d-m-Y'), substr($user->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= " " . $gmp->gmpayment_gmmember->gmmember_gm->gm_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Biaya : ", $gmp->gmpayment_gmmember->gmmember_gm->price, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $gmp->amount_due, 2);

            if ($gmp->payment_type_id != 1) :
                $cmds .= new Item(" " . $gmp->gmpayment_paymentmethod->payment_method . " " . $gmp->gmpayment_bank->bank_abv . " : ", $gmp->amount_paid, 2);
            elseif ($gmp->payment_type_id == 1) :
                $cmds .= new Item(" " . $gmp->gmpayment_paymentmethod->payment_method . " : ", $gmp->amount_paid, 2);
            endif;

            $cmds .= $newLine;

            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "Curves Indonesia     ";
            // $cmds .= $newLine . $newLine;
            // $cmds .= '';
            // $cmds .= $newLine . $newLine;
            // $cmds .= $esc . 'a' . '2';
            // $cmds .= "(________________________)";

            // $cmds .= $newLine;
            // $cmds .= $esc . 'a' . '0';
            // $cmds .= "[ ] Update Data";

            $cmds .= $newLine . $newLine;
            $cmds .= '';
            $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function reprintProduct(Request $request)
    {
        //Print PEMBELIAN PRODUK + PPN 11%
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $product = ProductPayment::findOrFail($request->id);

            $user = User::find($product->cashier_id);
            $pembeli = User::find($product->member_id);
			
			  // Check if club is a main
            $club = ClubInfo::find($user->user_club->jubilant_id);
        
			
			
            $product_detail = ProductPaymentDetail::where('product_payment_id', '=', $product->id)->get();

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($user->user_club->club_name);

            if (!empty($pembeli)) :
                $cmds .= "Terima Dari : " . $pembeli->name;
            else :
                $cmds .= "Terima Dari : Guest";
            endif;
            $cmds .= $newLine;

            $cmds .= "No : " . $product->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($product->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            foreach ($product_detail as $il) {
                $cmds .= " " . $il->productpaymentdetail_product->item_name;
                $cmds .= $newLine;
                $cmds .= new Item(" " . $il->qty . ' X ' . number_format((int)$il->price, 0, '', '.'), $il->qty * $il->price, 2);
            }
            $cmds .= $newLine;
            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            $cmds .= new Item(" Diskon : ", $product->invoice_discount, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;
            $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
            $cmds .= new Item(" Total : ", $product->total - $product->invoice_discount, 2);

            if ($product->payment_method_id != 1) :
                $cmds .= new Item(" " . $product->productpayment_paymentmethod->payment_method . " " . $product->productpayment_bank->bank_abv . " : ", $product->pay, 2);
            elseif ($product->payment_method_id == 1) :
                $cmds .= new Item(" " . $product->productpayment_paymentmethod->payment_method . " : ", $request->pay, 2);
            endif;
	
			if ($club->club_jubilant->type == 'Main'){
           		$cmds .= $newLine;
				$cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
                $cmds .= 'Harga Sudah Termasuk PPN 11%';
				$cmds .= $newLine;
				$cmds .= 'PT. Unity Brand Indonesia';
				$cmds .= $newLine;
				$cmds .= 'Jl. Surya Sumantri No. 76 Bandung';
				$cmds .= $newLine;
				$cmds .= 'NPWP: 84.387.563.4-428.000';
	            $cmds .= $newLine;
			}
				$cmds .= $newLine;
				$cmds .= $esc . '!' . '0x00'; 
                $cmds .= "Terima Kasih";
                $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                ->header('Content-Type', 'application/octet-stream');
        }
    }

    public function reprintLocker(Request $request){
        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $locker_payment = LockerPayment::findOrFail($request->id);
            $memberlocker = MemberLocker::findOrFail($locker_payment->memberlocker_id);
            $choosenlocker = Locker::findOrFail($locker_payment->locker_id);

            $club = ClubInfo::findOrFail($memberlocker->memberlocker_lockerinfo->club_id);
            $user = User::findOrFail($locker_payment->staff_id);

            $esc = '0x1B'; //ESC byte in hex notation
            $newLine = '0x0A'; //LF byte in hex notation

            $cmds = $this->setUpPrinter();
            $cmds .= $this->setPrintLogo($club->club_name);

            $cmds .= "Terima Dari : " . $memberlocker->memberlocker_user->name;
            $cmds .= $newLine;
            $cmds .= "No : " . $locker_payment->payment_number;
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '1';
            $cmds .= new Item('Tgl: ' . Carbon::parse($locker_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= $esc . 'a' . '0';
            $cmds .= " " . $memberlocker->memberlocker_lockerinfo->locker_name . " - " . $choosenlocker->name;
            $cmds .= $newLine;

            $cmds .= new Item(" Mulai Pinjaman : ", Carbon::parse($request->start_date)->format('d-m-Y'), 1);
            $cmds .= new Item(" Selesai Pinjaman : ", Carbon::parse($memberlocker->thru_date)->format('d-m-Y'), 1);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->billingtotal, 2);

            if ($request->payment_method != 1) :
                $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " " . $locker_payment->lockerpayment_bank->bank_abv . " : ", $locker_payment->pay, 2);
            elseif ($request->payment_method == 1) :
                $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " : ", $locker_payment->pay, 2);
            endif;

	

				$cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
                $cmds .= $newLine . $newLine;

            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            //set ESCPOS commands to print...
            $cpj->printerCommands = $cmds;
            $cpj->formatHexValues = true;

            if ($useDefaultPrinter || $printerName === 'null') {
                $cpj->clientPrinter = new DefaultPrinter();
            } else {
                $cpj->clientPrinter = new InstalledPrinter($printerName);
            }

            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
        }
    }

    private function getAutoNumber($autonumber_id, $no_reprint)
    {
        $temp_auto_number = AutoNumber::findOrFail($autonumber_id);

        if ($no_reprint == 1) :
            $payment_number = $temp_auto_number->current_number;

            $temp_auto_number->current_number = $temp_auto_number->current_number + 1;
            $temp_auto_number->save();
        else :
            $payment_number = $temp_auto_number->current_number - 1;
        endif;

        return $payment_number;
    }

    private function setUpPrinter()
    {
        $esc = '0x1B'; //ESC byte in hex notation
        $cmds = $esc . "@"; //Initializes the printer (ESC @)

        return $cmds;
    }

    private function setPrintLogo($club_name)
    {
        $esc = '0x1B'; //ESC byte in hex notation
        $newLine = '0x0A'; //LF byte in hex notation

        $highDensityHorizontal = true;
        $highDensityVertical = true;

        $im = new \Imagick();
        $im->setResourceLimit(6, 1); // Prevent libgomp1 segfaults, grumble grumble.
        $img = public_path('images/curveslogo.png');
        $im->readImage($img);

        $GS = "\x1d";
        $widthBytes = (int)(($im->getimagewidth() + 7) / 8);
        $heightPixels = $im->getimageheight();
        $densityByte = ($highDensityHorizontal ? 0 : 1) + ($highDensityVertical ? 0 : 2);
        $header = $GS . "v0" . chr($densityByte) . intLowHigh($widthBytes, 2) . intLowHigh($heightPixels, 2);
        $cmds = $esc . 'a' . '1';
        $cmds .= $header . toRasterFormat($im);
        $cmds .= $newLine;

        $cmds .= $esc . 'a' . '1';
        $cmds .= $esc . '!' . '0x01';
        $cmds .= $club_name;
        $cmds .= $newLine . $newLine;

        $cmds .= $esc . 'a' . '0';
        $cmds .= $esc . '!' . '0x01';

        return $cmds;
    }
}

function toRasterFormat($im)
{
    $im->setFormat('pbm');
    $blob = $im->getImageBlob();
    $i = strpos($blob, "\n", 3); // Find where header ends
    return substr($blob, $i + 1);
}

function intLowHigh($input, $length)
{
    $outp = "";
    for ($i = 0; $i < $length; $i++) {
        $outp .= chr($input % 256);
        $input = (int)($input / 256);
    }
    return $outp;
}

class item
{
    private $name;
    private $price;
    private $istotal;

    public function __construct($name = '', $price = '', $istotal)
    {
        $this->name = $name;
        $this->price = $price;
        $this->istotal = $istotal;
    }

    public function __toString()
    {
        $rightCols = 24;
        $leftCols = 24;

        if ($this->istotal == 1) {
            $right = str_pad($this->price, $rightCols, ' ', STR_PAD_LEFT);
        } else if ($this->istotal == 2) {
            $right = str_pad("Rp. " . number_format((int)$this->price, 0, '', '.'), $rightCols, ' ', STR_PAD_LEFT);
        }

        $left = str_pad($this->name, $leftCols);
        return "$left$right\n";
    }
}
