<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use ApiResponseTrait;
    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->unauthorizedResponse(
                null,
                403,
                'token missing'
            );
        }
    }
}
