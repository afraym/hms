<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $allowedRoles = explode('|', $roles);

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'غير مصرح بالدخول لهذه الصفحة.');
        }

        return $next($request);
    }
}