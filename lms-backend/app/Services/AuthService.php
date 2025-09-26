<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Revoke all existing tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $this->formatUserData($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user)
    {
        // Revoke all tokens for the user
        $user->tokens()->delete();

        return ['message' => 'Logged out successfully'];
    }

    public function me(User $user)
    {
        return $this->formatUserData($user);
    }

    private function formatUserData(User $user)
    {
        $user->load(['department']);
        $role = $user->roles()->with('details')->first();
        
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'current_year' => $user->current_year,
            'enrollment_number' => $user->enrollment_number,
            'photo_url' => $user->photo_url,
            'is_active' => $user->is_active,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->details?->display_name ?? $role->name,
                'description' => $role->details?->description,
            ],
            'department' => $user->department ? [
                'id' => $user->department->id,
                'name' => $user->department->name,
                'code' => $user->department->code,
                'description' => $user->department->description,
            ] : null,
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'role_names' => $user->getRoleNames(),
        ];
    }
}