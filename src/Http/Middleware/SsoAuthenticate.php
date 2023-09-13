<?php

namespace Girift\SSO\Http\Middleware;

use Girift\SSO\Services\SsoService;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class SsoAuthenticate
{
    private string $loginUrl;

    private int $validateTokenTime;

    private SsoService $service;

    public function __construct()
    {
        $this->loginUrl = route('sso.login');
        $this->validateTokenTime = 30;
        $this->service = new SsoService();
    }

    /**
     * Handle an incoming request.
     *
     * @return Application|RedirectResponse|Response|Redirector|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (! $user || ! $user->ssoToken()->exists()) {
            auth()->logout();

            return redirect($this->loginUrl);
        }

        // If the user is logged in, but the token is expired,
        if ($user->ssoToken->isExpired()) {
            $user->ssoToken->delete();
            auth()->logout();

            return redirect($this->loginUrl);
        }

        // If token last used at is greater than 30 minutes ago, logout user
        if ($user->ssoToken->last_used_at->diffInMinutes() > $this->validateTokenTime) {
            //validate token
            if (! $this->service->validateToken($user->getSsoToken(), $user)) {
                $user->ssoToken->delete();
                auth()->logout();

                return redirect($this->loginUrl);
            }
        }

        return $next($request);
    }
}
