<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use ApiResponseTrait;

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {

            return $this->unprocessableResponse('Authentication token is invalid or missing');
        }

        return redirect()->guest(route('login'));
    }
}
