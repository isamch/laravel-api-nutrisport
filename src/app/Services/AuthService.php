<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role_id' => DB::table('roles')->where('name', 'client')->value('id'),
            'email_verified_at' => now(),
        ]);

        // Load role before generating token
        $user->load('role');
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function login(array $credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        // Load role after login
        auth('api')->user()->load('role');

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth('api')->user()->load('role');
        
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role->name,
            'email_verified_at' => $user->email_verified_at,
        ];
    }

    public function logout()
    {
        auth('api')->logout();
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user()->load('role');
        
        $ttl = match($user->role->name ?? 'client') {
            'administrateur' => 480,
            'vendeur' => 360,
            'client' => 360,
            default => 360,
        };
        
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->name,
            ],
        ];
    }
}
