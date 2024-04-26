<?php

namespace Girift\SSO\Http\Middleware;

use Closure;
use Girift\SSO\Services\SSOService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SsoApiAuthenticate
{
    private int $validateTokenTime;

    private SSOService $service;

    public function __construct()
    {
        $this->validateTokenTime = 30;
        $this->service = new SSOService();
    }

    /**
     * Handle an incoming api request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = $this->service->handle($token);

        if (! $user || ! $user->ssoToken()->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // If the user is logged in, but the token is expired,
        if ($user->ssoToken->isExpired()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // If token last used at is greater than 30 minutes ago, logout user
        if ($user->ssoToken->last_used_at->diffInMinutes() > $this->validateTokenTime) {
            //validate token
            if (! $this->service->validateToken($user->getSsoToken(), $user)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
        }
        Auth::login($user);

        return $next($request);
    }
}
