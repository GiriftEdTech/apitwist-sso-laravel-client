<?php

namespace Girift\SSO\Http\Middleware;

use App\Models\User;
use Closure;
use Girift\SSO\Services\SSOService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if (! $user || ! $user->ssoToken()->exists()) {

            // check if session has sso_access_token
            if (! session()->has('sso_access_token')) {
                auth()->logout();

                return redirect($this->loginUrl);
            }

            $token = session('sso_access_token');
            if (! DB::table('sso_tokens')->where('token', $token)->first()) {
                auth()->logout();

                return redirect($this->loginUrl);
            }

            if (! $user = User::find($token)) {
                auth()->logout();

                return redirect($this->loginUrl);
            }

            Auth::login($user);
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
            if (! (new SSOService())->validateToken($user->getSsoToken(), $user)) {
                $user->ssoToken->delete();
                auth()->logout();

                return redirect($this->loginUrl);
            }
        }

        return $next($request);
    }
}
