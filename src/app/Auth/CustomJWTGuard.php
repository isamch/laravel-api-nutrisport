<?php

namespace App\Auth;

use Tymon\JWTAuth\JWTGuard as BaseJWTGuard;

class CustomJWTGuard extends BaseJWTGuard
{
    public function attempt(array $credentials = [], $login = true)
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) {
                $ttl = $this->getCustomTTL($user);
                $this->jwt->factory()->setTTL($ttl);
            }

            return $login ? $this->login($user) : true;
        }

        return false;
    }

    public function login($user)
    {
        $ttl = $this->getCustomTTL($user);
        $this->jwt->factory()->setTTL($ttl);

        $token = $this->jwt->fromUser($user);
        $this->setToken($token);

        return $token;
    }

    protected function getCustomTTL($user)
    {
        $user->load('role');
        
        return match($user->role->name ?? 'client') {
            'administrateur' => 480,
            'vendeur' => 360,
            'client' => 360,
            default => 360,
        };
    }
}
