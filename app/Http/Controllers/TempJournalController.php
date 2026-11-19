<?php

namespace App\Http\Controllers;

use App\AutoNumber;
use App\Model\Accounting\AccountingJournalV2;
use App\Model\Accounting\ChartOfAccount;
use App\Model\Accounting\TempJournal;
use App\Model\HQ\ConsignmentOrder;
use App\Model\HQ\ConsignmentOrderBillPayment;
use App\Model\HQ\HQCredit;
use App\Model\HQ\HQCreditPayment;
use App\Model\MasterData\Bank;
use App\Model\Payment\MemberPayment;
use App\Model\Product\Product;
use App\Model\Product\ProductPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiClientService;

class TempJournalController extends Controller
{
    // Buat PIUTANG Baru
    public function add_hq_credit(HQCredit $hq_credit, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Piutang (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $hq_credit->jubilant_id;
        $temp_journal->coa_id = $hq_credit->coa_id;
        $temp_journal->trans_date = $hq_credit->invoice_date;
        $temp_journal->debit = $hq_credit->subtotal;
        $temp_journal->invoice_number = $hq_credit->invoice_number;
        $temp_journal->information = 'Piutang ' . $hq_credit->invoice_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();

        // Penjualan (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-6000')
                    ->orWhere('coa_name', 'ilike', '%penjualan lainnya%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $hq_credit->jubilant_id;
        $temp_journal->coa_id = !empty($coa_penjualan) ? $coa_penjualan->id : $hq_credit->coa_id;
        $temp_journal->trans_date = $hq_credit->invoice_date;
        $temp_journal->credit = $hq_credit->subtotal;
        $temp_journal->invoice_number = $hq_credit->invoice_number;
        $temp_journal->information = 'Penjualan ' . $hq_credit->invoice_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();
    }

    // Edit PIUTANG
    public function edit_hq_credit(HQCredit $hq_credit, HQCredit $new_hq_credit)
    {
        // Cek jika jurnal titipan sudah APPROVED
        $temp_journal = TempJournal::where('d_or_c', '=', 'Debit')
            ->where('invoice_number', '=', $hq_credit->invoice_number)
            ->orderBy('id', 'desc')
            ->first();

        if (!empty($temp_journal)):
            // IF NOT APPROVED
            if (!$temp_journal->approved):
                $journals = TempJournal::where('invoice_number', '=', $hq_credit->invoice_number)
                    ->where('approved', '=', false)
                    ->where('pair', '=', $temp_journal->pair)
                    ->get();

                foreach ($journals as $journal):
                    if ($journal->d_or_c == 'Debit'):
                        // DEBIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->jubilant_id = $new_hq_credit->jubilant_id;
                        $new_journal->coa_id = $new_hq_credit->coa_id;
                        $new_journal->trans_date = $new_hq_credit->invoice_date;
                        $new_journal->debit = $new_hq_credit->subtotal;
                        $new_journal->credit = 0;
                        $new_journal->save();
                    elseif ($journal->d_or_c == 'Credit'):
                        // CREDIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->jubilant_id = $new_hq_credit->jubilant_id;
                        $new_journal->trans_date = $new_hq_credit->invoice_date;
                        $new_journal->debit = 0;
                        $new_journal->credit = $new_hq_credit->subtotal;
                        $new_journal->save();
                    endif;
                endforeach;
            else:
                // Buat Jurnal PEMBALIK
                $hq_credit->subtotal = $hq_credit->subtotal * (-1);
                $this->add_hq_credit($hq_credit, true);

                // Buat Jurnal Pengganti
                $this->add_hq_credit($new_hq_credit, false);
            endif;
        else:
            // Buat Jurnal Baru
            $this->add_hq_credit($new_hq_credit, false);
        endif;
    }

    // Buat Pembayaran Piutang Baru
    public function add_hq_credit_payment(HQCreditPayment $hq_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Bayar Piutang (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $hq_payment->hqcreditpayment_hqcredit->jubilant_id;

        // Cari COA ID berdasarkan metode pembayaran
        if ($hq_payment->payment_type_id == 1 || $hq_payment->payment_type_id == 6 || $hq_payment->payment_type_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where('id', '=', $hq_payment->hqcreditpayment_bank->coa_id)
                ->first();
        endif;

        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $hq_payment->payment_date;
        $temp_journal->debit = $hq_payment->total;
        $temp_journal->invoice_number = $hq_payment->payment_number;

        if ($hq_payment->payment_type_id == 1 || $hq_payment->payment_type_id == 6 || $hq_payment->payment_type_id == 7):
            $temp_journal->information = 'Kas ' . $hq_payment->payment_number;
        else:
            $temp_journal->information = $coa->coa_name . ' ' . $hq_payment->payment_number;
        endif;

        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();

        // Piutang (CREDIT)
        $coa_piutang = ChartOfAccount::findOrFail($hq_payment->hqcreditpayment_hqcredit->coa_id);

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $hq_payment->hqcreditpayment_hqcredit->jubilant_id;
        $temp_journal->coa_id = !empty($coa_piutang) ? $coa_piutang->id : $hq_payment->hqcreditpayment_hqcredit->coa_id;
        $temp_journal->trans_date = $hq_payment->payment_date;
        $temp_journal->credit = $hq_payment->total;
        $temp_journal->invoice_number = $hq_payment->payment_number;
        $temp_journal->information = 'Piutang ' . $hq_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();
    }

    // Edit Pembayaran Piutang
    public function edit_hq_credit_payment(HQCreditPayment $hq_payment, HQCreditPayment $new_hq_payment)
    {
        // Cek jika jurnal titipan sudah APPROVED
        $temp_journal = TempJournal::where('d_or_c', '=', 'Debit')
            ->where('invoice_number', '=', $hq_payment->payment_number)
            ->orderBy('id', 'desc')
            ->first();

        if (!empty($temp_journal)):
            // IF NOT APPROVED
            if (!$temp_journal->approved):
                $journals = TempJournal::where('invoice_number', '=', $hq_payment->payment_number)
                    ->where('approved', '=', false)
                    ->where('pair', '=', $temp_journal->pair)
                    ->get();

                foreach ($journals as $journal):
                    if ($journal->d_or_c == 'Debit'):
                        // DEBIT
                        $new_journal = TempJournal::findOrFail($journal->id);

                        // Cari COA ID berdasarkan metode pembayaran
                        if ($new_hq_payment->payment_type_id == 1 || $new_hq_payment->payment_type_id == 6 || $new_hq_payment->payment_type_id == 7):
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where(function ($c) {
                                    $c->where('coa_number', '=', '1-1100')
                                        ->orWhere('coa_name', 'ilike', '%kas%');
                                })
                                ->first();
                        else:
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where('id', '=', $new_hq_payment->hqcreditpayment_bank->coa_id)
                                ->first();
                        endif;

                        $new_journal->coa_id = $coa->id;
                        $new_journal->trans_date = $new_hq_payment->payment_date;
                        $new_journal->debit = $new_hq_payment->total;
                        $new_journal->credit = 0;

                        if ($new_hq_payment->payment_type_id == 1 || $new_hq_payment->payment_type_id == 6 || $new_hq_payment->payment_type_id == 7):
                            $new_journal->information = 'Kas ' . $new_hq_payment->payment_number;
                        else:
                            $new_journal->information = $coa->coa_name . ' ' . $new_hq_payment->payment_number;
                        endif;

                        $new_journal->save();
                    elseif ($journal->d_or_c == 'Credit'):
                        // CREDIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->trans_date = $new_hq_payment->payment_date;
                        $new_journal->debit = 0;
                        $new_journal->credit = $new_hq_payment->total;
                        $new_journal->save();
                    endif;
                endforeach;
            else:
                // Buat Jurnal PEMBALIK
                $hq_payment->total = $hq_payment->total * (-1);
                $this->add_hq_credit_payment($hq_payment, true);

                // Buat Jurnal Pengganti
                $this->add_hq_credit_payment($new_hq_payment, false);
            endif;
        else:
            // Buat Jurnal Baru
            $this->add_hq_credit_payment($new_hq_payment, false);
        endif;
    }

    // CONSIGNMENT ORDER
    // Buat Piutang PRODUK
    public function add_co_credit(ConsignmentOrder $co, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Piutang (DEBIT)
        $coa_piutang = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '1-1211')
                    ->orWhere('coa_name', 'ilike', '%piutang produk investor%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $co->co_club->jubilant_id;
        $temp_journal->coa_id = $coa_piutang->id;
        $temp_journal->trans_date = $co->order_date;
        $temp_journal->debit = $co->total;
        $temp_journal->invoice_number = $co->order_number;
        $temp_journal->information = 'Piutang ' . $co->order_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();

        // Penjualan (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-4000')
                    ->orWhere('coa_name', 'ilike', '%penjualan produk%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $co->co_club->jubilant_id;
        $temp_journal->coa_id = $coa_penjualan->id;
        $temp_journal->trans_date = $co->order_date;
        $temp_journal->credit = $co->total;
        $temp_journal->invoice_number = $co->order_number;
        $temp_journal->information = 'Penjualan ' . $co->order_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();
    }

    // Edit Piutang PRODUK
    public function edit_co_credit(ConsignmentOrder $co, ConsignmentOrder $new_co)
    {
        // Cek jika jurnal titipan sudah APPROVED
        $temp_journal = TempJournal::where('d_or_c', '=', 'Debit')
            ->where('invoice_number', '=', $co->order_number)
            ->orderBy('id', 'desc')
            ->first();

        if (!empty($temp_journal)):
            // IF NOT APPROVED
            if (!$temp_journal->approved):
                $journals = TempJournal::where('invoice_number', '=', $co->order_number)
                    ->where('approved', '=', false)
                    ->where('pair', '=', $temp_journal->pair)
                    ->get();

                // GET COA Piutang
                $coa_piutang = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                    ->where(function ($coa) {
                        $coa->where('coa_number', '=', '1-1211')
                            ->orWhere('coa_name', 'ilike', '%piutang produk investor%');
                    })
                    ->first();

                foreach ($journals as $journal):
                    if ($journal->d_or_c == 'Debit'):
                        // DEBIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->jubilant_id = $new_co->co_club->jubilant_id;
                        $new_journal->coa_id = $coa_piutang->id;
                        $new_journal->trans_date = $new_co->order_date;
                        $new_journal->debit = $new_co->total;
                        $new_journal->credit = 0;
                        $new_journal->save();
                    elseif ($journal->d_or_c == 'Credit'):
                        // CREDIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->jubilant_id = $new_co->co_club->jubilant_id;
                        $new_journal->trans_date = $new_co->order_date;
                        $new_journal->debit = 0;
                        $new_journal->credit = $new_co->total;
                        $new_journal->save();
                    endif;
                endforeach;
            else:
                // Buat Jurnal PEMBALIK
                $co->total = $co->total * (-1);
                $this->add_co_credit($co, true);

                // Buat Jurnal Pengganti
                $this->add_co_credit($new_co, false);
            endif;
        else:
            // Buat Jurnal Baru
            $this->add_co_credit($new_co, false);
        endif;
    }

    // Pembayaran Piutang PRODUK
    public function add_co_credit_payment(ConsignmentOrderBillPayment $co_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Bayar Piutang (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $co_payment->cobillpayment_cobill->jubilant_id;

        // Cari COA ID berdasarkan metode pembayaran
        if ($co_payment->payment_type_id == 1 || $co_payment->payment_type_id == 6 || $co_payment->payment_type_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where('id', '=', $co_payment->cobillpayment_bank->coa_id)
                ->first();
        endif;

        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $co_payment->payment_date;
        $temp_journal->debit = $co_payment->total;
        $temp_journal->invoice_number = $co_payment->payment_number;

        if ($co_payment->payment_type_id == 1 || $co_payment->payment_type_id == 6 || $co_payment->payment_type_id == 7):
            $temp_journal->information = 'Kas ' . $co_payment->payment_number;
        else:
            $temp_journal->information = $coa->coa_name . ' ' . $co_payment->payment_number;
        endif;

        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();

        // Piutang (CREDIT)
        // GET COA Piutang
        $coa_piutang = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '1-1211')
                    ->orWhere('coa_name', 'ilike', '%piutang produk investor%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $co_payment->cobillpayment_cobill->jubilant_id;
        $temp_journal->coa_id = $coa_piutang->id;
        $temp_journal->trans_date = $co_payment->payment_date;
        $temp_journal->credit = $co_payment->total;
        $temp_journal->invoice_number = $co_payment->payment_number;
        $temp_journal->information = 'Piutang ' . $co_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->save();
    }

    // Edit Pembayaran Piutang PRODUK
    public function edit_co_credit_payment(ConsignmentOrderBillPayment $cp, ConsignmentOrderBillPayment $new_cp)
    {
        // Cek jika jurnal titipan sudah APPROVED
        $temp_journal = TempJournal::where('d_or_c', '=', 'Debit')
            ->where('invoice_number', '=', $cp->payment_number)
            ->orderBy('id', 'desc')
            ->first();

        if (!empty($temp_journal)):
            // IF NOT APPROVED
            if (!$temp_journal->approved):
                $journals = TempJournal::where('invoice_number', '=', $cp->payment_number)
                    ->where('approved', '=', false)
                    ->where('pair', '=', $temp_journal->pair)
                    ->get();

                foreach ($journals as $journal):
                    if ($journal->d_or_c == 'Debit'):
                        // DEBIT
                        $new_journal = TempJournal::findOrFail($journal->id);

                        // Cari COA ID berdasarkan metode pembayaran
                        if ($new_cp->payment_type_id == 1 || $new_cp->payment_type_id == 6 || $new_cp->payment_type_id == 7):
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where(function ($c) {
                                    $c->where('coa_number', '=', '1-1100')
                                        ->orWhere('coa_name', 'ilike', '%kas%');
                                })
                                ->first();
                        else:
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where('id', '=', $new_cp->cobillpayment_bank->coa_id)
                                ->first();
                        endif;

                        $new_journal->coa_id = $coa->id;
                        $new_journal->trans_date = $new_cp->payment_date;
                        $new_journal->debit = $new_cp->total;
                        $new_journal->credit = 0;

                        if ($new_cp->payment_type_id == 1 || $new_cp->payment_type_id == 6 || $new_cp->payment_type_id == 7):
                            $new_journal->information = 'Kas ' . $new_cp->payment_number;
                        else:
                            $new_journal->information = $coa->coa_name . ' ' . $new_cp->payment_number;
                        endif;

                        $new_journal->save();
                    elseif ($journal->d_or_c == 'Credit'):
                        // CREDIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->trans_date = $new_cp->payment_date;
                        $new_journal->debit = 0;
                        $new_journal->credit = $new_cp->total;
                        $new_journal->save();
                    endif;
                endforeach;
            else:
                // Buat Jurnal PEMBALIK
                $cp->total = $cp->total * (-1);
                $this->add_co_credit_payment($cp, true);

                // Buat Jurnal Pengganti
                $this->add_co_credit_payment($new_cp, false);
            endif;
        else:
            // Buat Jurnal Baru
            $this->add_co_credit_payment($new_cp, false);
        endif;
    }

    //Penjualan Produk dari kasir
    public function product_payment($product_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Cari COA ID berdasarkan metode pembayaran
        if ($product_payment->payment_method_id == 1 || $product_payment->payment_method_id == 6 || $product_payment->payment_method_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $product_payment->productpayment_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', $product_payment->productpayment_club->jubilant_id)
                ->where('id', '=', $product_payment->productpayment_bank->coa_id)
                ->first();
        endif;

        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $product_payment->productpayment_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $product_payment->payment_date;
        $temp_journal->debit = $product_payment->total;
        $temp_journal->invoice_number = $product_payment->payment_number;
        $temp_journal->information = !($product_payment->is_reward) ? 'Terima penjualan product ' . $product_payment->payment_number : 'Terima penjualan product ' . $product_payment->payment_number . ' (Hadiah)';
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $product_payment->club_id;
        $temp_journal->save();

        // (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', $product_payment->productpayment_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-4000')
                    ->orWhere('coa_name', 'ilike', '%penjualan produk%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $product_payment->productpayment_club->jubilant_id;
        $temp_journal->coa_id = $coa_penjualan->id;
        $temp_journal->trans_date = $product_payment->payment_date;
        $temp_journal->credit = $product_payment->total;
        $temp_journal->invoice_number = $product_payment->payment_number;
        $temp_journal->information = !($product_payment->is_reward) ? 'Penjualan produk ' . $product_payment->payment_number : 'Penjualan produk ' . $product_payment->payment_number . ' (Hadiah)';
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $product_payment->club_id;
        $temp_journal->save();


        // Jonviter DEV
        // $product_payment->payment_date = $product_payment->payment_date->toDateTimeString(); 
        // $paymentData = $product_payment->toArray();
        // $apiClient = new ApiClientService();
        // $mobile_curves_api = $apiClient->post("/mobile-curves/payment-product/{$product_payment->member_id}", $paymentData);
        // Jonviter DEV
    }

    //Penjualan Loker dari kasir
    public function locker_payment($locker_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();
        $user = User::findOrFail($locker_payment->staff_id);

        // Cari COA ID berdasarkan metode pembayaran
        if ($locker_payment->paymentmethod_id == 1 || $locker_payment->paymentmethod_id == 6 || $locker_payment->paymentmethod_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', $user->user_club->jubilant_id)
                ->where('id', '=', $locker_payment->lockerpayment_bank->coa_id)
                ->first();
        endif;


        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $locker_payment->payment_date;
        $temp_journal->debit = $locker_payment->price;
        $temp_journal->invoice_number = $locker_payment->payment_number;
        $temp_journal->information = 'Terima penjualan sewa loker ' . $locker_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
        $temp_journal->save();

        // (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', $user->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-5000')
                    ->orWhere('coa_name', 'ilike', '%penjualan sewa loker%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa_penjualan->id;
        $temp_journal->trans_date = $locker_payment->payment_date;
        $temp_journal->credit = $locker_payment->price;
        $temp_journal->invoice_number = $locker_payment->payment_number;
        $temp_journal->information = 'Penjualan sewa loker ' . $locker_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $locker_payment->lockerpayment_locker->club_id;
        $temp_journal->save();
    }

    public function promo_membership_payment($promo_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Cari COA ID berdasarkan metode pembayaran
        if ($promo_payment->payment_type_id == 1 || $promo_payment->payment_type_id == 6 || $promo_payment->payment_type_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $promo_payment->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', $promo_payment->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->jubilant_id)
                ->where('id', '=', $promo_payment->gmpayment_bank->coa_id)
                ->first();
        endif;

        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $promo_payment->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $promo_payment->payment_date;
        $temp_journal->debit = $promo_payment->amount_paid;
        $temp_journal->invoice_number = $promo_payment->payment_number;
        $temp_journal->information = 'Terima penjualan promo ' . $promo_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $promo_payment->gmpayment_gmmember->gmmember_guest->club_id;
        $temp_journal->save();

        // (CREDIT)
        $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', $promo_payment->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-1000')
                    ->orWhere('coa_name', 'ilike', '%penjualan jasa%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $promo_payment->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->jubilant_id;
        $temp_journal->coa_id = $coa_penjualan->id;
        $temp_journal->trans_date = $promo_payment->payment_date;
        $temp_journal->credit = $promo_payment->amount_paid;
        $temp_journal->invoice_number = $promo_payment->payment_number;
        $temp_journal->information = 'Penjualan jasa ' . $promo_payment->payment_number . ' (Promo)';
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $promo_payment->gmpayment_gmmember->gmmember_guest->club_id;
        $temp_journal->save();
    }

    // Penjualan Membership => Hanya Service Fee
    public function membership_payment(MemberPayment $member_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Cari COA ID berdasarkan metode pembayaran
        if ($member_payment->payment_method_id == 1 || $member_payment->payment_method_id == 6 || $member_payment->payment_method_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where('id', '=', $member_payment->memberpayment_bank->coa_id)
                ->first();
        endif;

        // GET COA Credit
        $coa_piutang = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-1000')
                    ->orWhere('coa_name', 'ilike', '%penjualan jasa%');
            })
            ->first();

        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->debit = $member_payment->sf_amount - $member_payment->dc_amount;
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Terima biaya layanan keanggotaan ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->club_id;
        $temp_journal->save();

        // (CREDIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa_piutang->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->credit = $member_payment->sf_amount - $member_payment->dc_amount;
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Penjualan jasa ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->club_id;
        $temp_journal->save();

        // Jika pembayaran yang dilakukan lebih dari biaya layanan
        if (($member_payment->amount_paid - ($member_payment->sf_amount - $member_payment->dc_amount)) > 0):
            $this->membership_monthly_payment($member_payment, false);
        endif;
    }

    public function membership_monthly_payment(MemberPayment $member_payment, $approved)
    {
        // Get PAIR Number
        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // Cari COA ID berdasarkan metode pembayaran
        if ($member_payment->payment_method_id == 1 || $member_payment->payment_method_id == 6 || $member_payment->payment_method_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
                ->where('id', '=', $member_payment->memberpayment_bank->coa_id)
                ->first();
        endif;

        // (DEBIT)
        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->debit = $member_payment->amount_paid - ($member_payment->sf_amount - $member_payment->dc_amount);
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Terima cicilan keanggotaan ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->club_id;
        $temp_journal->save();

        // (CREDIT)
        // GET COA Credit
        $coa_piutang = ChartOfAccount::where('jubilant_id', '=', $member_payment->memberpayment_user->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '1-1260')
                    ->orWhere('coa_name', 'ilike', '%pendapatan jasa di terima di muka%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa_piutang->id;
        $temp_journal->trans_date = $member_payment->payment_date;
        $temp_journal->credit = $member_payment->amount_paid - ($member_payment->sf_amount - $member_payment->dc_amount);
        $temp_journal->invoice_number = $member_payment->payment_number;
        $temp_journal->information = 'Pendapatan jasa diterima di muka ' . $member_payment->payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = $member_payment->memberpayment_user->club_id;
        $temp_journal->save();
    }

    public function trf_amortization(User $user, $total_payment, $approved)
    {
        $member_payment = MemberPayment::where('user_id', '=', $user->id)
            ->where('payment_number', '<>', null)
            ->orderBy('payment_date', 'desc')->first();

//        // Check payment method
//        if ($member_payment->payment_method_id == 1) {
//            $coanumber = '1-1100';
//        } else {
//            $coanumber = $member_payment->memberpayment_bank->bank_chartofaccount->coa_number;
//        }

        // Get PAIR Number
        $amor = AutoNumber::where('name', '=', 'TrfAmortization')->first();
        $new_auto_number = AutoNumber::findOrFail($amor->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        $month_roman = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $payment_number = sprintf('%08d', $amor->current_number) . '/TRF/CIND/' . sprintf('%03d', Auth::user()->club_id) . '/' . $month_roman[Carbon::now()->format('n')] . '/' . Carbon::now()->format('Y');

        $pair = AutoNumber::where('name', '=', 'TempJournal')->first();
        $new_auto_number = AutoNumber::findOrFail($pair->id);
        $new_auto_number->current_number = $new_auto_number->current_number + 1;
        $new_auto_number->save();

        // (DEBIT)
        // GET COA Debit
        $coa_retur = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
            ->where(function ($coa) {
                $coa->where('coa_number', '=', '4-3000')
                    ->orWhere('coa_name', 'ilike', '%retur penjualan%');
            })
            ->first();

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa_retur->id;
        $temp_journal->trans_date = Carbon::today();
        $temp_journal->debit = $total_payment;
        $temp_journal->invoice_number = $payment_number;
        $temp_journal->information = 'Retur Penjualan ' . $payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Debit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = Auth::user()->club_id;
        $temp_journal->save();

        // (CREDIT)
        // GET COA Credit
        if ($member_payment->payment_method_id == 1 || $member_payment->payment_method_id == 6 || $member_payment->payment_method_id == 7):
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where(function ($c) {
                    $c->where('coa_number', '=', '1-1100')
                        ->orWhere('coa_name', 'ilike', '%kas%');
                })
                ->first();
        else:
            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                ->where('id', '=', $member_payment->memberpayment_bank->coa_id)
                ->first();
        endif;

        $temp_journal = new TempJournal;
        $temp_journal->jubilant_id = $member_payment->memberpayment_user->user_club->jubilant_id;
        $temp_journal->coa_id = $coa->id;
        $temp_journal->trans_date = Carbon::today();
        $temp_journal->credit = $total_payment;
        $temp_journal->invoice_number = $payment_number;
        $temp_journal->information = 'Retur Penjualan ' . $payment_number;
        $temp_journal->approved = $approved;
        $temp_journal->d_or_c = 'Credit';
        $temp_journal->pair = $pair->current_number;
        $temp_journal->club_id = Auth::user()->club_id;
        $temp_journal->save();
    }

    public function edit_invoice_product_payment(ProductPayment $temp_product, ProductPayment $product_payment) {
        $temp_journal = TempJournal::where('invoice_number', '=', $product_payment->payment_number)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!empty($temp_journal)) {
            //If Not Approved
            if (!$temp_journal->approved) {
                $journals = TempJournal::where('invoice_number', '=', $product_payment->payment_number)
                    ->where('approved', '=', false)
                    ->where('pair', '=', $temp_journal->pair)
                    ->get();

                foreach ($journals as $journal) {
                    if ($journal->d_or_c == 'Debit') {
                        if ($product_payment->payment_method_id == 1 || $product_payment->payment_method_id == 6 || $product_payment->payment_method_id == 7) {
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where(function ($c) {
                                    $c->where('coa_number', '=', '1-1100')
                                        ->orWhere('coa_name', 'ilike', '%kas%');
                                })
                                ->first();
                        } else {
                            $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                                ->where('id', '=', $product_payment->productpayment_bank->coa_id)
                                ->first();
                        }
                        // DEBIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->coa_id = $coa->id;
                        $new_journal->trans_date = $product_payment->payment_date;
                        $new_journal->debit = $product_payment->total;
                        $new_journal->credit = 0;
                        $new_journal->information = !($product_payment->is_reward) ? 'Terima penjualan product ' . $product_payment->payment_number : 'Terima penjualan product ' . $product_payment->payment_number . ' (Hadiah)';
                        $new_journal->save();
                    } elseif ($journal->d_or_c == 'Credit') {
                        $coa = ChartOfAccount::where('jubilant_id', '=', $product_payment->productpayment_club->jubilant_id)
                            ->where(function ($coa) {
                                $coa->where('coa_number', '=', '4-4000')
                                    ->orWhere('coa_name', 'ilike', '%penjualan produk%');
                            })
                            ->first();
                        // CREDIT
                        $new_journal = TempJournal::findOrFail($journal->id);
                        $new_journal->coa_id = $coa->id;
                        $new_journal->trans_date = $product_payment->payment_date;
                        $new_journal->debit = 0;
                        $new_journal->credit = $product_payment->total;
                        $new_journal->information = !($product_payment->is_reward) ? 'Penjualan produk ' . $product_payment->payment_number : 'Penjualan produk ' . $product_payment->payment_number . ' (Hadiah)';
                        $new_journal->save();
                    }
                }
            } else {
                // JOURNAL PEMBALIK
                // Write The Accounting Journal V2
                // Get PAIR Number
                $pair = AutoNumber::where('name', '=', 'Journal')->first();
                $new_auto_number = AutoNumber::findOrFail($pair->id);
                $new_auto_number->current_number = $new_auto_number->current_number + 1;
                $new_auto_number->save();

                // DEBIT
                if ($product_payment->payment_method_id == 1 || $product_payment->payment_method_id == 6 || $product_payment->payment_method_id == 7) {
                    $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                        ->where(function ($c) {
                            $c->where('coa_number', '=', '1-1100')
                                ->orWhere('coa_name', 'ilike', '%kas%');
                        })
                        ->first();
                } else {
                    $coa = ChartOfAccount::where('jubilant_id', '=', Auth::user()->user_club->jubilant_id)
                        ->where('id', '=', $temp_product->productpayment_bank->coa_id)
                        ->first();
                }

                $acc_d = new AccountingJournalV2;
                $acc_d->jubilant_id = $temp_product->productpayment_club->jubilant_id;
                $acc_d->coa_id = $coa->id;
                $acc_d->trans_date = $temp_product->payment_date;
                $acc_d->bank_date = $temp_product->payment_date;
                $acc_d->debit = $temp_product->total * (-1);
                $acc_d->credit = 0;
                $acc_d->invoice_number = $temp_product->payment_number;
                $acc_d->information = !($temp_product->is_reward) ? 'Terima penjualan product ' . $temp_product->payment_number : 'Terima penjualan product ' . $temp_product->payment_number . ' (Hadiah)';
                $acc_d->d_or_c = 'Debit';
                $acc_d->club_id = $temp_product->club_id;
                $acc_d->pair = $pair->current_number;
                $acc_d->save();

                // (CREDIT)
                $coa_penjualan = ChartOfAccount::where('jubilant_id', '=', $product_payment->productpayment_club->jubilant_id)
                    ->where(function ($coa) {
                        $coa->where('coa_number', '=', '4-4000')
                            ->orWhere('coa_name', 'ilike', '%penjualan produk%');
                    })
                    ->first();

                $acc_c = new AccountingJournalV2;
                $acc_c->jubilant_id = $temp_product->productpayment_club->jubilant_id;
                $acc_c->coa_id = $coa_penjualan->id;
                $acc_c->trans_date = $temp_product->payment_date;
                $acc_c->bank_date = $temp_product->payment_date;
                $acc_c->debit = 0;
                $acc_c->credit = $temp_product->total * (-1);
                $acc_c->invoice_number = $temp_product->payment_number;
                $acc_c->information = !($product_payment->is_reward) ? 'Penjualan produk ' . $product_payment->payment_number : 'Penjualan produk ' . $product_payment->payment_number . ' (Hadiah)';
                $acc_c->d_or_c = 'Credit';
                $acc_c->club_id = $temp_product->club_id;
                $acc_c->pair = $pair->current_number;
                $acc_c->save();

                // Cek jika perubahan diperuntukkan bagi bulan yang telah lalu
                if ((Carbon::parse($acc_d->trans_date)->format('m-Y') != Carbon::today()->format('m-Y')) && (Carbon::parse($acc_d->trans_date) < Carbon::today())):
                    // Update Monthly Ending Balance
                    app('App\Http\Controllers\AccountingTemporaryJournalController')->checkMonthlyEndingBalance($acc_d, $acc_c);
                endif;

                // JOURNAL PENGGANTI
                $this->product_payment($product_payment, false);
            }
        }
    }
}
