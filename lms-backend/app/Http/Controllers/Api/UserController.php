<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get the authenticated user's profile
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['role', 'department']);

        return response()->json([
            'message' => 'User profile retrieved successfully',
            'data' => [
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
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'display_name' => $user->role->display_name,
                    'description' => $user->role->description,
                ],
                'department' => $user->department ? [
                    'id' => $user->department->id,
                    'name' => $user->department->name,
                    'code' => $user->department->code,
                    'description' => $user->department->description,
                ] : null,
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * Display a listing of users (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user has permission to view users
        if (!$request->user()->can('manage-users') && !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::with(['role', 'department'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, User $user): JsonResponse
    {
        // Users can view their own profile, admins can view any
        if ($request->user()->id !== $user->id && 
            !$request->user()->can('manage-users') && 
            !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->load(['role', 'department']);

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // Users can update their own profile, admins can update any
        if ($request->user()->id !== $user->id && 
            !$request->user()->can('manage-users') && 
            !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'photo_url' => 'sometimes|url|nullable',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->fresh(['role', 'department']),
        ]);
    }
}