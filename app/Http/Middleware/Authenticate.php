<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Check which guard is being used
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                // Redirect to admin login if accessing admin routes
                return route('admin.login');
            }

            // Default redirect to user login
            return route('login');
        }

        return null;
    }
}
