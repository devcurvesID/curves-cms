<?php

namespace App\Services;

use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EzypayInstallmentService
{
    public function validate($total, $pay, $rows): array
    {
        $data = ['total' => $total, 'pay' => $pay, 'installments' => $rows];
        Validator::make($data, [
            'total' => 'required|numeric|min:0',
            'pay' => 'required|numeric|min:0',
            'installments' => 'present|array|max:120',
            'installments.*.amount' => 'required|numeric|gt:0',
            'installments.*.date' => 'required|date_format:Y-m-d|after_or_equal:' . Carbon::today('Asia/Jakarta')->format('Y-m-d'),
        ])->validate();
        if (!is_finite((float) $total) || !is_finite((float) $pay)) {
            throw ValidationException::withMessages(['installments' => 'Nominal tagihan tidak valid.']);
        }
        $remaining = (int) round((float) $total * 100) - (int) round((float) $pay * 100);
        $allocated = 0;
        foreach ($rows as $index => $row) {
            $amount = (float) $row['amount'];
            if (!is_finite($amount) || $amount !== floor($amount) || $amount > 2147483647) {
                throw ValidationException::withMessages(['installments' => 'Nominal cicilan harus berupa rupiah bulat dalam batas yang diizinkan.']);
            }
            $rows[$index]['amount'] = (int) $amount;
            $allocated += $rows[$index]['amount'] * 100;
        }
        if ($remaining < 0 || ($remaining === 0 && count($rows) > 0)
            || ($remaining > 0 && (count($rows) === 0 || $allocated !== $remaining))) {
            throw ValidationException::withMessages(['installments' => 'Total cicilan harus sama dengan Sisa Tagihan. Jika sudah lunas, tidak perlu mengatur cicilan.']);
        }
        return array_values($rows);
    }

    public function store(int $userId, MemberPayment $firstPayment, $memberStatus, array $rows): array
    {
        if (!$rows) return [];
        $payments = [];
        foreach ($rows as $row) {
            $payment = new MemberPayment;
            $payment->user_id = $userId;
            $payment->mf_amount = $row['amount'];
            $payment->tax_type_id = $firstPayment->tax_type_id;
            $payment->tax_percentage = $firstPayment->tax_percentage;
            $payment->tax_amount = round(($firstPayment->tax_percentage / (100 + $firstPayment->tax_percentage)) * $row['amount']);
            $payment->amount_due = $row['amount'];
            $payment->amount_paid = 0;
            $payment->payment_category = 'Monthly';
            $payment->payment_date = $row['date'];
            $payment->rest_of_bill = $row['amount'];
            $payment->payment_status = 'Unpaid';
            $payment->save();
            $payments[] = $payment;
        }
        $memberStatus->payment_status = 'Set';
        $memberStatus->save();
        return $payments;
    }
    public function storeAndSync($record, MemberPayment $firstPayment, $memberStatus, array $rows, string $token, string $memo): void
    {
        $payments = $this->store($firstPayment->user_id, $firstPayment, $memberStatus, $rows);
        $counter = \App\AutoNumber::where('id', 1)->lockForUpdate()->firstOrFail();
        $clubId = $firstPayment->memberpayment_user->club_id;
        $invoices = [];
        $debtIds = [];
        $roman = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        foreach ($payments as $index => $payment) {
            $date = Carbon::parse($rows[$index]['date']);
            $debt = new MemberDebtPayment;
            $debt->member_payment_id = $payment->id;
            $debt->payment_number = sprintf('%08d/PY/CIND/%03d/%s/%s', $counter->current_number++, $clubId, $roman[$date->month], $date->year);
            $debt->amount_due = $rows[$index]['amount'];
            $debt->amount_paid = 0;
            $debt->rest_of_bill = $rows[$index]['amount'];
            $debt->payment_date = $rows[$index]['date'];
            $debt->payment_method_id = $firstPayment->payment_method_id;
            $debt->bank_id = $firstPayment->bank_id;
            $debt->save();
            $debtIds[] = $debt->id;
            $invoices[] = ['externalInvoiceId' => $debt->payment_number, 'accountingCode' => 'INSTALLEMENT',
                'scheduledPaymentDate' => $rows[$index]['date'], 'amountValue' => (float) $rows[$index]['amount']];
        }
        $counter->save();
        $response = app(ApiClientService::class)->post('/mobile-curves/ezypay/invoices/batches', [
            'customerId' => $record->customer_id_ezypay, 'paymentMethodToken' => $token,
            'memo' => $memo, 'invoices' => $invoices,
        ])->throw();
        $batchId = $response->json('response.id');
        if (!is_string($batchId) || trim($batchId) === '' || strlen($batchId) > 255) {
            throw new \UnexpectedValueException('Missing or invalid EZYPAY invoice batch ID.');
        }
        $batchReference = $response->json('response.batchReference');
        MemberDebtPayment::whereIn('id', $debtIds)->update([
            'ezypay_subscription_id' => $batchId,
            'ezypay_batch_reference' => is_string($batchReference) ? $batchReference : null,
        ]);
        \Illuminate\Support\Facades\DB::table('ezypay_membership_purchases')->where('id', $record->id)->update([
            'installments_completed' => true, 'installment_sync_status' => 'synced',
            'installment_memo' => $memo,
            'installment_sync_response' => json_encode($response->json(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'updated_at' => Carbon::now(),
        ]);
    }

}
