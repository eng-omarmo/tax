<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
    use ApiResponseTrait;


    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            throw new AuthenticationException('Authentication token is invalid or missing', $guards);
        }

        return redirect()->guest(route('login'));
    }

}
