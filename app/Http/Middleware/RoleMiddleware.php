<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user || $user->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
