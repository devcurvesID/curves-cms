<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Payment\MemberDebtPayment;
use App\Model\Payment\MemberPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookEzypayController extends Controller
{
    private const INVOICE_EVENTS = [
        'INVOICE_CREATED' => 'UNPAID',
        'INVOICE_PAST_DUE' => 'PAST_DUE',
        'INVOICE_PAID' => 'PAID',
    ];

    private const SUBSCRIPTION_EVENTS = [
        'SUBSCRIPTION_CREATE' => 'CREATED',
        'SUBSCRIPTION_ACTIVATE' => 'ACTIVE',
        'SUBSCRIPTION_PAYMENT_REACTIVATE' => 'ACTIVE',
        'SUBSCRIPTION_PAYMENT_STOPPED' => 'PAYMENT_STOPPED',
        'SUBSCRIPTION_CANCEL' => 'CANCELLED',
        'SUBSCRIPTION_COMPLETE' => 'COMPLETE',
    ];

    private const BATCH_EVENTS = [
        'INVOICE_BATCH_CREATED' => 'SUBMITTED',
        'INVOICE_BATCH_PROCESSING' => 'PROCESSING',
        'INVOICE_BATCH_SUCCESS' => 'SUCCESS',
    ];

    private const BATCH_PROGRESS = ['SUBMITTED' => 1, 'PROCESSING' => 2, 'SUCCESS' => 3];

    public function update_member_payment(Request $request)
    {
        $payload = $request->validate(['eventType' => 'required|string|max:80']);
        $event = strtoupper($payload['eventType']);
        if (isset(self::BATCH_EVENTS[$event]) || $event === 'INVOICE_BATCH_INVOICE_FAILED') {
            $updated = $this->handleBatchEvent($request, $event);

            return response()->json(['status' => $updated ? 'updated' : 'ignored'], 200);
        }

        $invoiceStatus = self::INVOICE_EVENTS[$event] ?? null;
        $subscriptionStatus = self::SUBSCRIPTION_EVENTS[$event] ?? null;
        if (!$invoiceStatus && !$subscriptionStatus) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $payload = $request->validate([
            'createdOn' => $subscriptionStatus ? 'required|date' : 'nullable|date',
            'data' => 'required|array',
            'data.customerId' => 'required|string|max:255',
            'data.id' => $invoiceStatus ? 'required|string|max:255' : 'nullable|string|max:255',
            'data.externalInvoiceId' => 'nullable|string|max:255',
            'data.checkoutId' => 'nullable|string|max:255',
            'data.subscriptionId' => 'nullable|string|max:255',
            'data.batchReference' => 'nullable|string|max:255',
            'data.status' => 'nullable|string|max:32',
        ]);
        $data = $payload['data'];
        if ($invoiceStatus === 'PAID') {
            $paid = $request->validate([
                'data.amount.value' => 'required|numeric|gt:0|max:2147483647',
                'data.amount.currency' => 'required|in:IDR',
            ]);
            $data['amount'] = $paid['data']['amount'];
            abort_if(isset($data['status']) && strtoupper($data['status']) !== 'PAID', 422, 'Status invoice tidak sesuai dengan event.');
        }

        $updated = DB::transaction(function () use ($data, $payload, $invoiceStatus, $subscriptionStatus) {
            if ($subscriptionStatus) {
                return $this->updateSubscription($data, $subscriptionStatus, $payload['createdOn']);
            }

            return $this->updateInvoice($data, $invoiceStatus);
        });

        return response()->json(['status' => $updated ? 'updated' : 'ignored'], 200);
    }

    private function handleBatchEvent(Request $request, string $event): bool
    {
        $failed = $event === 'INVOICE_BATCH_INVOICE_FAILED';
        $payload = $request->validate([
            'data' => 'required|array',
            'data.id' => $failed ? 'nullable|string|max:255' : 'required|string|max:255',
            'data.invoiceBatchId' => $failed ? 'required|string|max:255' : 'nullable|string|max:255',
            'data.externalInvoiceId' => $failed ? 'required|string|max:255' : 'nullable|string|max:255',
            'data.batchReference' => 'nullable|string|max:255',
            'data.failedReason' => 'nullable|string|max:2000',
            'data.status' => 'nullable|string|max:32',
        ]);
        $data = $payload['data'];
        $status = $failed ? 'FAILED' : self::BATCH_EVENTS[$event];
        abort_if(isset($data['status']) && strtoupper($data['status']) !== $status, 422,
            'Status batch tidak sesuai dengan event.');

        return DB::transaction(function () use ($data, $failed, $status) {
            // The existing column holds the invoice batch ID for debt payments.
            $debts = MemberDebtPayment::where('ezypay_subscription_id', $failed ? $data['invoiceBatchId'] : $data['id']);
            if ($failed) {
                $debt = $this->lockedRecord($debts->where('payment_number', $data['externalInvoiceId']));
                abort_if($debt === null, 404, 'Cicilan invoice batch belum ditemukan.');
                if ($debt->ezypay_invoice_id || $debt->ezypay_invoice_status === 'PAID') {
                    return false;
                }
                $debt->ezypay_batch_item_status = 'FAILED';
                $debt->ezypay_batch_failure_reason = $data['failedReason'] ?? null;

                return $this->saveIfChanged($debt);
            }

            $records = $debts->orderBy('id')->lockForUpdate()->get();
            abort_if($records->isEmpty(), 404, 'Invoice batch belum ditemukan.');
            $reference = $data['batchReference'] ?? null;
            $updated = false;
            foreach ($records as $debt) {
                abort_if($reference && $debt->ezypay_batch_reference && $debt->ezypay_batch_reference !== $reference,
                    409, 'Referensi batch invoice berbeda.');
                if ((self::BATCH_PROGRESS[$debt->ezypay_batch_status] ?? 0) < self::BATCH_PROGRESS[$status]) {
                    $debt->ezypay_batch_status = $status;
                }
                if ($reference && !$debt->ezypay_batch_reference) {
                    $debt->ezypay_batch_reference = $reference;
                }
                $updated = $this->saveIfChanged($debt) || $updated;
            }

            return $updated;
        });
    }

    private function memberPayments(string $customerId): Builder
    {
        return MemberPayment::whereHas('memberpayment_user', function (Builder $query) use ($customerId) {
            $query->where('customer_id_ezypay', $customerId);
        });
    }

    private function lockedRecord(Builder $query)
    {
        $matches = $query->limit(2)->lockForUpdate()->get();
        abort_if($matches->count() > 1, 409, 'Referensi pembayaran EZYPAY tidak unik.');

        return $matches->first();
    }

    private function updateSubscription(array $data, string $status, string $createdOn): bool
    {
        $subscriptionId = $data['subscriptionId'] ?? $data['id'] ?? null;
        abort_unless($subscriptionId, 422, 'Subscription ID wajib diisi.');
        $payment = $this->lockedRecord($this->memberPayments($data['customerId'])
            ->where('ezypay_subscription_id', $subscriptionId));
        abort_if($payment === null, 404, 'Pembayaran subscription belum ditemukan.');

        $eventAt = Carbon::parse($createdOn, 'UTC')->utc();
        if ($payment->ezypay_subscription_event_at
            && Carbon::parse($payment->ezypay_subscription_event_at, 'UTC')->gte($eventAt)) {
            return false;
        }

        $payment->ezypay_subscription_status = $status;
        $payment->ezypay_subscription_event_at = $eventAt->format('Y-m-d H:i:s.u');

        return $this->saveIfChanged($payment);
    }

    private function updateInvoice(array $data, string $status): bool
    {
        $reference = $data['externalInvoiceId'] ?? null;
        $debt = $this->lockedRecord(MemberDebtPayment::whereHas('memberdebtpayment_memberpayment.memberpayment_user', function (Builder $query) use ($data) {
            $query->where('customer_id_ezypay', $data['customerId']);
        })->where(function (Builder $query) use ($data, $reference) {
            $query->where('ezypay_invoice_id', $data['id']);
            if ($reference) {
                $query->orWhere(function (Builder $byReference) use ($reference) {
                    $byReference->where('payment_number', $reference)
                        ->where(function (Builder $batch) {
                            $batch->whereNotNull('ezypay_subscription_id')->orWhereNotNull('ezypay_batch_reference');
                        });
                });
            }
        }));
        if ($debt) {
            return $this->updateDebtInvoice($debt, $data, $status);
        }

        $payments = $this->memberPayments($data['customerId']);
        $payment = $this->lockedRecord((clone $payments)->where(function (Builder $query) use ($data, $reference) {
            $query->where('ezypay_invoice_id', $data['id']);
            if (!empty($data['checkoutId'])) {
                $query->orWhere('ezypay_checkout_id', $data['checkoutId']);
            }
            if ($reference) {
                $query->orWhere(function (Builder $byReference) use ($reference) {
                    $byReference->where('payment_number', $reference)->where(function (Builder $known) {
                        $known->whereNotNull('ezypay_invoice_id')->orWhereNotNull('ezypay_checkout_id')
                            ->orWhereNotNull('ezypay_subscription_id')->orWhereNotNull('ezypay_checkout_amount');
                    });
                });
            }
        }));
        if (!$payment && !empty($data['subscriptionId'])) {
            $payment = $this->lockedRecord($payments->where('ezypay_subscription_id', $data['subscriptionId']));
            if ($payment && ($payment->ezypay_invoice_id || ($reference && $reference !== $payment->payment_number))) {
                // A later recurring invoice must not overwrite the initial membership payment.
                return false;
            }
        }
        abort_if($payment === null, 404, 'Pembayaran invoice belum ditemukan.');
        foreach (['checkoutId' => 'ezypay_checkout_id', 'subscriptionId' => 'ezypay_subscription_id'] as $key => $column) {
            abort_if(!empty($data[$key]) && $payment->$column && $payment->$column !== $data[$key],
                409, 'Referensi invoice berbeda dengan pembayaran lokal.');
        }

        if (!$this->applyInvoiceStatus($payment, $data['id'], $status)) {
            return false;
        }
        if ($status === 'PAID') {
            $remaining = DB::table('ezypay_membership_purchases')->where('member_payment_id', $payment->id)
                ->value('installment_remaining');
            $principal = (int) ($payment->ezypay_checkout_amount
                ?? ($remaining !== null ? (int) $payment->amount_due - (int) $remaining : $payment->amount_paid));
            if ($principal <= 0) {
                $principal = min((int) $payment->amount_due, (int) $data['amount']['value']);
            }
            $this->verifyPaidAmount($data, $principal);
            $this->setPaymentBalance($payment, max((int) $payment->amount_paid, $principal));
        }

        return $this->saveIfChanged($payment);
    }

    private function updateDebtInvoice(MemberDebtPayment $debt, array $data, string $status): bool
    {
        abort_if(!empty($data['batchReference']) && $debt->ezypay_batch_reference
            && $debt->ezypay_batch_reference !== $data['batchReference'], 409, 'Referensi batch invoice berbeda.');
        if (!$this->applyInvoiceStatus($debt, $data['id'], $status)) {
            return false;
        }
        if ($debt->ezypay_subscription_id || $debt->ezypay_batch_reference) {
            $debt->ezypay_batch_item_status = 'SUCCESS';
            $debt->ezypay_batch_failure_reason = null;
        }
        if ($status === 'PAID') {
            $principal = (int) $debt->amount_due;
            $this->verifyPaidAmount($data, $principal);
            $debt->amount_paid = $principal;
            $debt->rest_of_bill = 0;

            $payment = MemberPayment::lockForUpdate()->findOrFail($debt->member_payment_id);
            $this->setPaymentBalance($payment, max((int) $payment->amount_paid, $principal));
            $this->saveIfChanged($payment);
        }

        return $this->saveIfChanged($debt);
    }

    private function applyInvoiceStatus($record, string $invoiceId, string $status): bool
    {
        abort_if($record->ezypay_invoice_id && $record->ezypay_invoice_id !== $invoiceId, 409,
            'Invoice berbeda sudah terhubung ke pembayaran ini.');
        if ($record->ezypay_invoice_status === 'PAID'
            || ($record->ezypay_invoice_status === 'PAST_DUE' && $status === 'UNPAID')) {
            return false;
        }

        $record->ezypay_invoice_id = $invoiceId;
        $record->ezypay_invoice_status = $status;

        return true;
    }

    private function verifyPaidAmount(array $data, int $principal): void
    {
        abort_if($principal <= 0 || (float) $data['amount']['value'] < $principal, 422,
            'Nominal invoice lebih kecil dari nominal pembayaran yang tercatat.');
    }

    private function setPaymentBalance(MemberPayment $payment, int $paid): void
    {
        $payment->amount_paid = min((int) $payment->amount_due, $paid);
        $payment->rest_of_bill = (int) $payment->amount_due - $payment->amount_paid;
        $payment->payment_status = strcasecmp((string) $payment->payment_status, 'Halfunpaid') === 0
            || $payment->rest_of_bill > 0 ? 'Halfpaid' : 'Paid';
    }

    private function saveIfChanged($record): bool
    {
        if (!$record->isDirty()) {
            return false;
        }
        $record->save();

        return true;
    }
}
