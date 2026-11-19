<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Psr7\Response as PsrResponse;

class ApiClientService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.mobile_curves.base_url'), '/');
    }

    public static function isEnabled(): bool
    {
        return (bool) config('services.mobile_curves.enabled', true);
    }

    protected function headers(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function get(string $endpoint, array $query = [])
    {
        return $this->send('get', $endpoint, $query);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->send('post', $endpoint, $data);
    }

    public function put(string $endpoint, array $data = [])
    {
        return $this->send('put', $endpoint, $data);
    }

    public function delete(string $endpoint, array $data = [])
    {
        return $this->send('delete', $endpoint, $data);
    }

    private function send(string $method, string $endpoint, array $data): Response
    {
        if (!self::isEnabled() && preg_match('#^/?mobile-curves(?:/|\?|$)#', $endpoint)) {
            // Synchronization callers can continue, while EZYPAY requires a real provider result.
            $requiresProvider = preg_match('#^/?mobile-curves/ezypay(?:/|\?|$)#', $endpoint);
            return new Response(new PsrResponse(
                $requiresProvider ? 503 : 204,
                ['Content-Type' => 'application/json', 'X-Mobile-Curves-Skipped' => '1'],
                $requiresProvider ? json_encode(['message' => 'Integrasi mobile-curves sedang dinonaktifkan.']) : ''
            ));
        }

        return Http::withHeaders($this->headers())->{$method}($this->baseUrl . $endpoint, $data);
    }
}
