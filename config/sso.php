<?php

// config for Girift/apitwist-sso-laravel-client

return [
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_url' => env('SSO_REDIRECT_URL'),
    'authorize_url' => env('SSO_API_AUTHORIZE_URL', 'https://sso.apitwist.com/oauth/authorize'),
    'api_url' => env('SSO_API_URL', 'https://sso.apitwist.com/oauth/token'),
    'logout_url' => env('SSO_API_LOGOUT_URL', 'https://sso.apitwist.com/logout'),
    'get_user_url' => env('SSO_API_GET_USER_URL', 'https://sso.apitwist.com/api/user'),
];
