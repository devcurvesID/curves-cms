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
use App\Model\Payment\PaymentReceipts;
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

//ACCURATE SCRIPTS
use App\AccurateAuth;
use App\Helpers\Accurate;
use stdClass;
use Storage;
use Str;

//PDF Receipt
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Broadcasting\Channel;
use App\Notifications\SendReceipt;


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
//require_once app_path() .'/WebClientPrint/WebClientPrint.php';  //local

use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\DefaultPrinter;
use Neodynamic\SDK\Web\InstalledPrinter;
use Neodynamic\SDK\Web\ClientPrintJob;
use Throwable;

//PDF
use Barryvdh\DomPDF\Facade\Pdf;

class PrintESCPOSController extends Controller
{
    public function cashierProduct(Request $request)
    {
        //dd($request);

        // Normalize Item List
        $item_list = $this->normalizeItemList($request);
        DB::beginTransaction();

        try {
            $pdfData = [];

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
            $product_payment->invoice_discount = $request->invoice_discount;

            //$product_payment->bank_id = ($request->paymenttype == 1 || $request->paymenttype == 6 || $request->paymenttype == 7) ? 1 : $request->bank;

            if ($request->paymenttype != 1 || $request->paymenttype != 6  || $request->paymenttype != 7) :
                $product_payment->bank_id = $request->bank;
            elseif ($request->paymenttype == 1 || $request->paymenttype == 6  || $request->paymenttype == 7) :
                $product_payment->bank_id = 1;
            endif;

            if(is_null($product_payment->bank_id)){
                $product_payment->bank_id = 1;
            }

            // Check if club is a franchise
            $club = ClubInfo::findOrFail($request->clubid);
            if ($club->club_jubilant->type == 'Franchise') :
                $product_payment->is_billed = false;
            endif;

            if ($request->idprint == 1) :
                //dd($request);
                $product_payment->save();
                //Accounting

                

                app('App\Http\Controllers\TempJournalController')->product_payment($product_payment, false);

                //ACCURATE
                /* $buyer = User::find($request->buyer);
                $userData = [
                    'name' => str_replace("%20", " ", $buyer->name),
                    //'membership_type' => $request->membership_type,
                    'fa_date' => $request->payment_date,
                    'pay' => $request->pay,
                    'item_list' => $item_list
                ];

                app('App\Http\Controllers\ProductController')->store_accurate($request->clubid, $userData); */

            endif;

            // Add to product payment detail
            $this->insertProductPaymentDetail($item_list,$request->idprint,$product_payment->id,$request->status);

            DB::commit();


            // Sync only newly saved member payments after all database writes succeed.
            if ($request->idprint == 1 && !empty($product_payment->member_id)) {
                try {
                    $product_payment->refresh();
                    app(ApiClientService::class)
                        ->post("/mobile-curves/payment-product/{$product_payment->member_id}", $product_payment->toArray())
                        ->throw();
                } catch (Throwable $e) {
                    // The payment is already committed; keep receipt generation available.
                    Log::error('Failed to sync product payment to mobile Curves API.', [
                        'product_payment_id' => $product_payment->id,
                        'member_id' => $product_payment->member_id,
                        'exception' => $e,
                    ]);
                }
            }

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
                $cmds .= $this->setPrintLogo($user->user_club->club_name); //disable in local

                $buyerName = '';
                if (!empty($pembeli)) :
                    $cmds .= "Terima Dari : " . $pembeli->name;
                    $buyerName = $pembeli->name;
                else :
                    $cmds .= "Terima Dari : Guest";
                    $buyerName = 'Guest';
                endif;
                $cmds .= $newLine;

                $cmds .= "No : " . $product_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($product_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';


                $pdfData['Terima Dari'] = $buyerName;
                $pdfData['No'] = $product_payment->payment_number;
                $Tgl = Carbon::parse($product_payment->payment_date)->format('d-m-Y H:i').' '. substr($user->name, '0', '24');
                $pdfData['Tgl'] = $Tgl;
                $pdfData['spacer1'] = '-';

                $pdfData['item_list'] = [];

                foreach ($item_list as $il) {
                    $cmds .= " " . $il->name;
                    $cmds .= $newLine;
                    $cmds .= new Item(" " . $il->qty . ' X ' . number_format((int)$il->price, 0, '', '.'), $il->qty * $il->price, 2);

                    $price = number_format((int)$il->price, 0, '', '.');
                    $pdfData['item_list'][] = ['item'=> $il->name, 'price' => $price, 'qty' => $il->qty];
                }
                $cmds .= $newLine;
                $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
                $cmds .= new Item(" Diskon : ", $request->invoice_discount, 2);

                $Diskon = number_format($request->invoice_discount, 0, '', '.');
                $pdfData['Diskon'] = $Diskon;

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;
                $cmds .= $esc . '!' . '0x00'; //Character font A selected (ESC ! 0)
                $cmds .= new Item(" Total : ", $request->subtotal - $request->invoice_discount, 2);

                $Total = $request->subtotal - $request->invoice_discount;
                $pdfData['Total'] = number_format($Total, 0, '', '.');

                if ($request->paymenttype != 1 || $request->paymenttype != 6 || $request->paymenttype != 7) :
                    $cmds .= new Item(" " . $product_payment->productpayment_paymentmethod->payment_method . " " . $product_payment->productpayment_bank->bank_abv . " : ", $request->pay, 2);

                    $payment_method_not_1 = $product_payment->productpayment_paymentmethod->payment_method . " " . $product_payment->productpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                    $pdfData['payment_method_not_1'] = $payment_method_not_1;

                elseif ($request->paymenttype == 1 || $request->paymenttype == 6 || $request->paymenttype == 7) :
                    $cmds .= new Item(" " . $product_payment->productpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                    $payment_method_1 = $product_payment->productpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                    $pdfData['payment_method_1'] = $payment_method_1;
                endif;

                $cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
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

                //CHECK IF PRINT BUTTON CLICKED
                if(isset($request->axn) && $request->axn === 'print'){
                    //Send ClientPrintJob back to the client
                    return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
                }else{
                    //PDF Generate
                    //dd($pdfData);
                    return $this->generate_pdf($pdfData, $user->user_club->club_name, 'product');
                }
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

    public function cashierMembership_ORIGINAL(Request $request){
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

                //ACCURATE
                /* $userData = [
                    'name' => str_replace("%20", " ", $request->premember_name),
                    'membership_type' => $request->membership_type,
                    'fa_date' => $request->fa_date,
                    'pay' => $request->pay,
                ];

                app('App\Http\Controllers\PaymentController')->store_accurate($request->club_id, $userData); */

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
                $cmds .= $this->setPrintLogo($club->club_name); //disable in local

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
                    //$cmds .= new Item(" Total : ", $request->total /*+ $harga_digital_product*/, 2);
                    $cmds .= new Item(" Total : ", $request->total , 2);
                } else {
                    $cmds .= new Item(" Total : ", $request->total, 2);
                }

                if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);
                elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
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

    private function insertUser($lead, $club_id, $no_reprint = 0, $isEzypay = false){
        $user = new User;
        $user->name = $lead->fullname;
        $user->email = date('dmYhis', strtotime(Carbon::now())) . '@curvesmember.com';
        if ($isEzypay) {
            $user->email = $lead->email;
            $user->customer_id_ezypay = $lead->customer_id_ezypay;
        }
        $user->password = bcrypt('curvesindonesia');
        $user->role_id = '7';
        $user->club_id = $club_id;
        $user->username = date('dnygi', strtotime(Carbon::now())) . '.cm.' . $lead->id;
        if ($no_reprint == 1) :
            $user->save();
        endif;

        return $user;
    }

    private function insertMemberPayment($payment_number, $request, $user_id, $no_reprint, $isEzypay = false){
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
        if ($isEzypay) {
            $requestedPayment = $request->attributes->get('ezypay_amount_value');
            $remaining = round((float) $request->total - $requestedPayment, 2);
            $member_payment->payment_status = $remaining <= 0 ? 'Unpaid' : 'Halfunpaid';
        }
        /* if( $request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7){
            $member_payment->bank_id = 1;
        }else{
            $member_payment->bank_id = $request->bank;
        } */

        if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
            $member_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
            $member_payment->bank_id = 1;
        endif;

        if(is_null($member_payment->bank_id)){
            $member_payment->bank_id = 1;
        }
        

        //$member_payment->bank_id = ($request->payment_method != 1 || $request->payment_method != 6 || $request->payment_method != 7) ? $request->bank : 1;
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

    private function insertMemberPersonal($request, $lead_id, $user, $ezypayLead = null){
        $member_personal = new MemberPersonal;
        $member_personal->phone = $ezypayLead ? $ezypayLead->phone : $request->phone;
        if ($ezypayLead) {
            $member_personal->cellphone = $ezypayLead->wa_number;
        }
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
            $pdfData = [];

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
            //dd($memberlocker);

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
            //$locker_payment->bank_id = $request->payment_method == 1 ? 1 : $request->bank;

            //$locker_payment->bank_id = ($request->payment_method == 1 || $request->payment_method == 6 || $request->payment_method == 7) ? 1 : $request->bank;

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $locker_payment->bank_id = $request->bank;
            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $locker_payment->bank_id = 1;
            endif;

            if(is_null($locker_payment->bank_id)){
                $locker_payment->bank_id = 1;
            }

            //dd($locker_payment);
            if ($request->id == 1) :
                $locker_payment->save();
                //
                //ACCURATE
                /* $buyer = User::find($request->member);
                $userData = [
                    'name' => str_replace("%20", " ", $buyer->name),
                    'locker_type' => 'LOKER',
                    'locker_id' => $request->locker,
                    'description' => $choosenlocker->name .' | '. $request->start_date .' - '. Carbon::parse($memberlocker->thru_date)->format('Y-m-d'),
                    'payment_date' => $request->payment_date,
                    'price' => $choosenlocker->price,
                    'qty' => $request->amount,
                    'pay' => $request->pay,
                ];

                app('App\Http\Controllers\LockerController')->store_accurate($request->club_id, $userData); */

                app('App\Http\Controllers\TempJournalController')->locker_payment($locker_payment, false);
                //            app('App\Http\Controllers\AccountingJournalController')->locker_payment_journal($locker_payment);

                // Added 25102019
                app('App\Http\Controllers\PointStaffController')->locker_point($request->staff, $locker_payment->id);


            endif;

            DB::commit();

            // Sync the newly created locker only after all database writes succeed.
            if ($request->id == 1 && $locker_payment->exists) {
                try {
                    $locker_payment->refresh();
                    $locker_payment->member_source_id = $memberlocker->user_id;
                    app(ApiClientService::class)
                        ->post("/mobile-curves/locker-payment/{$locker_payment->id}", $locker_payment->toArray())
                        ->throw();
                } catch (Throwable $e) {
                    // The locker is already committed; keep receipt generation available.
                    Log::error('Failed to sync locker payment to mobile Curves API.', [
                        'member_locker_id' => $locker_payment->id,
                        'exception' => $e,
                    ]);
                }
            }

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

                $pdfData['Terima Dari'] = $memberlocker->memberlocker_user->name;
                $pdfData['No'] =  $locker_payment->payment_number;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($locker_payment->payment_date)->format('d-m-Y H:i'), substr($user->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $Tgl = Carbon::parse($locker_payment->payment_date)->format('d-m-Y H:i').' '. substr($user->name, '0', '24');
                $pdfData['Tgl'] = $Tgl;
                $pdfData['spacer1'] = '-';

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $memberlocker->memberlocker_lockerinfo->locker_name . " - " . $choosenlocker->name;
                $cmds .= $newLine;

                $pdfData['Chosen Locker'] = $memberlocker->memberlocker_lockerinfo->locker_name . " - " . $choosenlocker->name;

                $cmds .= new Item(" Mulai Pinjaman : ", Carbon::parse($request->start_date)->format('d-m-Y'), 1);
                $cmds .= new Item(" Selesai Pinjaman : ", Carbon::parse($memberlocker->thru_date)->format('d-m-Y'), 1);

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;


                $pdfData['spacer2'] = '-';

                $MulaiPinjaman = Carbon::parse($request->start_date)->format('d-m-Y');
                $pdfData['Mulai Pinjaman'] = $MulaiPinjaman;

                $SelesaiPinjaman = Carbon::parse($memberlocker->thru_date)->format('d-m-Y');
                $pdfData['Selesai Pinjaman'] = $SelesaiPinjaman;

                $cmds .= new Item(" Total : ", $request->billingtotal, 2);

                $pdfData['Total'] = number_format( $request->billingtotal, 0, '', '.');

                if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                    $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " " . $locker_payment->lockerpayment_bank->bank_abv . " : ", $request->pay, 2);

                    $payment_method_not_1 = $locker_payment->lockerpayment_paymentmethod->payment_method . " " . $locker_payment->lockerpayment_paymentmethod->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                    $pdfData['payment_method_not_1'] = $payment_method_not_1;

                elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                    $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                    $payment_method_1 = $locker_payment->lockerpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                    $pdfData['payment_method_1'] = $payment_method_1;
                endif;

                $cmds .= $newLine;
                $cmds .= $esc . 'a' . '1';
                $cmds .= $esc . '!' . '0x35';
                $cmds .= "Terima Kasih";
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

                //CHECK IF PRINT BUTTON CLICKED
                if(isset($request->axn) && $request->axn === 'print'){
                    //Send ClientPrintJob back to the client
                    return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
                }else{
                    //PDF Generate
                    //dd($pdfData);
                    return $this->generate_pdf($pdfData, $user->user_club->club_name, 'locker');
                }
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

    public function cashierRenewalWithEzypay(Request $request)
    {
        abort_unless((int) $request->club_id === (int) Auth::user()->club_id && (int) $request->id === 1, 403);
        $data = $request->validate([
            'selectedmember' => 'required|integer',
            'membership_type' => 'required|integer',
            'payment_method' => 'required|integer',
            'renewal_key' => 'required|uuid',
            'pay' => 'required|numeric|gt:0',
            'total' => 'required|numeric|gte:pay',
            'sf_amount' => 'required|numeric|min:0',
            'mf_amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'fa_date' => 'required|date',
            'fa_coach' => 'required|integer',
            'self_payment' => 'required|boolean',
            'subscription' => 'required|boolean',
            'paymentMethodToken' => 'nullable|string|max:255',
        ]);
        $user = User::where('club_id', Auth::user()->club_id)->where('role_id', 7)->findOrFail($data['selectedmember']);
        abort_unless(session('ezypay_renewal_keys.'.$user->id) === $data['renewal_key'], 422);
        $type = MembershipType::where('status', 'Active')->findOrFail($data['membership_type']);
        $method = PaymentMethod::findOrFail($data['payment_method']);
        abort_unless(strcasecmp(trim($method->payment_method), 'EZYPAY') === 0, 422);
        abort_unless(User::where('club_id', $user->club_id)->whereKey($data['fa_coach'])->exists(), 422);
        $fee = \App\Model\Club\ClubServiceFee::where('club_id', $user->club_id)
            ->where('membership_type_id', $type->id)->whereDate('from_date', '<=', Carbon::today())
            ->whereNull('thru_date')->where('sf_amount', $data['sf_amount'])->firstOrFail();
        abort_unless(round((float) $fee->mf_amount * (int) $type->duration, 2) === round((float) $data['mf_amount'], 2), 422);
        $expected = round((float) $fee->sf_amount + (float) $data['mf_amount'] - (float) $data['discount'], 2);
        abort_unless($expected === round((float) $data['total'], 2) && $user->customer_id_ezypay && $type->plan_id_ezypay, 422);
        $token = $data['paymentMethodToken'] ?? null;
        $selfPayment = $request->boolean('self_payment') || empty($token);
        $service = app(\App\Services\EzypayMembershipSubscriptionService::class)->forRenewal($data['renewal_key']);
        $reservedAt = null;
        $level = DB::transactionLevel();
        try {
            if ($token) {
                $methods = app(ApiClientService::class)->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($user->customer_id_ezypay))->throw()->json('response.data');
                abort_unless(is_array($methods) && collect($methods)->contains(fn ($item) => ($item['paymentMethodToken'] ?? null) === $token && ($item['valid'] ?? false) === true), 422);
            }
            $reservedAt = DB::transaction(function () use ($user, $service, $request, $type, $selfPayment) {
                User::lockForUpdate()->findOrFail($user->id);
                $pending = DB::table('ezypay_membership_purchases')
                    ->where('renewal_user_id', $user->id)->where('renewal_key', '<>', $request->renewal_key)
                    ->whereNull('member_payment_id')->exists();
                $unpaid = MemberPayment::where('user_id', $user->id)->where('renewal', true)
                    ->whereIn('payment_status', ['Unpaid', 'Halfunpaid'])
                    ->where(function ($q) {
                        $q->whereNotNull('ezypay_checkout_id')->orWhereNotNull('ezypay_invoice_id')->orWhereNotNull('ezypay_subscription_id');
                    })->exists();
                return $pending || $unpaid ? null : $service->reserve($user, $type, $request->boolean('subscription'), $selfPayment);
            });
            if ($reservedAt === null) {
                return response()->json(['message' => 'Pengajuan renewal EZYPAY sudah diproses atau sedang menunggu konfirmasi. Periksa status sebelum mengirim ulang.'], 409);
            }
            $request->attributes->set('ezypay_renewal_service', $service);
            $request->attributes->set('ezypay_reserved_at', $reservedAt);
            $request->attributes->set('ezypay_amount_value', (float) $data['pay']);
            $request->attributes->set('ezypay_self_payment', $selfPayment);
            $response = $this->cashierRenewal($request);
            $receipt = is_string($response) ? json_decode($response, true) : null;
            return response()->json(array_merge(is_array($receipt) ? $receipt : [], [
                'checkoutUrl' => $request->attributes->get('ezypay_checkout_url'),
                'installment_url' => $request->attributes->get('ezypay_installment_url'),
            ]));
        } catch (Throwable $e) {
            if (DB::transactionLevel() > $level) DB::rollBack();
            $committed = $request->attributes->get('ezypay_renewal_committed', false);
            $retry = !$committed && ($reservedAt === null || $service->recordRolledBackAttempt(
                $user->id, $reservedAt, $request->attributes->get('ezypay_submission_reference'),
                $request->attributes->get('ezypay_submission_id')
            ));
            $this->membershipPaymentLogger()->error('EZYPAY renewal payment failed.', [
                'user_id' => $user->id, 'file' => $e->getFile(), 'line' => $e->getLine(),
                'stage' => $request->attributes->get('ezypay_renewal_stage'),
                'exception_type' => get_class($e), 'committed' => $committed,
            ]);
            return response()->json([
                'message' => $committed ? 'Renewal sudah tersimpan. Periksa tautan pembayaran atau pengaturan cicilan.'
                    : ($retry ? 'Pembayaran belum dikirim. Perbaiki masalah lalu coba kembali.' : 'Hasil pengajuan EZYPAY perlu diperiksa sebelum mengirim ulang.'),
                'retry_allowed' => $retry,
                'diagnostic' => [
                    'stage' => $request->attributes->get('ezypay_renewal_stage'),
                    'exception_type' => class_basename($e),
                ],
                'checkoutUrl' => $committed ? $request->attributes->get('ezypay_checkout_url') : null,
                'installment_url' => $committed ? $request->attributes->get('ezypay_installment_url') : null,
            ], 502);
        }
    }

    public function cashierRenewal(Request $request)
    {
        $method = PaymentMethod::find($request->payment_method);
        $isEzypay = $method && strcasecmp(trim($method->payment_method), 'EZYPAY') === 0;
        $service = $request->attributes->get('ezypay_renewal_service');
        abort_if($isEzypay && !$service, 422, 'Gunakan proses renewal EZYPAY.');
        $payment_number = $this->getAutoNumber('1', $request->id);
        if ($isEzypay) {
            DB::beginTransaction();
            $request->merge(['pay' => 0]);
        }

        $user = User::findOrFail($request->selectedmember);
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

        $request->attributes->set('ezypay_renewal_stage', 'save_renewal_payment');
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

        if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
            $member_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
            $member_payment->bank_id = 1;
        endif;

        if(is_null($member_payment->bank_id)){
            $member_payment->bank_id = 1;
        }

        $member_payment->payment_method_id = $request->payment_method;
        $member_payment->amount_paid = $request->pay;

        if (($request->total - $request->pay) > 0) :
            $member_payment->rest_of_bill = ($request->total - $request->pay);
            $member_payment->payment_status = 'Halfpaid';
        elseif (($request->total - $request->pay) <= 0) :
            $member_payment->payment_status = 'Paid';
        endif;

        if ($isEzypay) {
            $remaining = round((float) $request->total - $request->attributes->get('ezypay_amount_value'), 2);
            $member_payment->payment_status = $remaining > 0 ? 'Halfunpaid' : 'Unpaid';
        }

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

        $request->attributes->set('ezypay_renewal_stage', 'save_renewal_contract');
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

        if ($isEzypay) $continue->payment_status = $remaining > 0 ? 'NotSet' : 'Set';

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
            $request->attributes->set('ezypay_renewal_stage', 'save_renewal_accounting');
            // Amortization
            app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, Carbon::parse($user->user_memberstatus->thru_date)->addDay(1));

            // Accounting
            if (!$isEzypay) {
                app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
                app('App\Http\Controllers\AccountingJournalController')->membership_renewal_journal($member_payment, $membership_type);
            }

            //ACCURATE
            /* $userData = [
                'name' => $user->name,
                'membership_type' => $request->membership_type,
                'fa_date' => $request->fa_date,
                'pay' => $request->pay,
                'userId' => $user->id,
            ];


            Storage::put('preinvoice/'.date("d-m-Y").'_'.$user->id.'_file.txt', json_encode($userData));

            app('App\Http\Controllers\PaymentController')->store_accurate($request->club_id, $userData); */

        endif;

        if ($isEzypay) {
            $request->attributes->set('ezypay_renewal_stage', 'create_ezypay_payment');
            $result = $service->create(
                $user, $membership_type, $request->input('paymentMethodToken'),
                $member_payment->payment_number, $request->boolean('subscription'),
                $request->attributes->get('ezypay_self_payment'), $request->attributes->get('ezypay_amount_value'),
                function (string $reference) use ($service, $user, $request) {
                    $service->markSubmitting($user->id, $request->attributes->get('ezypay_reserved_at'), $reference);
                    $request->attributes->set('ezypay_submission_reference', $reference);
                }
            );
            $request->attributes->set('ezypay_submission_id', $result['id']);
            if ($request->attributes->get('ezypay_self_payment')) {
                $member_payment->ezypay_checkout_id = $result['id'];
            } elseif ($request->boolean('subscription')) {
                $member_payment->ezypay_subscription_id = $result['id'];
            } else {
                $member_payment->ezypay_invoice_id = $result['id'];
            }
            $member_payment->save();
            $purchase = DB::table('ezypay_membership_purchases')
                ->where('renewal_key', $request->renewal_key)->where('renewal_user_id', $user->id);
            $purchase->update([
                'cashier_id' => Auth::id(), 'member_payment_id' => $member_payment->id,
                'continue_membership_id' => $continue->id, 'installment_remaining' => $remaining,
                'installments_completed' => $remaining <= 0, 'updated_at' => Carbon::now(),
            ]);
            $request->attributes->set('ezypay_checkout_url', $result['checkoutUrl']);
            $request->attributes->set('ezypay_response_text', $result['response_text']);
            if ($remaining > 0) {
                $request->attributes->set('ezypay_installment_url', route('ezypay.installments', ['purchase' => $purchase->value('id')]));
            }
            DB::commit();
            $request->attributes->set('ezypay_renewal_committed', true);
            app(\App\Services\EzypayMembershipSubscriptionService::class)->syncToUserMobile($user->id);
        }

        // Sync only after all renewal database writes have completed.
        if ($request->id == 1 && !empty($request->fa_coach)) {
            try {
                $member_payment->refresh();
                app(ApiClientService::class)
                    ->post("/mobile-curves/payment/{$request->fa_coach}", $member_payment->toArray())
                    ->throw();
            } catch (Throwable $e) {
                // API failures must not interrupt receipt generation.
                Log::error('Failed to sync renewal payment to mobile Curves API.', [
                    'member_payment_id' => $member_payment->id,
                    'staff_id' => $request->fa_coach,
                    'exception' => $e,
                ]);
            }
        }

        $pdfData = [];

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

            $pdfData['Terima Dari'] = $user->name;
            $pdfData['No'] = $member_payment->payment_number;
            $Tgl = Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i').' '. substr($staff->name, '0', '24');
            $pdfData['Tgl'] = $Tgl;
            $pdfData['spacer1'] = '-';

            $cmds .= $esc . 'a' . '0';
            $cmds .= " " . $member_type->membership_type_name;
            $cmds .= $newLine;

            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
            $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);

            $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
            $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');

            $Diskon = number_format($request->discount, 0, '', '.');

            $pdfData['Biaya Layanan'] = $BiayaLayanan;
            $pdfData['Biaya Bulanan'] = $BiayaBulanan;
            $pdfData['Diskon'] = $Diskon;
            $pdfData['spacer2'] = '-';

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $cmds .= new Item(" Total : ", $request->total, 2);

            $Total = number_format($request->total, 0, '', '.');
            $pdfData['Total'] = $Total;

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);

                $payment_method_not_1 = $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                $pdfData['payment_method_not_1'] = $payment_method_not_1;

            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                $payment_method_1 = $member_payment->memberpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                $pdfData['payment_method_1'] = $payment_method_1;
            endif;

            $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
            $cmds .= $newLine;

            $SisaHutangMember = $request->total - $request->pay;
            $pdfData['Sisa Hutang Member'] = number_format($SisaHutangMember, 0, '', '.');

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
            //return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');

            //dd($pdfData);
            //Generate PDF Receipt

            //return $this->generate_pdf($pdfData, $club->club_name, 'renewal', $request->attributes->get('ezypay_response_text'));

            //CHECK IF PRINT BUTTON CLICKED
            if(isset($request->axn) && $request->axn === 'print'){
                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }else{
                //PDF Generate
                //dd($pdfData);
                return $this->generate_pdf($pdfData, $club->club_name, 'renewal', $request->attributes->get('ezypay_response_text'));
            }
        }
    }

    public function cashierInstallment(Request $request)
    {
        //Print
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

    //PDF GENERATOR
    public function generate_pdf($param=[], $club, $moduleReq, ?string $ezypayDetails = null){
        $total = $param['Total'];

        $membershipType = isset($param['membershipType']) ? $param['membershipType'] : '';

        $data = [
            'param' => $param,
            'club' => $club,
            'total' => $total,
            'membershipType' => $membershipType,
            'module' => $moduleReq,
        ];

        $pdf = Pdf::loadView('pdf', ['data' => $data ]);
        //dd($pdf);
        //return $pdf->stream();

        $_filename = $club.'__'.date("d-m-Yhis").'__'.$moduleReq.'--'.$param['Terima Dari'].'.pdf';
        $filename = preg_replace('/\s+/', '_', $_filename);

        //INSERT TRANSACTION DETAILS FOR LATER EMAIL USE.
        $receiptDetails = new PaymentReceipts();
        $receiptDetails->transId = $param['No'];
        $receiptDetails->details = json_encode($param);
        $receiptDetails->details_ezypay = $ezypayDetails;
        $receiptDetails->save();
        $paymentReceiptID = $receiptDetails->id;

        if($pdf->save('public/receipts/'. $filename, 'local')){
            return json_encode(['pdf'=> $filename, 'status'=> 1, 'receiptID'=> $paymentReceiptID ]);
        }else{
            return json_encode(['pdf'=> '', 'status'=> 0]);
        }
    }

    public function download_pdf($filename){

        if (Storage::disk('public')->exists('receipts/'.$filename)) {
            return Storage::disk('public')->download('receipts/'.$filename, $filename);
        }else{
            return json_encode(['pdf'=> '', 'status'=> 0]);
        }
    }
    public function send_pdf($filename, $email, $receiptId, $lead, $moduleReq){
        $user = User::where('email', $email)->first();
        $receipt = DB::table('payment_receipts')->where('id', $receiptId)->get();

        //dd($user);
        if(is_numeric($lead)){
            $userLead = User::where('id', $lead)->first();
            if($userLead){
                $lead = $userLead->name;
            }
        }

        if (Storage::disk('public')->exists('receipts/'.$filename)) {
            $pdf = Storage::disk('public')->path('receipts/'.$filename);

            if($user){
                $user->email = $email; //force email since it is manually supplied
                $user->name = $lead;
                Notification::send($user, new SendReceipt($user, $pdf, $receipt, $moduleReq));
            }else{
                $user =  new \stdClass();
                $user->email = $email;
                $user->name = $lead;
                Notification::route('mail', $email)->notify(new SendReceipt($user, $pdf, $receipt, $moduleReq));
            }

            return json_encode(['status'=> 1]);
        }else{
            return json_encode(['status'=> 0]);
        }
    }

    public function cashierMembershipWithEzypay(Request $request)
    {
        if (!$request->isMethod('post') || (int) $request->id !== 1
            || (int) $request->club_id !== (int) Auth::user()->club_id) {
            return response()->json(['message' => 'Permintaan pembayaran EZYPAY tidak valid.'], 422);
        }
        $lead = BuddyReferralLead::where('club_id', Auth::user()->club_id)
            ->where('status_gm', 'Guest')->find($request->lead);
        if (!$lead || empty($lead->customer_id_ezypay)) {
            return response()->json(['message' => 'Lengkapi data customer EZYPAY terlebih dahulu.'], 422);
        }

        $membership = MembershipType::find($request->membership_type);
        $token = $request->input('paymentMethodToken');
        if (!in_array($request->input('self_payment', 0), [0, 1, '0', '1', true, false], true)) {
            return response()->json(['message' => 'Pilihan Pembayaran Mandiri tidak valid.'], 422);
        }
        if (is_string($token) && trim($token) === '') $token = null;
        $request->merge(['paymentMethodToken' => $token]);
        $selfPayment = $request->boolean('self_payment') || $token === null;
        $request->attributes->set('ezypay_self_payment', $selfPayment);
        if (!$membership || empty($membership->plan_id_ezypay)
            || $request->input('plan_id_ezypay') !== $membership->plan_id_ezypay
            || ($token !== null && (!is_string($token) || strlen($token) > 255))) {
            return response()->json(['message' => 'Pilih metode pembayaran dan plan EZYPAY terlebih dahulu.'], 422);
        }
        if (is_string($token) && trim($token) !== '') {
            try {
                $methods = app(ApiClientService::class)
                    ->get('/mobile-curves/ezypay/payment-methods/' . rawurlencode($lead->customer_id_ezypay))
                    ->throw()->json('response.data');
                $selected = is_array($methods) ? collect($methods)->first(function ($method) use ($token) {
                    return is_array($method) && ($method['paymentMethodToken'] ?? null) === $token
                        && ($method['valid'] ?? false) === true;
                }) : null;
                if (!$selected) {
                    return response()->json(['message' => 'Metode pembayaran tidak valid untuk customer EZYPAY ini.'], 422);
                }
            } catch (Throwable $e) {
                Log::warning('Failed to validate selected EZYPAY payment method.', ['lead_id' => $lead->id]);
                return response()->json(['message' => 'Gagal memvalidasi metode pembayaran EZYPAY. Silakan coba kembali.'], 502);
            }
        }

        $available = \App\Model\Club\ClubServiceFee::where('club_id', Auth::user()->club_id)
            ->where('membership_type_id', $membership->id)->whereDate('from_date', '<=', Carbon::today())
            ->whereNull('thru_date')->exists();
        if (!$available || $membership->status !== 'Active') {
            return response()->json(['message' => 'Tipe keanggotaan tidak tersedia untuk klub ini.'], 422);
        }
        if (!in_array($request->input('subscription', 0), [0, 1, '0', '1', true, false], true)) {
            return response()->json(['message' => 'Pilihan Subscription tidak valid.'], 422);
        }
        $pay = $request->input('pay');
        if (!is_numeric($pay) || !is_finite((float) $pay) || (float) $pay <= 0) {
            return response()->json(['message' => 'Isi nominal Bayar lebih dari nol.'], 422);
        }
        $total = $request->input('total');
        if (!is_numeric($total) || !is_finite((float) $total) || (float) $total < (float) $pay) {
            return response()->json(['message' => 'Nominal Bayar tidak boleh melebihi total tagihan.'], 422);
        }
        // Preserve the requested checkout amount before self-payment records paid=0.
        $request->attributes->set('ezypay_amount_value', (float) $pay);
        $ezypay = app(\App\Services\EzypayMembershipSubscriptionService::class);
        try {
            $reservedAt = $ezypay->reserve($lead, $membership, $request->boolean('subscription'), $selfPayment);
            if ($reservedAt === null) {
                $purchase = DB::table('ezypay_membership_purchases')
                    ->where('lead_id', $lead->id)->first(['id', 'status', 'payment_type']);
                $previousPaymentType = match ($purchase->payment_type ?? null) {
                    'invoice' => 'invoice',
                    'subscription' => 'subscription',
                    'checkout' => 'pembayaran mandiri',
                    default => 'pembayaran',
                };
                $message = match ($purchase->status ?? null) {
                    'needs_review' => "Percobaan {$previousPaymentType} EZYPAY #{$purchase->id} perlu diperiksa sebelum pembayaran dapat dikirim ulang.",
                    'preparing', 'preparing_retryable' => 'Pembayaran EZYPAY sedang disiapkan. Coba lagi jika proses terhenti.',
                    'submitting' => "Percobaan {$previousPaymentType} EZYPAY #{$purchase->id} sudah mulai dikirim. Periksa hasilnya sebelum mencoba lagi.",
                    default => 'Pembelian EZYPAY sudah pernah diproses. Periksa status pembayaran sebelum melanjutkan.',
                };

                $this->membershipPaymentLogger()->warning('EZYPAY payment request blocked before submission.', [
                    'lead_id' => $lead->id,
                    'purchase_id' => $purchase->id ?? null,
                    'status' => $purchase->status ?? null,
                    'payment_type' => $purchase->payment_type ?? null,
                ]);

                return response()->json([
                    'message' => $message,
                    'purchase_id' => $purchase->id ?? null,
                    'diagnostic' => ['stage' => 'reserve_ezypay_purchase', 'status' => $purchase->status ?? null],
                ], 409);
            }
        } catch (Throwable $e) {
            $this->membershipPaymentLogger()->error('Failed to reserve EZYPAY purchase.', [
                'lead_id' => $lead->id,
                'exception_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'sqlstate' => $e instanceof \PDOException ? ($e->errorInfo[0] ?? null) : null,
            ]);
            return response()->json([
                'message' => 'Gagal menyiapkan pembelian EZYPAY.',
                'diagnostic' => ['stage' => 'reserve_ezypay_purchase', 'exception_type' => class_basename($e)],
            ], 502);
        }
        $request->attributes->set('ezypay_authorized', true);
        $request->attributes->set('ezypay_reserved_at', $reservedAt);
        $response = $this->cashierMembership($request);
        $result = $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : [];
        if ($result['rolled_back'] ?? false) {
            $retryAllowed = $ezypay->recordRolledBackAttempt(
                $lead->id,
                $reservedAt,
                $request->attributes->get('ezypay_submission_reference'),
                $request->attributes->get('ezypay_submission_id')
            );

            if ($retryAllowed) {
                $result['retry_allowed'] = true;
                $result['message'] = 'Pembayaran belum dikirim ke EZYPAY. Setelah masalah diperbaiki, klik Bayar untuk mencoba kembali.';
                $response->setData($result);
            }
        }
        return $response;
    }

    public function cashierMembership(Request $request){
        $ezypayPaymentResult = null;
        $installmentUrl = null;
        $membershipCommitted = false;
        $transactionLevel = DB::transactionLevel();
        $stage = 'prepare_transaction';

        try{
            $method = PaymentMethod::find($request->payment_method);
            $isEzypay = $method && strcasecmp(trim($method->payment_method), 'EZYPAY') === 0;
            if ($isEzypay && !$request->attributes->get('ezypay_authorized')) {
                return response()->json(['message' => 'Gunakan proses pembayaran EZYPAY untuk metode ini.'], 422);
            }
            $selfPayment = $isEzypay && $request->attributes->get('ezypay_self_payment', false);
            if ($selfPayment) {
                $request->merge(['pay' => 0]);
            }

            $ezypayPaymentNumber = null;
            if ($isEzypay) {
                // Never reuse a reference that may have reached EZYPAY after a local rollback.
                $ezypayPaymentNumber = $this->getAutoNumber('1', $request->id);
            }

            DB::beginTransaction();
            $pdfData = [];

            $stage = 'save_member';
            $membership_type = MembershipType::findOrFail($request->membership_type);
            $lead = $this->updateBuddyReferralLead($request);
            $this->insertGuestAppointmentAndStatus($lead, $request->fa_date, $request->id, 'Member');
            $user = $this->insertUser($lead, $request->club_id, $request->id, $isEzypay);

            $stage = 'save_member_payment';
            $payment_number = $ezypayPaymentNumber ?? $this->getAutoNumber('1', $request->id);
            $member_payment = $this->insertMemberPayment($payment_number, $request, $user->id, $request->id, $isEzypay);
            //dd($member_payment);

            $stage = 'save_membership_details';
            $digital_product_inventory = $this->insertDigitalProduct($request->club_id, $member_payment, $request);

            $member_status = $this->insertMemberStatus($request, $user);

            //Adding LogMemberStatus (Log for Member's Membership History)
            $this->insertLogMemberStatus($user->id, $member_status, $request);

            //Adding MemberPersonal (member_personals table)
            $this->insertMemberPersonal($request, $lead->id, $user, $isEzypay ? $lead : null);

            $stage = 'assign_coach';
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

            $stage = 'save_staff_points_and_accounting';
            //Staff Point
            if ($request->id == 1) :
                // Coach Point
                app('App\Http\Controllers\PointStaffController')->staff_point_apo($lead->staff_id, $request->fa_date);
                app('App\Http\Controllers\PointStaffController')->staff_point_apo_showup($lead->staff_id, $request->fa_date, 'showup');
                app('App\Http\Controllers\PointStaffController')->add_br_point($lead->staff_id, 28, $request->fa_date, $lead);
                app('App\Http\Controllers\PointStaffController')->membership_sales_point($user->id, $request->membership_type, $member_payment->id);

                // Amortisasi
                app('App\Http\Controllers\AccountingAmortizationController')->create_amortization($member_payment, $membership_type, null);

                // EZYPAY payment requests are pending until the provider confirms receipt.
                if (!$isEzypay) {
                    app('App\Http\Controllers\TempJournalController')->membership_payment($member_payment, false);
                    app('App\Http\Controllers\AccountingJournalController')->membership_journal($member_payment, $membership_type);
                }

                //ACCURATE
                /* $userData = [
                    'name' => str_replace("%20", " ", $request->premember_name),
                    'membership_type' => $request->membership_type,
                    'fa_date' => $request->fa_date,
                    'pay' => $request->pay,
                ];

                app('App\Http\Controllers\PaymentController')->store_accurate($request->club_id, $userData); */

            endif;

            if ($isEzypay && $request->id == 1) {
                $stage = 'create_ezypay_payment';
                $subscription = $request->boolean('subscription');
                $ezypay = app(\App\Services\EzypayMembershipSubscriptionService::class);
                $ezypayPaymentResult = $ezypay->create(
                    $lead, $membership_type, $request->input('paymentMethodToken'),
                    $member_payment->payment_number, $subscription, $selfPayment,
                    $request->attributes->get('ezypay_amount_value'),
                    function (string $reference) use ($ezypay, $lead, $request) {
                        $ezypay->markSubmitting($lead->id, $request->attributes->get('ezypay_reserved_at'), $reference);
                        $request->attributes->set('ezypay_submission_reference', $reference);
                    }
                );
                $request->attributes->set('ezypay_submission_id', $ezypayPaymentResult['id']);
                $stage = 'save_ezypay_payment_reference';
                if ($selfPayment) {
                    $member_payment->ezypay_checkout_id = $ezypayPaymentResult['id'];
                } elseif ($subscription) {
                    $member_payment->ezypay_subscription_id = $ezypayPaymentResult['id'];
                } else {
                    $member_payment->ezypay_invoice_id = $ezypayPaymentResult['id'];
                }
                $member_payment->save();
            }
            $installmentUrl = null;
            if ($isEzypay) {
                $stage = 'save_installment_state';
                $remaining = round((float) $request->total - $request->attributes->get('ezypay_amount_value'), 2);
                DB::table('ezypay_membership_purchases')->where('lead_id', $lead->id)->update([
                    'cashier_id' => Auth::id(), 'member_payment_id' => $member_payment->id,
                    'installment_remaining' => $remaining, 'installments_completed' => $remaining <= 0,
                    'updated_at' => Carbon::now(),
                ]);
                $member_status->payment_status = $remaining > 0 ? 'NotSet' : 'Set';
                $member_status->save();
                if ($remaining > 0) {
                    $purchaseId = DB::table('ezypay_membership_purchases')->where('lead_id', $lead->id)->value('id');
                    $installmentUrl = route('ezypay.installments', ['purchase' => $purchaseId]);
                }
            }
// tes
            $stage = 'commit_membership';
            DB::commit();
            $membershipCommitted = true;
            // Sync the saved payment after all membership database writes succeed.
            if ($request->id == 1 && !empty($request->fa_coach)) {
                try {
                    $member_payment->refresh();
                    app(ApiClientService::class)
                        ->post("/mobile-curves/payment/{$request->fa_coach}", $member_payment->toArray())
                        ->throw();
                } catch (Throwable $e) {
                    // API failures must not interrupt receipt generation after commit.
                    Log::error('Failed to sync membership payment to mobile Curves API.', [
                        'member_payment_id' => $member_payment->id,
                        'staff_id' => $request->fa_coach,
                        'exception' => $e,
                    ]);
                }
            }

            $stage = 'prepare_receipt';
            $response = null;
            $receiptReady = true;
            if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
                $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
                $printerName = urldecode($request->input('printerName'));

                $club = ClubInfo::findOrFail($request->club_id);
                $coach = User::findOrFail($request->fa_coach);
                $member_type = MembershipType::findOrFail($request->membership_type);
                $digital_product = DigitalProduct::find($request->digital_product);
                $esc = '0x1B'; //ESC byte in hex notation
                $newLine = '0x0A'; //LF byte in hex notation

                $cmds = $this->setUpPrinter();
                $cmds .= $this->setPrintLogo($club->club_name); //disable in local

                $cmds .= "Terima Dari : " . $lead->fullname;
                $cmds .= $newLine;
                $cmds .= "No : " . $member_payment->payment_number;
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '1';
                $cmds .= new Item('Tgl: ' . Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i'), substr($coach->name, '0', '24'), 1);
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $cmds .= $esc . 'a' . '0';
                $cmds .= " " . $member_type->membership_type_name;
                $cmds .= $newLine;

                $harga_digital_product_bool = false;

                //$pdfData['Club'] = $club->club_name;
                $pdfData['Terima Dari'] = $lead->fullname;
                $pdfData['No'] = $member_payment->payment_number;
                $Tgl = Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i').' '. substr($coach->name, '0', '24');
                $pdfData['Tgl'] = $Tgl;
                $pdfData['spacer1'] = '-';


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

                                $Biaya = $request->sf_amount + $request->mf_amount - $harga_digital_product;
                                $pdfData['Biaya'] = number_format($Biaya, 0, '', '.');

                                $Biaya2 = $digital_product->item_name . " : ". number_format($harga_digital_product, 0, '', '.');
                                $pdfData['Biaya2'] = $Biaya2;

                            } else {
                                $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                                $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);

                                $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
                                $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');
                                $pdfData['Biaya Layanan'] = $BiayaLayanan;
                                $pdfData['Biaya Bulanan'] = $BiayaBulanan;
                            }
                        else :
                            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);

                            $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
                            $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');

                            $pdfData['Biaya Layanan'] = $BiayaLayanan;
                            $pdfData['Biaya Bulanan'] = $BiayaBulanan;
                        endif;
                    } else {
                        $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount, 2);
                        $Biaya3 = $request->sf_amount + $request->mf_amount;
                        $pdfData['Biaya 3'] = number_format($Biaya3, 0, '', '.');
                    }
                } else {
                    $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                    $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);
                $Diskon = number_format($request->discount, 0, '', '.');
                $pdfData['Diskon'] = $Diskon;

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $pdfData['spacer2'] = '-';

                $pdfData['membershipType'] = $member_type->membership_type_name;

                if ($harga_digital_product_bool) {
                    //$cmds .= new Item(" Total : ", $request->total /*+ $harga_digital_product*/, 2);
                    $cmds .= new Item(" Total : ", $request->total , 2);

                    $Total = number_format($request->total, 0, '', '.');
                    $pdfData['Total'] = $Total;

                } else {
                    $cmds .= new Item(" Total : ", $request->total, 2);
                    $Total = number_format($request->total, 0, '', '.');
                    $pdfData['Total'] = $Total;
                }

                if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);

                    $payment_method_not_1 = $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                    $pdfData['payment_method_not_1'] = $payment_method_not_1;

                elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                    $payment_method_1 = $member_payment->memberpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                    $pdfData['payment_method_1'] = $payment_method_1;
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
                $cmds .= $newLine;

                $SisaHutangMember = $request->total - $request->pay;
                $pdfData['Sisa Hutang Member'] = number_format($SisaHutangMember, 0, '', '.');

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
                //return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');

                //dd($pdfData);
                //Generate PDF Receipt

                //return $this->generate_pdf($pdfData, $club->club_name, 'membership');

                //CHECK IF PRINT BUTTON CLICKED
                if(isset($request->axn) && $request->axn === 'print'){
                    //Send ClientPrintJob back to the client
                    $response = response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
                }else{
                    //PDF Generate
                    $receipt = json_decode($this->generate_pdf($pdfData, $club->club_name, 'membership', $ezypayPaymentResult['response_text'] ?? null), true);
                    $receiptReady = ($receipt['status'] ?? 0) === 1;
                    if ($selfPayment) {
                        $receipt['checkoutUrl'] = $ezypayPaymentResult['checkoutUrl'];
                    }
                    $receipt['installment_url'] = $installmentUrl;
                    $response = response()->json($receipt);
                }
            }

            if ($user->exists && $receiptReady) {
                app(\App\Services\EzypayMembershipSubscriptionService::class)->syncToUserMobile($user->id);
                app(\App\Services\EzypayMembershipSubscriptionService::class)->syncAddMemberPaymentMobile($member_payment->toArray(), Auth::id());

            }

            return $response;
        } catch (Throwable $e) {
            if (!$membershipCommitted && DB::transactionLevel() > $transactionLevel) {
                DB::rollBack();
            }
            $cause = $e->getPrevious() ?? $e;
            $applicationFrames = collect($cause->getTrace())
                ->filter(fn ($frame) => str_starts_with($frame['file'] ?? '', app_path() . DIRECTORY_SEPARATOR))
                ->take(8)
                ->map(fn ($frame) => ['file' => $frame['file'], 'line' => $frame['line'] ?? null])
                ->values()->all();

            $databaseObject = null;
            if ($cause instanceof \PDOException) {
                preg_match('/\b(?:constraint|column|relation) "([a-z_][a-z0-9_.]*)"/i', $cause->errorInfo[2] ?? '', $matches);
                $databaseObject = $matches[1] ?? null;
            }

            $errorResponse = $cause instanceof \Illuminate\Http\Client\RequestException ? $cause->response : null;
            $providerCode = $errorResponse?->json('error.code');

            $sqlstate = $cause instanceof \PDOException ? ($cause->errorInfo[0] ?? null) : null;
            $providerCode = is_string($providerCode) && preg_match('/^[a-z][a-z0-9_]{0,63}$/iD', $providerCode)
                ? $providerCode : null;

            $this->membershipPaymentLogger()->error('Membership payment failed.', [
                'stage' => $stage,
                'ezypay_submission_reference' => $request->attributes->get('ezypay_submission_reference'),
                'lead_id' => (int) $request->lead,
                'club_id' => (int) $request->club_id,
                'payment_method_id' => (int) $request->payment_method,
                'committed' => $membershipCommitted,
                'exception_type' => get_class($e),
                'cause_type' => get_class($cause),
                'file' => $cause->getFile(),
                'line' => $cause->getLine(),
                'application_frames' => $applicationFrames,
                'sqlstate' => $sqlstate,
                'database_object' => $databaseObject,
                'http_status' => $errorResponse?->status(),
                'provider_code' => $providerCode,
            ]);

            $appPrefix = app_path() . DIRECTORY_SEPARATOR;
            $origin = str_starts_with($cause->getFile(), $appPrefix)
                ? ['file' => $cause->getFile(), 'line' => $cause->getLine()]
                : ($applicationFrames[0] ?? null);

            return response()->json(['message' => $membershipCommitted
                ? 'Pembayaran sudah tersimpan, tetapi pembuatan struk gagal. Jangan ulangi pembayaran.'
                : 'Pembayaran gagal. Perubahan data membership sudah di-rollback. Periksa status EZYPAY sebelum mencoba kembali.',
                'rolled_back' => !$membershipCommitted,
                'diagnostic' => [
                    'stage' => $stage,
                    'file' => $origin ? 'app/' . substr($origin['file'], strlen($appPrefix)) : null,
                    'line' => $origin['line'] ?? null,
                    'exception_type' => class_basename($cause),
                    'sqlstate' => $sqlstate,
                    'database_object' => $databaseObject,
                    'http_status' => $errorResponse?->status(),
                    'provider_code' => $providerCode,
                ],
                'installment_url' => $membershipCommitted ? $installmentUrl : null,
                'checkoutUrl' => $membershipCommitted ? ($ezypayPaymentResult['checkoutUrl'] ?? null) : null], 502);
        }
    }

    private function membershipPaymentLogger(): \Psr\Log\LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'warning',
            'locking' => true,
        ]);
    }

    public function cashierMembershipDummy(Request $request){

        DB::beginTransaction();

        try{
            $pdfData = [];

            $membership_type = MembershipType::findOrFail($request->membership_type);
            $lead = $this->updateBuddyReferralLead($request);
            $this->insertGuestAppointmentAndStatus($lead, $request->fa_date, $request->id, 'Member');
            $user = $this->insertUser($lead,$request->club_id,$request->id);

            $payment_number = $this->getAutoNumber('1', $request->id);
            $member_payment = $this->insertMemberPayment($payment_number, $request, $user->id, $request->id);
            //dd($member_payment);

            $digital_product_inventory = $this->insertDigitalProduct($request->club_id, $member_payment, $request);

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
                //
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
                //dd($request);


                //ACCURATE
                /* $userData = [
                    'name' => str_replace("%20", " ", $request->premember_name),
                    'membership_type' => $request->membership_type,
                    'fa_date' => $request->fa_date,
                    'pay' => $request->pay,
                ];

                app('App\Http\Controllers\PaymentController')->store_accurate($request->club_id, $userData); */

            endif;

            DB::commit();

            //Print
            //dd($request->exists(WebClientPrint::CLIENT_PRINT_JOB));
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
                $cmds .= $this->setPrintLogo($club->club_name); //disable in local

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

                //$pdfData['Club'] = $club->club_name;
                $pdfData['Terima Dari'] = $lead->fullname;
                $pdfData['No'] = $member_payment->payment_number;
                $Tgl = Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i').' '. substr($user->name, '0', '24');
                $pdfData['Tgl'] = $Tgl;
                $pdfData['spacer1'] = '-';


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

                                $Biaya = $request->sf_amount + $request->mf_amount - $harga_digital_product;
                                $pdfData['Biaya'] = number_format($Biaya, 0, '', '.');

                                $Biaya2 = $digital_product->item_name . " : ". number_format($harga_digital_product, 0, '', '.');
                                $pdfData['Biaya2'] = $Biaya2;

                            } else {
                                $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                                $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);

                                $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
                                $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');
                                $pdfData['Biaya Layanan'] = $BiayaLayanan;
                                $pdfData['Biaya Bulanan'] = $BiayaBulanan;
                            }
                        else :
                            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);

                            $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
                            $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');

                            $pdfData['Biaya Layanan'] = $BiayaLayanan;
                            $pdfData['Biaya Bulanan'] = $BiayaBulanan;
                        endif;
                    } else {
                        $cmds .= new Item(" Biaya : ", $request->sf_amount + $request->mf_amount, 2);
                        $Biaya3 = $request->sf_amount + $request->mf_amount;
                        $pdfData['Biaya 3'] = number_format($Biaya3, 0, '', '.');
                    }
                } else {
                    $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
                    $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
                }

                $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);
                $Diskon = number_format($request->discount, 0, '', '.');
                $pdfData['Diskon'] = $Diskon;

                $cmds .= $esc . 'a' . '1';
                $cmds .= "----------------------------------------------";
                $cmds .= $newLine;

                $pdfData['spacer2'] = '-';

                $pdfData['membershipType'] = $member_type->membership_type_name;

                if ($harga_digital_product_bool) {
                    //$cmds .= new Item(" Total : ", $request->total /*+ $harga_digital_product*/, 2);
                    $cmds .= new Item(" Total : ", $request->total , 2);

                    $Total = number_format($request->total, 0, '', '.');
                    $pdfData['Total'] = $Total;

                } else {
                    $cmds .= new Item(" Total : ", $request->total, 2);
                    $Total = number_format($request->total, 0, '', '.');
                    $pdfData['Total'] = $Total;
                }

                if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);

                    $payment_method_not_1 = $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                    $pdfData['payment_method_not_1'] = $payment_method_not_1;

                elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                    $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                    $payment_method_1 = $member_payment->memberpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                    $pdfData['payment_method_1'] = $payment_method_1;
                endif;

                $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
                $cmds .= $newLine;

                $SisaHutangMember = $request->total - $request->pay;
                $pdfData['Sisa Hutang Member'] = number_format($SisaHutangMember, 0, '', '.');

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
                //return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');

                dd($pdfData);
                //Generate PDF Receipt

                //return $this->generate_pdf($pdfData, $club->club_name, 'membership');

                //CHECK IF PRINT BUTTON CLICKED
                if(isset($request->axn) && $request->axn === 'print'){
                    //Send ClientPrintJob back to the client
                    return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
                }else{
                    //PDF Generate
                    return $this->generate_pdf($pdfData, $club->club_name, 'membership');
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
        } catch (Throwable $e) {
            Log::error($e);
            DB::rollBack();
        }
    }

    public function promoMembership(Request $request)
    {
        $prename = str_replace('%20', ' ', $request->premember_name);
        $lead = BuddyReferralLead::find($request->lead);
        $pdfData = [];

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

        if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
            $gm_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
            $gm_payment->bank_id = 1;
        endif;

        if(is_null($gm_payment->bank_id)){
            $gm_payment->bank_id = 1;
        }

        //dd($gm_payment);
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

            //ACCURATE
            /* $userData = [
                'name' => str_replace("%20", " ", $request->premember_name),
                'membership_type' => $request->accurate_code,
                'fa_date' => $request->fa_date,
                'pay' => $request->pay,
            ];

            app('App\Http\Controllers\PaymentController')->store_accurate_promo($request->club_id, $userData); */
        endif;

        // Sync only after all promo database writes have completed.
        if ($request->id == 1 && !empty($request->fa_coach)) {
            try {
                $gm_payment->refresh();
                app(ApiClientService::class)
                    ->post("/mobile-curves/guest-member-payment/{$request->fa_coach}", $gm_payment->toArray())
                    ->throw();
            } catch (Throwable $e) {
                // API failures must not interrupt receipt generation.
                Log::error('Failed to sync promo payment to mobile Curves API.', [
                    'guest_member_payment_id' => $gm_payment->id,
                    'staff_id' => $request->fa_coach,
                    'exception' => $e,
                ]);
            }
        }

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

            //$pdfData['Club'] = $club->club_name;
            $pdfData['Terima Dari'] = $prename;
            $pdfData['No'] = $gm_payment->payment_number;
            $Tgl = Carbon::parse($request->fa_date)->format('d-m-Y H:i').' '. substr($user->name, '0', '24');
            $pdfData['Tgl'] = $Tgl;
            $pdfData['spacer1'] = '-';

            $pdfData['Promo'] = $promo->gm_name;
            $Biaya =  $request->price;
            $pdfData['Biaya'] = number_format($Biaya, 0, '', '.');
            $pdfData['spacer2'] = '-';

            $Total = number_format($request->total, 0, '', '.');
            $pdfData['Total'] = $Total;

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $cmds .= new Item(" " . $gm_payment->gmpayment_paymentmethod->payment_method . " " . $gm_payment->gmpayment_bank->bank_abv . " : ", $request->pay, 2);

                $payment_method_not_1 = $gm_payment->gmpayment_paymentmethod->payment_method . " " . $gm_payment->gmpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                $pdfData['payment_method_not_1'] = $payment_method_not_1;

            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $cmds .= new Item(" " . $gm_payment->gmpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                $payment_method_1 = $gm_payment->gmpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                $pdfData['payment_method_1'] = $payment_method_1;
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
            //return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');

            //dd($pdfData);
            //Generate PDF Receipt

            //return $this->generate_pdf($pdfData, $club->club_name, 'promo');

            //CHECK IF PRINT BUTTON CLICKED
            if(isset($request->axn) && $request->axn === 'print'){
                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }else{
                //PDF Generate
                return $this->generate_pdf($pdfData, $club->club_name, 'promo');
            }
        }
    }

    public function promoConvertMembership(Request $request)
    {
        $pdfData = [];
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

        if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
            $member_payment->bank_id = $request->bank;
        elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
            $member_payment->bank_id = 1;
        endif;

        if(is_null($member_payment->bank_id)){
            $member_payment->bank_id = 1;
        }

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

        // Sync only after all promo conversion database writes have completed.
        if ($request->id == 1 && !empty($request->fa_coach)) {
            try {
                $member_payment->refresh();
                app(ApiClientService::class)
                    ->post("/mobile-curves/payment/{$request->fa_coach}", $member_payment->toArray())
                    ->throw();
            } catch (Throwable $e) {
                // API failures must not interrupt receipt generation.
                Log::error('Failed to sync promo conversion payment to mobile Curves API.', [
                    'member_payment_id' => $member_payment->id,
                    'staff_id' => $request->fa_coach,
                    'exception' => $e,
                ]);
            }
        }

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

            //$pdfData['Club'] = $club->club_name;
            $pdfData['Terima Dari'] = $prename;
            $pdfData['No'] = $member_payment->payment_number;
            $Tgl = Carbon::parse($member_payment->payment_date)->format('d-m-Y H:i').' '. substr($user->name, '0', '24');
            $pdfData['Tgl'] = $Tgl;
            $pdfData['spacer1'] = '-';

            $cmds .= $esc . 'a' . '0';
            $cmds .= "Konversi Keanggotaan " . $member_type->membership_type_name;
            $cmds .= $newLine;

            $pdfData['Konversi Keanggotaan'] = $member_type->membership_type_name;

            $cmds .= new Item(" Biaya Layanan : ", $request->sf_amount, 2);
            $cmds .= new Item(" Biaya Bulanan : ", $request->mf_amount, 2);
            $cmds .= new Item(" Diskon : (-) ", $request->discount, 2);
            $cmds .= new Item(" Simpanan : (-) ", $request->dp_deposit, 2);

            $cmds .= $esc . 'a' . '1';
            $cmds .= "----------------------------------------------";
            $cmds .= $newLine;

            $BiayaLayanan = number_format($request->sf_amount, 0, '', '.');
            $BiayaBulanan = number_format($request->mf_amount, 0, '', '.');

            $Diskon = number_format($request->discount, 0, '', '.');
            $Simpanan = number_format($request->dp_deposit, 0, '', '.');

            $pdfData['Biaya Layanan'] = $BiayaLayanan;
            $pdfData['Biaya Bulanan'] = $BiayaBulanan;
            $pdfData['Diskon'] = $Diskon;
            $pdfData['Simpanan'] = $Simpanan;
            $pdfData['spacer2'] = '-';

            $cmds .= new Item(" Total : ", $request->total, 2);

            $Total = number_format($request->total, 0, '', '.');
            $pdfData['Total'] = $Total;

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ", $request->pay, 2);

                $payment_method_not_1 = $member_payment->memberpayment_paymentmethod->payment_method . " " . $member_payment->memberpayment_bank->bank_abv . " : ". number_format($request->pay, 0, '', '.');

                $pdfData['payment_method_not_1'] = $payment_method_not_1;

            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $cmds .= new Item(" " . $member_payment->memberpayment_paymentmethod->payment_method . " : ", $request->pay, 2);

                $payment_method_1 = $member_payment->memberpayment_paymentmethod->payment_method . " : ". number_format($request->pay, 0, '', '.');
                $pdfData['payment_method_1'] = $payment_method_1;
            endif;

            $cmds .= new Item(" Sisa Hutang Member : ", $request->total - $request->pay, 2);
            $cmds .= $newLine;

            $SisaHutangMember = $request->total - $request->pay;
            $pdfData['Sisa Hutang Member'] = number_format($SisaHutangMember, 0, '', '.');

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
            //return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');

            //dd($pdfData);
            //Generate PDF Receipt

            //return $this->generate_pdf($pdfData, $club->club_name, 'promoConvert', );

            //CHECK IF PRINT BUTTON CLICKED
            if(isset($request->axn) && $request->axn === 'print'){
                //Send ClientPrintJob back to the client
                return response($cpj->sendToClient())->header('Content-Type', 'application/octet-stream');
            }else{
                //PDF Generate
                return $this->generate_pdf($pdfData, $club->club_name, 'promoConvert', );
            }
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
            /* if ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $member_debt_payment->bank_id = 1;
            else :
                $member_debt_payment->bank_id = $request->bank;
            endif; */

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $member_debt_payment->bank_id = $request->bank;
            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $member_debt_payment->bank_id = 1;
            endif;

            if(is_null($member_debt_payment->bank_id)){
                $member_debt_payment->bank_id = 1;
            }

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
            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $bank = Bank::findOrFail($request->bank);
                $cmds .= new Item(" " . $payment_method->payment_method . " " . $bank->bank_abv . " : ", $request->total_pay, 2);
            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
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
        //Print
        if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {
            $useDefaultPrinter = ($request->input('useDefaultPrinter') === 'checked');
            $printerName = urldecode($request->input('printerName'));

            $product = ProductPayment::findOrFail($request->id);

            $user = User::find($product->cashier_id);
            $pembeli = User::find($product->member_id);
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

            $cmds .= $newLine;
            $cmds .= $esc . 'a' . '1';
            $cmds .= $esc . '!' . '0x35';
            $cmds .= "Terima Kasih";
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

            if ($request->payment_method != 1 || $request->payment_method != 6  || $request->payment_method != 7) :
                $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " " . $locker_payment->lockerpayment_bank->bank_abv . " : ", $locker_payment->pay, 2);
            elseif ($request->payment_method == 1 || $request->payment_method == 6  || $request->payment_method == 7) :
                $cmds .= new Item(" " . $locker_payment->lockerpayment_paymentmethod->payment_method . " : ", $locker_payment->pay, 2);
            endif;

            $cmds .= $newLine;
            $cmds .= $esc . 'a' . '1';
            $cmds .= $esc . '!' . '0x35';
            $cmds .= "Terima Kasih";
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

    private function getAutoNumber($autonumber_id, $no_reprint)
    {
        if ($no_reprint != 1) {
            return AutoNumber::findOrFail($autonumber_id)->current_number - 1;
        }

        return DB::transaction(function () use ($autonumber_id) {
            $counter = AutoNumber::lockForUpdate()->findOrFail($autonumber_id);
            $paymentNumber = $counter->current_number;
            $counter->current_number = $paymentNumber + 1;
            $counter->save();

            return $paymentNumber;
        });
    }


    private function setUpPrinter()
    {
        $esc = '0x1B'; //ESC byte in hex notation
        $cmds = $esc . "@"; //Initializes the printer (ESC @)

        return $cmds;
    }

    private function setPrintLogo($club_name)
    {
        if(env('APP_URL') == 'http://localhost' || !class_exists(\Imagick::class)){
            return;
        }else{
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
