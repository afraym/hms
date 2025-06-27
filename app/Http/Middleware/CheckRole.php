<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!$request->user()) {
            return redirect('login');
        }

        // Split roles and clean them
        $allowedRoles = array_map('trim', explode('|', $roles));
        
        // Check if user has any of the allowed roles
        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'غير مصرح بالدخول لهذه الصفحة');
        }

        return $next($request);
    }
}