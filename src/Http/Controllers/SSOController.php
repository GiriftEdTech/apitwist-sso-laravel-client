<?php

namespace Girift\SSO\Http\Controllers;

use Carbon\Carbon;
use Girift\SSO\Services\SSOService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SSOController extends Controller
{
    public function login(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));
        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.redirect_url'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);

        return redirect()->away(config('sso.authorize_url').'?'.$query);
    }

    public function callback(Request $request)
    {
        if ($error = $request->get('error')) {
            return redirect()->away(config('sso.error_url'))->with('error', $error);
        }

        $state = $request->session()->get('state');
        if ($request->input('state') != $state) {
            return redirect()->route('sso.login')->with('error', 'Invalid state');
        }

        $http = new Client(['http_errors' => false]);
        $response = $http->post(config('sso.api_url'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => config('sso.client_id'),
                'client_secret' => config('sso.client_secret'),
                'redirect_uri' => config('sso.redirect_url'),
                'code' => $request->input('code'),
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            return redirect()->route('sso.login')->with('error', 'Invalid code');
        }

        $response = json_decode((string) $response->getBody(), true);

        $request->session()->put('sso_access_token', $response['access_token']);
        $request->session()->put('sso_refresh_token', $response['refresh_token']);
        $request->session()->put('sso_tokens_verified_at', now());
        $request->session()->put('sso_tokens_expires_in', $response['expires_in']);

        $expires_at = Carbon::parse($response['expires_in'] + now()->timestamp);

        if (! $user = (new SSOService())->handle($response['access_token'], $expires_at)) {
            return redirect()->route('sso.login')->with('error', 'Invalid state');
        }
        Auth::login($user);

        return redirect()->route('sso.loggedIn', ['user' => $user->id, 'token' => $response['access_token']]);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->ssoToken()->exists()) {
                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$user->getSsoToken(),
                ];
                $client = new Client(['headers' => $headers, 'http_errors' => false]);
                $client->post(config('sso.sso_domain').'/api/logout');
                $user->ssoToken->delete();
            }
        }
        $request->session()->forget('access_token');

        return redirect()->away(config('sso.logout_url').'?callback='.env('APP_URL'));
    }
}
