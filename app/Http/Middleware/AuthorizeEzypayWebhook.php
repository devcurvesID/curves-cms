<?php

namespace App\Http\Middleware;

use Laravel\Passport\Exceptions\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class AuthorizeEzypayWebhook extends CheckClientCredentials
{
    public const SCOPE = 'ezypay-webhook';

    protected function validateCredentials($token)
    {
        if (!$token || $token->user_id !== null) {
            throw new AuthenticationException;
        }
    }

    protected function validateScopes($token, $scopes)
    {
        if (!in_array(self::SCOPE, $token->scopes, true)) {
            throw new MissingScopeException(self::SCOPE);
        }
    }
}
