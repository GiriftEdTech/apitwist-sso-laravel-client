<?php

namespace Girift\SSO\Http\Middleware;

use Closure;
use Girift\SSO\Services\SSOService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class SsoAuthenticate
{
    private string $loginUrl;

    private int $validateTokenTime;

    public function __construct()
    {
        $this->loginUrl = route('sso.login');
        $this->validateTokenTime = 30;
    }

    /**
     * Handle an incoming request.
     *
     * @return Application|RedirectResponse|Response|Redirector|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        info('---------------- SsoAuthenticate middleware');
        info('user: '.$user ? $user->id : 'no user');
        if (! $user || ! $user->ssoToken()->exists()) {
            info('no token found');
            auth()->logout();

            return redirect($this->loginUrl);
        }

        // If the user is logged in, but the token is expired,
        if ($user->ssoToken->isExpired()) {
            info('token is expired');
            $user->ssoToken->delete();
            auth()->logout();

            return redirect($this->loginUrl);
        }

        // If token last used at is greater than 30 minutes ago, logout user
        if ($user->ssoToken->last_used_at->diffInMinutes() > $this->validateTokenTime) {
            info('token last used at is greater than 30 minutes ago');
            //validate token
            if (! (new SSOService())->validateToken($user->getSsoToken(), $user)) {
                info('token is not valid');
                $user->ssoToken->delete();
                auth()->logout();

                return redirect($this->loginUrl);
            }
        }

        return $next($request);
    }
}
