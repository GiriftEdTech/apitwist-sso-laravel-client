<?php

namespace Girift\SSO\Services;

use App\Models\User;
use GuzzleHttp\Client;

class SSOService
{
    public function handle(string $token, ?string $expires_at = null, ?array $scopes = null): ?User
    {
        if (! $token) {
            return null;
        }

        $data = $this->getUserData($token);
        if (! $data) {
            return null;
        }

        $user = $this->createOrUpdateUser($data);

        $this->updateToken($user, $token, $expires_at, $scopes);

        return $user;
    }

    protected function getUserData(string $token)
    {
        if (! $token) {
            return null;
        }

        $http = new Client(['http_errors' => false]);
        $res = $http->get(config('sso.get_user_url'), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],

        ]);
        if ($res->getStatusCode() != 200) {
            return null;
        }
        $result = (string) $res->getBody();

        return json_decode($result, true);
    }

    protected function createOrUpdateUser($data): User
    {
        if ($old_user = User::where('email', $data['email'])->first()) {
            // check if any data is changed
            $changed = false;
            foreach ($data as $key => $value) {
                if ($value != $old_user->$key) {
                    $changed = true;
                    break;
                }
            }

            if ($changed) {
                $old_user->update([
                    'name' => $data['name'] ?? null,
                    'surname' => $data['surname'] ?? null,
                    'username' => $data['username'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'active' => $data['active'] ?? null,
                ]);
            }
        } else {
            return User::create([
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'surname' => $data['surname'] ?? null,
                'username' => $data['username'] ?? null,
                'phone' => $data['phone'] ?? null,
                'active' => $data['active'] ?? null,
            ]);
        }
    }

    protected function updateToken(User $user, string $token, ?string $expires_at = null, ?array $scopes = null): void
    {
        $user->ssoToken()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'token' => $token,
                'scopes' => $scopes,
                'last_used_at' => now(),
                'expires_at' => $expires_at,
            ]
        );
    }

    public function validateToken(string $token, User $user): bool
    {
        $result = $this->getUserData($token);

        if ($result && $result['email'] && $result['email'] === $user->email) {
            $user->ssoToken()->update([
                'last_used_at' => now(),
            ]);

            return true;
        }

        return false;
    }
}
