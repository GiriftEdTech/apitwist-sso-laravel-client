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
            info('Token: '.$request->all());
            return response()->error(401, 'Unauthorized');
        }

        $user = $this->service->handle($token);

        if (! $user || ! $user->ssoToken()->exists()) {
            return response()->error(401, 'Unauthorized');
        }

        // If the user is logged in, but the token is expired,
        if ($user->ssoToken->isExpired()) {
            return response()->error(401, 'Unauthorized');
        }

        // If token last used at is greater than 30 minutes ago, logout user
        if ($user->ssoToken->last_used_at->diffInMinutes() > $this->validateTokenTime) {
            //validate token
            if (! $this->service->validateToken($user->getSsoToken(), $user)) {
                return response()->error(401, 'Unauthorized');
            }
        }
        Auth::login($user);

        return $next($request);
    }
}
