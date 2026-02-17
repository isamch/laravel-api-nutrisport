<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponse;

    public function update(UpdateProfileRequest $request)
    {
        $user = auth('api')->user();
        $user->update($request->validated());

        return $this->success($user, 'Profile updated successfully');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth('api')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return $this->success(null, 'Password updated successfully');
    }
}
