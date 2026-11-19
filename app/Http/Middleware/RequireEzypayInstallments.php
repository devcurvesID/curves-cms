<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequireEzypayInstallments
{
    public function handle($request, Closure $next)
    {
        if (!\App\Services\ApiClientService::isEnabled()) return $next($request);
        if (!Auth::check() || $request->routeIs('logout')) return $next($request);
        $pending = DB::table('ezypay_membership_purchases')->where('cashier_id', Auth::id())
            ->where('installments_completed', false)->where('installment_remaining', '>', 0)->orderBy('id')->first();
        if (!$pending) return $next($request);
        $url = route('ezypay.installments', ['purchase' => $pending->id]);
        if ($request->routeIs('ezypay.installments', 'ezypay.installments.store', 'ezypay.installments.methods', 'ezypay.installments.card')
            && (string) $request->route('purchase') === (string) $pending->id) return $next($request);
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Selesaikan pengaturan cicilan EZYPAY sebelum membuka menu lain.', 'installment_url' => $url], 409);
        }
        return redirect($url);
    }
}
