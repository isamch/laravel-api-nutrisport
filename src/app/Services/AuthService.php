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

        $user->load('role');
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function login(array $credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        auth('api')->user()->load('role');

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth('api')->user()->load(['role.permissions']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => [
                'name' => $user->role->name,
                'permissions' => $user->role->permissions->pluck('name'),
            ],
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
        $user = auth('api')->user()->load(['role.permissions']);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => [
                    'name' => $user->role->name,
                    'permissions' => $user->role->permissions->pluck('name'),
                ],
            ],
        ];
    }
}
