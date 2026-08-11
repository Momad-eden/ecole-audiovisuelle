<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, 'Vous n’avez pas l’autorisation d’accéder à cette section.');
        }

        return $next($request);
    }
}