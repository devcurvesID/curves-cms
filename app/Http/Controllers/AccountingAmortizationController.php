<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Accounting\AccountingAccount;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\AmortizationDetail;
use App\Model\Accounting\AmortizationHeader;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Club\ClubInfo;
use App\Model\Jubilant\Jubilant;
use App\Model\MasterData\MembershipType;
use App\Model\Payment\MemberPayment;
use App\User;
use Carbon\Carbon;
use Session;
use Illuminate\Http\Request;

class AccountingAmortizationController extends Controller
{
    public function create_amortization(MemberPayment $member_payment, MembershipType $membership_type, $contract_start) {
        $start_contract = $member_payment->memberpayment_user->user_memberstatus->from_date;
        $this_year_contract = 13 - Carbon::parse($start_contract)->format('n');
        if ($membership_type->duration < 12):
            if ($this_year_contract > $membership_type->duration):
                $this_year_contract = $membership_type->duration;
            endif;
        endif;

        $rest_of_month = $membership_type->duration;
        $amount_paid = $member_payment->amount_paid - ($member_payment->sf_amount - $member_payment->dc_amount);
        $monthly_bill = (($member_payment->amount_due - ($member_payment->sf_amount - $member_payment->dc_amount)) / $membership_type->duration);

        // Set Amortization Header
        $counter = 0;
        while ($rest_of_month > 0):
            $amortization_header = new AmortizationHeader;
            $amortization_header->user_id = $member_payment->user_id;
            $amortization_header->membership_type_id = $membership_type->id;
//            $amortization_header->amortization_status = 'Ongoing';
//            $amortization_header->contract_start = Carbon::parse($start_contract)->addMonths();
            $amortization_header->membership_duration = $membership_type->duration;

            if ($counter == 0):
                $amortization_header->contract_start = ($contract_start == null) ? $start_contract : $contract_start;
                $amortization_header->amortization_month = $this_year_contract;
                $rest_of_month -= $this_year_contract;
            else:
                $amortization_header->contract_start = Carbon::parse($start_contract)->addMonths($this_year_contract)->addYears($counter - 1);
                $amortization_header->amortization_month = ($rest_of_month > 12) ? 12 : $rest_of_month;
                $rest_of_month -= 12;
            endif;

            $amortization_header->rest_of_amortization_month = ($rest_of_month > 0) ? $rest_of_month : 0;
//            $amortization_header->total_amount_due = $member_payment->amount_due - ($member_payment->sf_amount - $member_payment->dc_amount);
            $amortization_header->total_amount_due = $monthly_bill * $amortization_header->amortization_month;

            if (($amortization_header->total_amount_due - $amount_paid) > 0):
                $amortization_header->amortization_status = 'Ongoing';
            else:
                $amortization_header->amortization_status = 'Done';
            endif;

            $amortization_header->monthly_bill = $monthly_bill;
            $amortization_header->status = 'Published';
            $amortization_header->save();

            // Set Amortization Detail
            if ($counter == 0):
//                $month_number = 13 - $amortization_header->amortization_month;
                $month_number = Carbon::parse($amortization_header->contract_start)->format('n');
            else:
                $month_number = 1;
            endif;

            $additional_month = 0;
            $last_month = 0;
            while (($amount_paid > 0) && ($month_number < 13)):
                $amortization_detail = new AmortizationDetail;
                $amortization_detail->amortization_header_id = $amortization_header->id;
                $amortization_detail->club_id = $member_payment->memberpayment_user->club_id;
                $amortization_detail->amortization_date = Carbon::parse($amortization_header->contract_start)->addMonths($additional_month);

                // Modif 31012020
                $this_month = Carbon::parse($amortization_detail->amortization_date)->format('n');
                if ($last_month > 0):
                    if ((Carbon::parse($amortization_detail->amortization_date)->format('n') - $last_month) > 1):
                        $this_month = Carbon::parse($amortization_detail->amortization_date)->format('n') - 1;
                    endif;
                endif;
                $amortization_detail->month_number = $this_month;

                if ($amount_paid >= $amortization_header->monthly_bill):
                    $amortization_detail->amount_paid = $amortization_header->monthly_bill;
                    $amortization_detail->status = 'Published';
                else:
                    $amortization_detail->amount_paid = $amount_paid;
                    $amortization_detail->status = 'Pending';
                endif;

                $amortization_detail->accounting_status = 'Pending';
                $amortization_detail->save();

                $amount_paid -= $amortization_header->monthly_bill;
                $month_number++;
                $additional_month++;
                $last_month = $this_month;
            endwhile;

            $counter++;
        endwhile;
    }

    public function update_amortization(MemberPayment $member_payment) {
        $pay = $member_payment->amount_paid;

        // Cari amortization_headers dengan:
        // amortization_status = Ongoing; contract_start >= member_status->from_date; order asc;
        $amortization_headers = AmortizationHeader::where('user_id', '=', $member_payment->user_id)
            ->where('amortization_status', '=', 'Ongoing')
            ->whereDate('contract_start', '<=', Carbon::parse($member_payment->memberpayment_user->user_memberstatus->from_date)->toDateString())
            ->where('status', '=', 'Published')
            ->orderBy('contract_start', 'asc')
            ->get();

        if (!empty($amortization_headers)):
            // Cek amortization_details
            // Jika jumlahnya < amortization_headers->amortization_month, maka dibuat amortization_details sesuai dengan jumlah uang yg dibayarkan
            // Cek juga amortization_details yang status = Pending
            foreach ($amortization_headers as $amortization_header):
                if ($pay > 0):
                    $a_header = AmortizationHeader::findOrFail($amortization_header->id);
                    $last_month_number = 0;
                    $a_header_gate = true;

                    foreach ($a_header->amorheader_amordetail->where('status', 'Pending') as $a_detail):
                        if ($pay > 0):
                            $amor_detail = AmortizationDetail::findOrFail($a_detail->id);
                            if ($pay >= ($a_header->monthly_bill - $a_detail->amount_paid)):
                                $pay -= $a_header->monthly_bill - $a_detail->amount_paid;
                                $amor_detail->amount_paid = $a_header->monthly_bill;
                                $amor_detail->status = 'Published';
                            else:
                                $amor_detail->amount_paid = $amor_detail->amount_paid + $pay;
                                $pay = 0;
                            endif;
                            $amor_detail->save();

                            $last_month_number = $amor_detail->month_number;
                        endif;
                    endforeach;

                    // Jika masih ada amortization month yang belum dibuatkan amortization_details nya
                    if (!empty($a_header->amorheader_amordetail)):
                        $noted_amor_detail = count($a_header->amorheader_amordetail);
                    else:
                        $noted_amor_detail = 0;
                    endif;

                    if ($noted_amor_detail < $a_header->amortization_month):
                        // Jika tidak ada pending amortization_details, cari amortization_detail pada bulan terakhir
                        if ($last_month_number == 0):
                            foreach ($a_header->amorheader_amordetail as $a_detail):
                                if ($last_month_number < $a_detail->month_number):
                                    $last_month_number = $a_detail->month_number;
                                endif;
                            endforeach;
                        endif;

                        if ($pay > 0):
                            $rest_of_month = $a_header->amortization_month - $noted_amor_detail;
                            while ($rest_of_month > 0):
                                if ($pay > 0):
                                    // Buat amortization_details yang tersisa
                                    $amortization_detail = new AmortizationDetail;
                                    $amortization_detail->amortization_header_id = $a_header->id;
                                    $amortization_detail->club_id = $member_payment->memberpayment_user->club_id;
                                    $amortization_detail->amortization_date = Carbon::parse($a_header->contract_start)->addMonths($last_month_number);
                                    $last_month_number++;

                                    $amortization_detail->month_number = $last_month_number;

                                    if ($pay >= $a_header->monthly_bill):
                                        $amortization_detail->amount_paid = $a_header->monthly_bill;
                                        $amortization_detail->status = 'Published';
                                        $pay -= $a_header->monthly_bill;
                                        $rest_of_month--;
                                    else:
                                        $amortization_detail->amount_paid = $pay;
                                        $amortization_detail->status = 'Pending';
                                        $pay = 0;
                                        $rest_of_month = 0;
                                        $a_header_gate = false;
                                    endif;

                                    $amortization_detail->accounting_status = 'Pending';
                                    $amortization_detail->save();
                                else:
                                    $rest_of_month--;
                                endif;
                            endwhile;
                        endif;
                    endif;

                    if ($a_header_gate):
                        $a_header->amortization_status = 'Done';
                        $a_header->save();
                    endif;
                endif;
            endforeach;
        endif;
    }

    public function amortization_report(Request $request){
        $amortization_reports = [];
        $account_id = session('account')->id;

        $clubs = ClubInfo::whereHas('club_jubilant', function ($jubilant) use ($account_id) {
            $jubilant->whereHas('jubilant_accounting', function ($jub) use ($account_id) {
                $jub->where('accounting_id', '=', $account_id);
            });
        })->orderBy('club_name', 'asc')->get();

        if (!empty($request->year)) {
            $amortization_reports = AmortizationHeader::whereRaw('SUBSTR(CAST(contract_start AS varchar), 1, 4) = ' . '\'' . $request->year . '\'');
            if (!empty($request->clubs)) {
                $amortization_reports = $amortization_reports->whereHas('amorheader_user', function ($q) use ($request) {
                    $q->where('club_id', '=', $request->clubs);
                });
            }

            $amortization_reports->orderBy('contract_start', 'asc');

            $amortization_reports = $amortization_reports->get();
        }

//        foreach ($amortization_reports as $report) {
//            $check = 0;
//
//            $report->setAttribute('nilai_awal', ($report->amortization_month + $report->rest_of_amortization_month < $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0);
//            $report->setAttribute('penambahan_pengurangan', ($report->amortization_month + $report->rest_of_amortization_month >= $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0);
//
//            if ($report->rest_of_amortization_month > 12) {
//                $check = 12;
//            } else {
//                $check = $report->rest_of_amortization_month;
//            }
//
//            $report->setAttribute('value_after_addition', $report->mambership_duration * $report->monthly_bill);
//            $report->setAttribute('masa_tahun', $report->amortization_month + $report->rest_of_amortization_month);
//            $report->setAttribute('nilai_sisa_buku_awal', $report->rest_of_amortization_month * $report->monthly_bill);
////            $report->setAttribute('nilai_sisa_buku_awal', ($report->rest_of_amortization_month > 0) ? $report->rest_of_amortization_month * $report->monthly_bill : $report->monthly_bill);
//            $report->setAttribute('amortization_next', $check * $report->monthly_bill);
//            $report->setAttribute('ak_amor_akhir', $report->amount_due + $report->amortization_next);
//            $report->setAttribute('nilai_sisa_buku_akhir', $report->membership_duration * $report->monthly_bill - $report->ak_amor_akhir);
//        }

        return view('cf_acc.accounting.amortization-report', compact('clubs', 'amortization_reports'));
    }

    public function transfer_amortization(User $member, $trf_date) {
        $total_payment = 0;
        $amor_details = AmortizationDetail::where('amortization_date', '>=', $trf_date)->whereHas('amordetail_amorheader', function ($q) use ($member) {
            $q->where('user_id', '=', $member->id);
        })->get();

        foreach ($amor_details as $amor_detail) {
            $detail = AmortizationDetail::findOrFail($amor_detail->id);
            $detail->club_id = $member->club_id;
            $detail->save();

            $total_payment += $amor_detail->amount_paid;
        }

        // Temp Journal
        app('App\Http\Controllers\TempJournalController')->trf_amortization($member, $total_payment, false);
    }

    public function membership_change_amortization(MemberPayment $member_payment, MembershipType $membership_type) {
//        $deposit = $member_payment->amount_paid - ($member_payment->sf_amount - $member_payment->dc_amount);
        $deposit = $member_payment->mf_amount - $member_payment->rest_of_bill;
        $additional_amor_detail_paid = 0;
        $rest_of_amortization_month = $membership_type->duration;
        $count_year = 0;
        $first_year_contract = 0;

        $amor_headers = AmortizationHeader::where('user_id', '=', $member_payment->user_id)
            ->where('status', '=', 'Published')
            ->whereDate('contract_start', '>=', Carbon::parse($member_payment->memberpayment_user->user_memberstatus->from_date))
            ->orderBy('contract_start', 'asc')
            ->get();

        // Hitung jumlah amortisasi yang telah dibayarkan member
//        foreach ($amor_headers as $amor_header):
//            foreach ($amor_header->amorheader_amordetail as $amor_detail):
//                $deposit += $amor_detail->amount_paid;
//            endforeach;
//        endforeach;

        // Update amortization header & amortization detail
        foreach ($amor_headers as $amor_header):
            $new_amor_header = AmortizationHeader::findOrFail($amor_header->id);
            $total_amount_paid = 0;

            if ($rest_of_amortization_month > 0):
                // Update Amortization Header
                if ($new_amor_header->membership_duration != $membership_type->duration):
                    $new_amor_header->membership_duration = $membership_type->duration;

                    if ($count_year == 0):
                        if ((13 - Carbon::parse($new_amor_header->contract_start)->format('n')) > $rest_of_amortization_month):
                            $new_amor_header->amortization_month = $rest_of_amortization_month;
                            $rest_of_amortization_month = 0;
                        else:
                            $new_amor_header->amortization_month = 13 - Carbon::parse($new_amor_header->contract_start)->format('n');
                            $rest_of_amortization_month -= 13 - Carbon::parse($new_amor_header->contract_start)->format('n');
                        endif;

                        $first_year_contract = $new_amor_header->amortization_month;
                    else:
                        $new_amor_header->amortization_month = ($rest_of_amortization_month > 12) ? 12 : $rest_of_amortization_month;
                        $rest_of_amortization_month = ($rest_of_amortization_month > 12) ? ($rest_of_amortization_month - 12) : 0;
                    endif;

                    $new_amor_header->rest_of_amortization_month = $rest_of_amortization_month;
                endif;

                $new_amor_header->membership_type_id = $membership_type->id;
                $new_amor_header->monthly_bill = $member_payment->mf_amount / $membership_type->duration;
                $new_amor_header->total_amount_due = $new_amor_header->amortization_month * $new_amor_header->monthly_bill;

                $total_amortization_month = $new_amor_header->amortization_month;

                // Cek Amortization Detail
                $amor_details = AmortizationDetail::where('amortization_header_id', '=', $amor_header->id)
                    ->orderBy('amortization_date', 'asc')
                    ->get();

                // Hitung amortization detail dengan accounting_status "Published"
                if (count($amor_details->where('accounting_status', 'Published')) > 0):
                    $published_paid = 0;
                    $total_amortization_month -= count($amor_details->where('accounting_status', 'Published'));
                    foreach ($amor_details->where('accounting_status', 'Published') as $published):
                        $published_paid += $published->amount_paid;
                    endforeach;

                    if ($published_paid > 0):
                        $additional_amor_detail_paid += (count($amor_details->where('accounting_status', 'Published')) * $new_amor_header->monthly_bill) - $published_paid;
                        $total_amount_paid += $published_paid;
                        $deposit -= $published_paid;
                    endif;
                endif;

                // Update Amortization Detail dengan accounting_status "Pending"
                $count_pending = 0;
                $last_month_number = 0;
                // Jika sudah ada amortization detail
                if (count($amor_details->where('accounting_status', 'Pending')) > 0):
                    foreach ($amor_details->where('accounting_status', 'Pending') as $pending):
                        $new_amor_detail = AmortizationDetail::findOrFail($pending->id);

                        if ($deposit != 0):
                            if ($total_amortization_month > 0):
                                if (($count_pending == 0) && ($additional_amor_detail_paid != 0)):
                                    $new_amor_detail->amount_paid = $new_amor_header->monthly_bill + $additional_amor_detail_paid;
                                    $new_amor_detail->status = 'Published';

                                    $deposit -= $new_amor_header->monthly_bill + $additional_amor_detail_paid;
                                    $additional_amor_detail_paid = 0;
                                else:
                                    if ($deposit > 0):
                                        if ($deposit >= $new_amor_header->monthly_bill):
                                            $new_amor_detail->amount_paid = $new_amor_header->monthly_bill;
                                            $new_amor_detail->status = 'Published';

                                            $deposit -= $new_amor_header->monthly_bill;
                                        else:
                                            $new_amor_detail->amount_paid = $deposit;
                                            $new_amor_detail->status = 'Pending';

                                            $deposit = 0;
                                        endif;
                                    elseif ($deposit < 0):
                                        $new_amor_detail->amount_paid = $deposit;
                                        $new_amor_detail->status = 'Pending';

                                        $deposit = 0;
                                    endif;
                                endif;

                                $new_amor_detail->save();
                                $last_month_number = $new_amor_detail->month_number;
                                $total_amount_paid += $new_amor_detail->amount_paid;
                            else:
                                $new_amor_detail->delete();
                            endif;
                        else:
                            $new_amor_detail->delete();
                        endif;

                        $total_amortization_month -= 1;
                        $count_pending++;
                    endforeach;
                endif;

                // Jika total_amortization_month > 0 dan deposit > 0, maka lanjut membuat amortization detail
                if (($total_amortization_month > 0) && ($deposit != 0)):
                    $additional_month = count($amor_details->where('accounting_status', 'Published')) + $count_pending;
                    while ($total_amortization_month > 0):
                        $add_amor_detail = new AmortizationDetail;
                        $add_amor_detail->amortization_header_id = $new_amor_header->id;
                        $add_amor_detail->club_id = $member_payment->memberpayment_user->club_id;
                        $add_amor_detail->amortization_date = Carbon::parse($new_amor_header->contract_start)->addMonths($additional_month);

                        $this_month = Carbon::parse($add_amor_detail->amortization_date)->format('n');
                        if ($last_month_number > 0):
                            if ((Carbon::parse($add_amor_detail->amortization_date)->format('n') - $last_month_number) > 1):
                                $this_month = Carbon::parse($add_amor_detail->amortization_date)->format('n') - 1;
                            endif;
                        endif;
                        $add_amor_detail->month_number = $this_month;

                        if ($deposit >= $new_amor_header->monthly_bill):
                            $add_amor_detail->amount_paid = $new_amor_header->monthly_bill;
                            $add_amor_detail->status = 'Published';

                            $deposit -= $new_amor_header->monthly_bill;
                            $total_amortization_month--;
                        else:
                            $add_amor_detail->amount_paid = $deposit;
                            $add_amor_detail->status = 'Pending';

                            $deposit = 0;
                            $total_amortization_month = 0;
                        endif;

                        $add_amor_detail->save();
                        $total_amount_paid += $add_amor_detail->amount_paid;
                        $last_month_number = $add_amor_detail->month_number;
                        $additional_month++;
                    endwhile;
                endif;

                // Save Edited Amortization Header
                if ($total_amount_paid >= $new_amor_header->total_amount_due):
                    $new_amor_header->amortization_status = 'Done';
                else:
                    $new_amor_header->amortization_status = 'Ongoing';
                endif;
                $new_amor_header->save();

                $count_year++;
            else:
                foreach ($new_amor_header->amorheader_amordetail as $amor_detail):
                    $delete_amor_detail = AmortizationDetail::findOrFail($amor_detail->id);
                    $delete_amor_detail->delete();
                endforeach;

                $new_amor_header->delete();
            endif;
        endforeach;

        // Buat Amortization Header baru jika masih ada sisa bulan amortisasi
        while ($rest_of_amortization_month > 0):
            $add_amor_header = new AmortizationHeader;
            $add_amor_header->user_id = $member_payment->user_id;
            $add_amor_header->membership_type_id = $membership_type->id;
            $add_amor_header->contract_start = Carbon::parse($member_payment->memberpayment_user->user_memberstatus->from_date)->addMonths($first_year_contract)->addYears($count_year - 1);
            $add_amor_header->membership_duration = $membership_type->duration;

            $add_amor_header->amortization_month = ($rest_of_amortization_month > 12) ? 12 : $rest_of_amortization_month;
            $rest_of_amortization_month = ($rest_of_amortization_month > 12) ? ($rest_of_amortization_month - 12) : 0;

            $add_amor_header->rest_of_amortization_month = $rest_of_amortization_month;
            $add_amor_header->monthly_bill = $member_payment->mf_amount / $membership_type->duration;
            $add_amor_header->total_amount_due = $add_amor_header->amortization_month * $add_amor_header->monthly_bill;
            $add_amor_header->status = 'Published';

            // Set Amortization Status
            if ($deposit < $add_amor_header->total_amount_due):
                $add_amor_header->amortization_status = 'Ongoing';
            else:
                $add_amor_header->amortization_status = 'Done';
            endif;

            $add_amor_header->save();

            // Buat Amortization Detail
            if ($deposit != 0):
                for ($i = 0; $i < $add_amor_header->amortization_month; $i++):
                    $create_amor_detail = new AmortizationDetail;
                    $create_amor_detail->amortization_header_id = $add_amor_header->id;
                    $create_amor_detail->club_id = $member_payment->memberpayment_user->club_id;
                    $create_amor_detail->amortization_date = Carbon::parse($add_amor_header->contract_start)->addMonth($i);
                    $create_amor_detail->month_number = $i + 1;

                    if ($deposit >= $add_amor_header->monthly_bill):
                        $create_amor_detail->amount_paid = $add_amor_header->monthly_bill;
                        $create_amor_detail->status = 'Published';

                        $deposit -= $add_amor_header->monthly_bill;
                    else:
                        $create_amor_detail->amount_paid = $deposit;
                        $create_amor_detail->status = 'Pending';

                        $deposit = 0;
                        $i = $add_amor_header->amortization_month;
                    endif;

                    $create_amor_detail->accounting_status = 'Pending';
                    $create_amor_detail->save();
                endfor;
            endif;
        endwhile;
    }

    public function generate_amortization_page(Request $request) {
        $gate = false;

        $user = AccountingAccount::findOrFail(Session::get('account')->id);
        if (count($user->accounting_jubilant) > 0):
            $counter = 0;
            $jubilants = Jubilant::where(function ($jub) use ($user, $counter) {
                foreach ($user->accounting_jubilant as $value):
                    if (empty($counter)):
                        $jub->where('id', '=', $value->id);
                    else:
                        $jub->orWhere('id', '=', $value->id);
                    endif;
                    $counter++;
                endforeach;
            })
                ->pluck('jubilant_name', 'id');

            $counter = 0;
            $clubs = ClubInfo::where(function ($club) use ($jubilants, $counter) {
                foreach ($jubilants as $key => $value):
                    if (empty($counter)):
                        $club->where('jubilant_id', '=', $key);
                    else:
                        $club->orWhere('jubilant_id', '=', $key);
                    endif;
                    $counter++;
                endforeach;
            })
                ->orderBy('club_name', 'asc')
                ->get();
        else:
            return redirect('cf-acc/dashboard');
        endif;


        if (!empty($request->month)):
            $last_day = Carbon::parse(new Carbon('01-' . $request->month))->lastOfMonth();
        else:
            $last_day = Carbon::parse(Carbon::today())->lastOfMonth();
        endif;

        $amortizations = AmortizationDetail::selectRaw(
            'SUM(amount_paid) as total, 
            club_infos.club_name as club, 
            club_infos.id as club_id, 
            to_char(amortization_date,\'Mon\') as month, 
            extract(year from amortization_date) as year')
            ->where('amortization_details.status', '=', 'Published')
            ->where('accounting_status', '=', 'Pending')
            ->whereDate('amortization_date', '<=', Carbon::parse($last_day)->toDateString())
            ->join('club_infos', function ($join) {
                $join->on('club_infos.id', '=', 'amortization_details.club_id');
            });

        if (!empty($request->club)):
            $amortizations->where('club_id', '=', $request->club);
            $gate = true;
        else:
            if (!empty($request->jubilant)):
                $amortizations->whereHas('amordetail_club', function ($club) use ($request) {
                    $club->where('jubilant_id', '=', $request->jubilant);
                });
                $gate = true;
            endif;
        endif;

        if ($gate):
            $amortizations = $amortizations
                ->groupBy('month', 'year', 'club_infos.id')
                ->get();
        else:
            $amortizations = null;
        endif;

        return view('cf_acc.accounting.generate-amortization', compact('jubilants', 'clubs', 'amortizations'));
    }

    public function generate_amortization(Request $request) {
        $this->validate($request, [
            'club' => 'required|exists:club_infos,id',
            'total' => 'required|numeric',
            'date' => 'required',
        ]);

        // Preparations
        $date = explode('-', $request->date);
        $from_date = new Carbon('first day of ' . $date[0] . ' ' . $date[1]);
        $thru_date = new Carbon('last day of ' . $date[0] . ' ' . $date[1]);
        $club = ClubInfo::findOrFail($request->club);
        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $invoice_number = 'AMOR/CIND/' . sprintf('%03d', $club->id) . '/' . $month_roman[Carbon::parse($from_date)->format('n')] . '/' . $date[1];

        // Update seluruh amortization_details
        $amortization_details = AmortizationDetail::where('status', '=', 'Published')
            ->where('accounting_status', '=', 'Pending')
            ->where('club_id', '=', $club->id)
            ->whereDate('amortization_date', '>=', Carbon::parse($from_date)->toDateString())
            ->whereDate('amortization_date', '<=', Carbon::parse($thru_date)->toDateString())
            ->get();

        if (!empty($amortization_details)):
            foreach ($amortization_details as $amor_detail):
                $amor = AmortizationDetail::findOrFail($amor_detail->id);
                $amor->accounting_status = 'Published';
                $amor->save();
            endforeach;
        endif;

        // Masukkan amortisasi ke Accounting Journals V2
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'Journal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Debit
        // Cari COA Debit
        $coa_debit = ChartOfAccount::where('jubilant_id', '=', $club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '1-1260')
                    ->orWhere('coa_name', 'ilike', '%pendapatan jasa di terima di muka%');
            })
            ->first();

        $acc_d = new AccountingJournalV2;
        $acc_d->jubilant_id = $club->jubilant_id;
        $acc_d->coa_id = $coa_debit->id;
        $acc_d->trans_date = $thru_date;
        $acc_d->debit = $request->total;
        $acc_d->invoice_number = $invoice_number;
        $acc_d->information = 'Amortisasi klub ' . ucwords($club->club_name) . ' bulan ' . $date[0] . ' tahun ' . $date[1];
        $acc_d->d_or_c = 'Debit';
        $acc_d->club_id = $club->id;
        $acc_d->pair = $pair->current_number;
        $acc_d->save();

        // Credit
        // Cari COA Debit
        $coa_credit = ChartOfAccount::where('jubilant_id', '=', $club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-1000')
                    ->orWhere('coa_name', 'ilike', '%penjualan jasa%');
            })
            ->first();

        $acc_c = new AccountingJournalV2;
        $acc_c->jubilant_id = $club->jubilant_id;
        $acc_c->coa_id = $coa_credit->id;
        $acc_c->trans_date = $thru_date;
        $acc_c->credit = $request->total;
        $acc_c->invoice_number = $invoice_number;
        $acc_c->information = 'Amortisasi klub ' . ucwords($club->club_name) . ' bulan ' . $date[0] . ' tahun ' . $date[1];
        $acc_c->d_or_c = 'Credit';
        $acc_c->club_id = $club->id;
        $acc_c->pair = $pair->current_number;
        $acc_c->save();

        return response()->json(true);
    }
}
