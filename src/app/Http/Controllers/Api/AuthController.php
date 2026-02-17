<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());
        return response()->json($result, 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($result);
    }

    public function me()
    {
        return response()->json($this->authService->me());
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return response()->json($this->authService->refresh());
    }
}
