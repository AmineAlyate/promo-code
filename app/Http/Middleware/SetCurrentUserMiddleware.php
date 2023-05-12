<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PromoCode\Locator\CurrentUserLocator;
use PromoCode\Models\User;

class SetCurrentUserMiddleware
{
    public function __construct(private readonly CurrentUserLocator $currentUserLocator)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = auth('users')->user();
        if (!$user instanceof User) {
            return $next($request);
        }

        $this->currentUserLocator->set($user);

        return $next($request);
    }
}
