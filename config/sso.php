<?php

// config for Girift/apitwist-sso-laravel-client

$sso_domain = env('SSO_DOMAIN', 'https://sso.apitwist.com');

return [
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_url' => config('app.url').'/sso/callback',
    'sso_domain' => $sso_domain,
    'authorize_url' => $sso_domain.'/oauth/authorize',
    'api_url' => $sso_domain.'/oauth/token',
    'logout_url' => $sso_domain.'/logout',
    'error_url' => $sso_domain.'/error',
    'get_user_url' => $sso_domain.'/api/user',
];
