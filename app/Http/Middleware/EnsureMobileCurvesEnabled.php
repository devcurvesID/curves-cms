<?php

namespace App\Http\Middleware;

use App\Services\ApiClientService;
use Closure;

class EnsureMobileCurvesEnabled
{
    public function handle($request, Closure $next)
    {
        $providerActions = [
            'cashierMembershipWithEzypay',
            'cashierRenewalWithEzypay',
            'storeEzypayInstallments',
            'ensureEzypayMembershipPlan',
            'saveEzypayCustomer',
            'storeEzypayPaymentMethod',
            'storeBillEzypayCard',
            'storeEzypayInstallmentCard',
            'prepareInstallmentEzypayCustomer',
            'renewalEzypayOptions',
        ];
        if (ApiClientService::isEnabled() || !in_array($request->route()?->getActionMethod(), $providerActions, true)) {
            return $next($request);
        }

        $message = 'Integrasi mobile-curves sedang dinonaktifkan. Pilih metode pembayaran lain atau coba setelah integrasi diaktifkan.';
        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'retry_allowed' => true], 503);
        }

        return back()->withErrors(['payment_method' => $message]);
    }
}
