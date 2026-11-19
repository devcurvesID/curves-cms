<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EzypayMembershipSubscriptionService
{
    private ?string $renewalKey = null;

    public function forRenewal(string $key): self
    {
        $service = clone $this;
        $service->renewalKey = $key;
        return $service;
    }

    private function purchaseQuery(int $sourceId, $connection = null)
    {
        $query = ($connection ?? DB::connection())->table('ezypay_membership_purchases');
        return $this->renewalKey === null
            ? $query->where('lead_id', $sourceId)
            : $query->where('renewal_key', $this->renewalKey)->where('renewal_user_id', $sourceId);
    }

    public function reserve($lead, $membership, bool $subscription, bool $selfPayment = false): ?Carbon
    {
        // The timestamp also identifies the request that owns this reservation.
        $reservedAt = Carbon::now()->startOfSecond();
        $attributes = [
            'membership_type_id' => $membership->id,
            'customer_id_ezypay' => $lead->customer_id_ezypay,
            'plan_id_ezypay' => $membership->plan_id_ezypay,
            'payment_type' => $selfPayment ? 'checkout' : ($subscription ? 'subscription' : 'invoice'),
            'status' => 'preparing_retryable',
            'updated_at' => $reservedAt,
        ];

        if (DB::table('ezypay_membership_purchases')->insertOrIgnore($attributes + [
            'lead_id' => $this->renewalKey === null ? $lead->id : null,
            'renewal_key' => $this->renewalKey,
            'renewal_user_id' => $this->renewalKey === null ? null : $lead->id,
            'created_at' => $reservedAt,
        ])) {
            return $reservedAt;
        }

        // Only reclaim expired reservations created with the durable submission marker.
        $claimed = $this->purchaseQuery($lead->id)
            ->where('customer_id_ezypay', $lead->customer_id_ezypay)
            ->where(function ($query) use ($reservedAt) {
                $query->where('status', 'retry_allowed')->orWhere(function ($preparing) use ($reservedAt) {
                    $preparing->where('status', 'preparing_retryable')
                        ->where('updated_at', '<=', $reservedAt->copy()->subMinutes(5));
                });
            })
            ->whereNull('external_payment_id_ezypay')
            ->whereNull('subscription_id_ezypay')
            ->whereNull('checkout_url')
            ->whereNull('payment_number')
            ->whereNull('member_payment_id')
            ->update($attributes);

        return $claimed === 1 ? $reservedAt : null;
    }

    public function markSubmitting(int $leadId, Carbon $reservedAt, string $reference): void
    {
        // Commit the marker on a separate connection so membership rollback cannot erase it.
        $connection = DB::connection(DB::getDefaultConnection() . '::write');
        $updated = $this->purchaseQuery($leadId, $connection)
            ->where('status', 'preparing_retryable')
            ->where('updated_at', $reservedAt)
            ->update([
                'status' => 'submitting',
                'payment_number' => $reference,
                'updated_at' => Carbon::now(),
            ]);

        if ($updated !== 1) {
            throw new \RuntimeException('Reservasi pembayaran EZYPAY sudah diambil oleh percobaan lain.');
        }
    }

    public function recordRolledBackAttempt(
        int $leadId,
        Carbon $reservedAt,
        ?string $reference,
        ?string $externalId
    ): bool {
        $purchase = $this->purchaseQuery($leadId);

        if ($reference !== null) {
            $purchase->where('status', 'submitting')->where('payment_number', $reference)->update([
                'status' => 'needs_review',
                'external_payment_id_ezypay' => $externalId,
                'updated_at' => Carbon::now(),
            ]);
            return false;
        }

        return $purchase->where('status', 'preparing_retryable')->where('updated_at', $reservedAt)->update([
            'status' => 'retry_allowed',
            'updated_at' => Carbon::now(),
        ]) === 1;
    }

    public function create(
        $lead,
        $membership,
        ?string $paymentMethodToken,
        string $paymentNumber,
        bool $subscription,
        bool $selfPayment,
        float $amountValue,
        ?\Closure $beforeSubmit = null
    ): array {
        if (!is_finite($amountValue) || $amountValue <= 0) {
            throw new \InvalidArgumentException('Nominal Bayar harus lebih dari nol.');
        }
        $api = app(ApiClientService::class);
        $plan = $api->get('/mobile-curves/ezypay/plans/' . rawurlencode($membership->plan_id_ezypay))
            ->throw()->json('response');
        if (!is_array($plan) || ($plan['id'] ?? null) !== $membership->plan_id_ezypay
            || ($plan['status'] ?? null) !== 'ACTIVE'
            || !is_string($plan['name'] ?? null) || trim($plan['name']) === ''
            || !is_string($plan['accountingCode'] ?? null) || trim($plan['accountingCode']) === ''
            || !is_numeric($plan['amount']['value'] ?? null) || $plan['amount']['value'] <= 0) {
            throw new \UnexpectedValueException('Informasi plan EZYPAY tidak valid atau tidak aktif.');
        }
        $payload = [
            'customerId' => $lead->customer_id_ezypay,
            'paymentMethodToken' => $paymentMethodToken,
            'amountValue' => $amountValue,
            'accountingCode' => $this->renewalKey !== null ? 'MEMBERSHIP-RENEWAL' : $plan['accountingCode'],
        ];
        if ($selfPayment) {
            $endpoint = '/mobile-curves/ezypay/checkout';
            $payload += ['description' => $plan['name'], 'externalInvoiceId' => $paymentNumber];
        } elseif ($subscription) {
            $endpoint = '/mobile-curves/ezypay/subscriptions';
            $payload += ['planId' => $plan['id'], 'name' => $plan['name'], 'startDate' => Carbon::now('Asia/Jakarta')->format('Y-m-d'), 'externalInvoiceId' => $paymentNumber];
        } else {
            $endpoint = '/mobile-curves/ezypay/invoices';
            $payload += [
                'scheduledPaymentDate' => Carbon::now('Asia/Jakarta')->addDay()->toDateString(),
                'externalInvoiceId' => $paymentNumber,
                'description' => $plan['name'],
            ];
        }
        if (config('services.ezypay.log_request_payload')) {
            $loggedPayload = $payload;
            if ($paymentMethodToken !== null) {
                $loggedPayload['paymentMethodToken'] = '[REDACTED]';
            }

            Log::info('EZYPAY outgoing payment request.', [
                'lead_id' => $lead->id,
                'method' => 'POST',
                'endpoint' => $endpoint,
                'payload' => $loggedPayload,
            ]);
        }

        try {
            $beforeSubmit?->__invoke($paymentNumber);
            $response = $api->post($endpoint, $payload)->throw();
            $remoteId = $response->json('response.id');
            if (!is_string($remoteId) || trim($remoteId) === '' || strlen($remoteId) > 255) {
                throw new \UnexpectedValueException('Missing EZYPAY purchase ID.');
            }
            $checkoutUrl = $selfPayment ? $response->json('response.checkoutUrl') : null;
            if ($selfPayment && (!is_string($checkoutUrl) || !filter_var($checkoutUrl, FILTER_VALIDATE_URL)
                || parse_url($checkoutUrl, PHP_URL_SCHEME) !== 'https')) {
                throw new \UnexpectedValueException('Invalid checkout URL.');
            }
            $this->purchaseQuery($lead->id)->update([
                'subscription_id_ezypay' => !$selfPayment && $subscription ? $remoteId : null,
                'checkout_url' => $checkoutUrl,
                'external_payment_id_ezypay' => $remoteId,
                'payment_number' => $paymentNumber,
                'status' => $selfPayment ? 'checkout_created' : ($subscription ? 'subscribed' : 'invoiced'),
                'updated_at' => Carbon::now(),
            ]);
            return [
                'id' => $remoteId,
                'checkoutUrl' => $checkoutUrl,
                'response_text' => json_encode($response->json(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            ];
        } catch (\Throwable $e) {
            Log::warning('EZYPAY membership purchase requires review.', [
                'lead_id' => $lead->id, 'payment_type' => $selfPayment ? 'checkout' : ($subscription ? 'subscription' : 'invoice'),
                'endpoint' => $endpoint,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);
            throw new \RuntimeException(
                'Pembelian EZYPAY gagal atau belum dapat dipastikan. Periksa status sebelum mencoba kembali.',
                0,
                $e
            );
        }
    }

    public function syncToUserMobile(int $userId): void
    {
        try {
            $user = DB::table('usermobilecurves_v')->where('id', $userId)->first();
            if (!$user) {
                throw new \UnexpectedValueException('User not found in usermobilecurves_v.');
            }

            app(ApiClientService::class)->post("/mobile-curves/users/{$userId}", (array) $user)->throw();
        } catch (\Throwable $e) {
            // Membership is already committed. Keep the receipt and checkout link available.
            Log::warning('Failed to sync membership user to mobile Curves API.', [
                'user_id' => $userId,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);
        }
    }


    public function syncToUpdateUserMobile(int $userId, array $payload): void
    {
        try {
            app(ApiClientService::class)->put("/mobile-curves/users/{$userId}", $payload)->throw();
        } catch (\Throwable $e) {
            Log::warning('Failed to sync updated member to mobile Curves API.', [
                'user_id' => $userId,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);
        }
    }


    public function syncToUpdateMemberPaymentMobile(array $payload, int $staff_id): void
    {
        try {
            $payload += ['staff_id' => $staff_id];
            app(ApiClientService::class)->put("/mobile-curves/installments", $payload)->throw();
        } catch (\Throwable $e) {
            Log::warning('Failed to sync updated payment to mobile Curves API.', [
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);
        }
    }

    public function syncAddMemberPaymentMobile(array $payload, int $staff_id): void
    {
        try {
            $payload += ['staff_id' => $staff_id];
            app(ApiClientService::class)->post("/mobile-curves/installments", $payload)->throw();
        } catch (\Throwable $e) {
            Log::warning('Failed to sync updated payment to mobile Curves API.', [
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);
        }
    }
}
