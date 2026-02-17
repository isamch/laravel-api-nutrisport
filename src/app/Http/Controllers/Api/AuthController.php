<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());
        return $this->success($result, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->success($result, 'Login successful');
    }

    public function me()
    {
        return $this->success($this->authService->me());
    }

    public function logout()
    {
        $this->authService->logout();
        return $this->success(null, 'Successfully logged out');
    }

    public function refresh()
    {
        return $this->success($this->authService->refresh(), 'Token refreshed');
    }
}
