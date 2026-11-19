<?php

namespace App\Http\Controllers;

use App\Model\Club\ClubInfo;
use App\Services\ApiClientService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EzypayInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
            'club_id' => 'nullable|integer|exists:club_infos,id',
        ]);
        $search = trim($filters['q'] ?? '');
        $clubId = $filters['club_id'] ?? null;
        $members = User::select('id', 'name', 'email', 'club_id')
            ->with('user_club:id,club_name')
            ->whereNotNull('customer_id_ezypay')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            })
            ->when($clubId !== null, function ($query) use ($clubId) {
                $query->where('club_id', $clubId);
            })
            ->orderByRaw('created_at DESC NULLS LAST')->orderByDesc('id')->paginate(20)->withQueryString();
        $pendingCheckouts = DB::table('ezypay_membership_purchases as purchase')
            ->leftJoin('member_payments as payment', 'payment.id', '=', 'purchase.member_payment_id')
            ->leftJoin('users as member', 'member.id', '=', 'payment.user_id')
            ->leftJoin('buddy_referral_leads as lead', 'lead.id', '=', 'purchase.lead_id')
            ->leftJoin('club_infos as club', 'club.id', '=', DB::raw('COALESCE(member.club_id, lead.club_id)'))
            ->whereNotNull('purchase.checkout_url')
            ->whereNull('payment.ezypay_invoice_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('member_payments as checkout_payment')
                    ->whereColumn('checkout_payment.ezypay_checkout_id', 'purchase.external_payment_id_ezypay')
                    ->whereNotNull('checkout_payment.ezypay_invoice_id');
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereRaw('COALESCE(member.name, lead.fullname) ILIKE ?', ['%' . $search . '%']);
            })
            ->when($clubId !== null, function ($query) use ($clubId) {
                $query->where('club.id', $clubId);
            })
            ->select('purchase.id', 'purchase.checkout_url', 'purchase.payment_number', 'purchase.created_at', 'club.club_name')
            ->selectRaw('COALESCE(member.name, lead.fullname) AS member_name')
            ->orderByRaw('purchase.created_at DESC NULLS LAST')->orderByDesc('purchase.id')
            ->paginate(10, ['*'], 'checkout_page')->withQueryString();
        $clubs = ClubInfo::orderBy('club_name')->get(['id', 'club_name']);

        return response()->view('cf_lite.cashiers.invoices', compact('members', 'pendingCheckouts', 'clubs', 'search', 'clubId'))
            ->header('Cache-Control', 'no-store');
    }

    public function show(int $member, ApiClientService $apiClient)
    {
        $user = User::whereNotNull('customer_id_ezypay')->findOrFail($member, ['id', 'customer_id_ezypay']);
        $customerId = trim($user->customer_id_ezypay);
        abort_if($customerId === '', 404);

        try {
            $response = $apiClient->get('/mobile-curves/ezypay/invoices', ['customerId' => $customerId])->throw();
            $invoices = $response->json('response.data');
            if (!is_array($invoices) || !array_is_list($invoices)) {
                throw new \UnexpectedValueException('Invalid EZYPAY invoices response.');
            }

            $details = [];
            foreach ($invoices as $invoice) {
                if (!is_array($invoice) || (isset($invoice['customerId']) && $invoice['customerId'] !== $customerId)) {
                    throw new \UnexpectedValueException('Invalid EZYPAY invoice for member.');
                }

                $detail = Arr::only($invoice, ['documentNumber', 'externalInvoiceId', 'date', 'dueDate', 'scheduledPaymentDate', 'status', 'memo']);
                $detail['amount'] = $this->invoiceAmount($invoice['amount'] ?? null);
                $detail['discount'] = $this->invoiceAmount($invoice['totalDiscounted'] ?? null);
                $detail['tax'] = $this->invoiceAmount($invoice['totalTax'] ?? null);
                $detail['items'] = [];
                foreach ((array) ($invoice['items'] ?? []) as $item) {
                    if (is_array($item)) {
                        $detail['items'][] = [
                            'description' => $item['description'] ?? '',
                            'amount' => $this->invoiceAmount($item['amount'] ?? null),
                        ];
                    }
                }

                $method = $invoice['paymentMethodData'] ?? [];
                $last4 = $method['card']['last4'] ?? null;
                $detail['paymentMethod'] = implode(' ', array_filter([
                    $method['card']['type'] ?? $method['type'] ?? null,
                    is_string($last4) && preg_match('/^[0-9]{4}$/D', $last4) ? '•••• ' . $last4 : null,
                ]));
                $url = $invoice['payNowUrl'] ?? null;
                $detail['payNowUrl'] = is_string($url) && filter_var($url, FILTER_VALIDATE_URL)
                    && parse_url($url, PHP_URL_SCHEME) === 'https' ? $url : null;
                $details[] = $detail;
            }

            return response()->json([
                'invoices' => $details,
                'total' => (int) $response->json('response.paging.totalCount', count($details)),
            ])->header('Cache-Control', 'no-store');
        } catch (\Throwable $e) {
            Log::warning('Failed to load EZYPAY invoices.', [
                'member_id' => $user->id,
                'http_status' => $e instanceof \Illuminate\Http\Client\RequestException ? $e->response->status() : null,
                'exception_type' => get_class($e),
            ]);

            return response()->json(['message' => 'Gagal memuat invoice EZYPAY. Silakan coba lagi.'], 502);
        }
    }

    private function invoiceAmount($amount): ?array
    {
        return is_array($amount) ? Arr::only($amount, ['currency', 'value']) : null;
    }
}
