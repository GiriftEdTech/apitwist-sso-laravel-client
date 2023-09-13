<?php

namespace Girift\SSO\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * @property mixed $id
 * @property mixed $user_id
 * @property mixed $token
 * @property mixed $scopes
 * @property mixed $last_used_at
 * @property mixed $expires_at
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class SSOToken extends Model
{
    use Prunable;

    protected $table = 'sso_tokens';

    protected $guarded = [];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prunable()
    {
        return static::where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }
}
