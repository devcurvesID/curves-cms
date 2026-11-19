<?php

namespace App\Http\Controllers;

use App\Services\ApiClientService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EzypayTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'cursor' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);
        $cursor = (int) ($filters['cursor'] ?? 0);
        $limit = (int) ($filters['limit'] ?? 10);
        $query = ['limit' => $limit];
        if ($cursor > 0) {
            $query['cursor'] = $cursor;
        }
        $transactions = collect();
        $nextCursor = null;
        $loadFailed = false;

        try {
            $response = app(ApiClientService::class)->get('/mobile-curves/ezypay/transactions', $query)
                ->throw()->json('response');

            $data = $response['data'] ?? null;
            if (!is_array($data) || !array_is_list($data)) {
                throw new \UnexpectedValueException('Invalid EZYPAY transactions response.');
            }

            $rows = collect($data)->filter(function ($transaction) {
                $senderId = data_get($transaction, 'sender.id');
                return is_string($senderId) && trim($senderId) !== '';
            });
            $senderIds = $rows->pluck('sender.id')->unique()->values();
            $members = User::where('club_id', $request->user()->club_id)
                ->whereNotNull('customer_id_ezypay')
                ->whereIn('customer_id_ezypay', $senderIds)
                ->pluck('name', 'customer_id_ezypay');

            $transactions = $rows->filter(fn ($transaction) => $members->has($transaction['sender']['id']))
                ->map(function ($transaction) use ($members) {
                    $transaction['member_name'] = $members->get($transaction['sender']['id']);
                    return $transaction;
                });

            $candidate = filter_var(data_get($response, 'paging.nextCursor'), FILTER_VALIDATE_INT);
            if ($candidate !== false && $candidate > $cursor) {
                $nextCursor = $candidate;
            }
        } catch (\Throwable $e) {
            $loadFailed = true;
            Log::warning('Failed to load EZYPAY transactions.', [
                'exception_type' => get_class($e),
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
            ]);
        }

        return view('cf_lite.financial-management.ezypay-transactions', compact(
            'transactions', 'cursor', 'limit', 'nextCursor', 'loadFailed'
        ));
    }

    public function show(Request $request, string $transactionId)
    {
        try {
            $transaction = app(ApiClientService::class)
                ->get('/mobile-curves/ezypay/transactions/' . rawurlencode($transactionId))
                ->throw()->json('response');
            if (!is_array($transaction) || ($transaction['id'] ?? null) !== $transactionId) {
                throw new \UnexpectedValueException('Invalid EZYPAY transaction detail response.');
            }
        } catch (\Throwable $e) {
            $status = $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null;
            if ($status === 404) {
                return response()->json(['message' => 'Transaksi Ezypay tidak ditemukan.'], 404);
            }
            Log::warning('Failed to load EZYPAY transaction detail.', [
                'transaction_id' => $transactionId,
                'exception_type' => get_class($e),
                'http_status' => $status,
            ]);
            return response()->json(['message' => 'Gagal memuat detail transaksi Ezypay.'], 502);
        }

        $senderId = data_get($transaction, 'sender.id');
        $member = is_string($senderId) && trim($senderId) !== ''
            ? User::where('club_id', $request->user()->club_id)
                ->where('customer_id_ezypay', $senderId)->first(['name'])
            : null;

        if ($member === null) {
            return response()->json(['message' => 'Transaksi Ezypay tidak ditemukan.'], 404);
        }

        $amount = data_get($transaction, 'amount.value');
        $cardSuffix = $transaction['paymentMethodDescription'] ?? null;
        return response()->json([
            'ID transaksi' => $transaction['id'],
            'Nomor transaksi' => $transaction['number'] ?? null,
            'Anggota' => $member->name,
            'Penerima' => data_get($transaction, 'receiver.name'),
            'Waktu transaksi' => $transaction['createdOn'] ?? null,
            'Nomor dokumen' => data_get($transaction, 'document.number'),
            'Jenis dokumen' => data_get($transaction, 'document.type'),
            'Jenis transaksi' => $transaction['type'] ?? null,
            'Metode pembayaran' => $transaction['paymentMethodType'] ?? null,
            'Kartu' => ($transaction['paymentMethodType'] ?? null) === 'CARD'
                && is_string($cardSuffix) && preg_match('/^[0-9]{4}$/D', $cardSuffix) ? '•••• ' . $cardSuffix : null,
            'Sumber transaksi' => $transaction['source'] ?? null,
            'Kanal' => $transaction['channel'] ?? null,
            'Jumlah' => is_numeric($amount)
                ? trim(data_get($transaction, 'amount.currency') . ' ' . number_format((float) $amount, (float) $amount == (int) $amount ? 0 : 2, ',', '.'))
                : null,
            'Status' => $transaction['status'] ?? null,
            'Waktu gagal' => $transaction['failedOn'] ?? null,
            'Alasan gagal' => $transaction['failedPaymentReason'] ?? null,
            'Kode respons penyedia' => data_get($transaction, 'paymentProviderResponse.code'),
            'Pesan penyedia' => data_get($transaction, 'paymentProviderResponse.description'),
        ]);
    }
}
