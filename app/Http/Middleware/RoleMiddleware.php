<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = is_object($user->role) ? $user->role->value : (string) $user->role;

        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        return match ($userRole) {
            'admin' => redirect()->route('admin.dashboard'),
            'marketing' => redirect()->route('marketing.dashboard'),
            'manajemen' => redirect()->route('manajemen.dashboard'),
            default => abort(Response::HTTP_FORBIDDEN, 'Akses ditolak.'),
        };
    }
}
