<?php

namespace Girift\SSO\Traits;

use Girift\SSO\Models\SSOToken;

trait HasSsoTokens
{
    public function getSsoToken()
    {
        return $this->ssoToken->token;
    }

    public function ssoToken(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SSOToken::class);
    }
}
