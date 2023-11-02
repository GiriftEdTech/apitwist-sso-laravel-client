<?php

// config for Girift/apitwist-sso-laravel-client

return [
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_url' => config('app.url').'/sso/callback',
    'sso_domain' => env('SSO_DOMAIN', 'https://sso.apitwist.com'),
    'authorize_url' => config('sso.sso_domain').'/oauth/authorize',
    'api_url' => config('sso.sso_domain').'/oauth/token',
    'logout_url' => config('sso.sso_domain').'/logout',
    'get_user_url' => config('sso.sso_domain').'/api/user',
];
