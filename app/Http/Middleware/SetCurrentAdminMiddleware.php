<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PromoCode\Locator\CurrentAdminLocator;
use PromoCode\Models\Admin;

class SetCurrentAdminMiddleware
{
    public function __construct(private readonly CurrentAdminLocator $currentAdminLocator)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admins')->user();
        if (!$admin instanceof Admin) {
            return $next($request);
        }

        $this->currentAdminLocator->set($admin);

        return $next($request);
    }
}
