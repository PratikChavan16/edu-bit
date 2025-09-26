<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    /**
     * Get subjects accessible to the authenticated user
     * GET /api/subjects
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $subjects = $this->getAccessibleSubjects($user);

            return response()->json([
                'message' => 'Subjects retrieved successfully',
                'data' => $subjects,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve subjects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific subject
     * GET /api/subjects/{subject}
     */
    public function show(Request $request, Subject $subject): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Check if user has access to this subject
            if (!$this->hasSubjectAccess($subject, $user)) {
                return response()->json([
                    'message' => 'You do not have access to this subject',
                ], 403);
            }

            $subject->load(['course.department', 'notes' => function($query) {
                $query->active()->with('uploader:id,first_name,last_name');
            }, 'videos' => function($query) use ($user) {
                $query->active()->with('uploader:id,first_name,last_name');
                // Students only see processed videos
                if ($user->hasRole('student')) {
                    $query->processed();
                }
            }]);

            return response()->json([
                'message' => 'Subject retrieved successfully',
                'data' => $subject,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subjects accessible to a user based on their role
     */
    private function getAccessibleSubjects(User $user)
    {
        $query = Subject::with(['course.department']);

        // Admins and principals can see all subjects
        if ($user->hasAnyRole(['admin', 'principal'])) {
            return $query->get();
        }

        // HODs can see subjects in their department
        if ($user->hasRole('hod') && $user->department_id) {
            return $query->whereHas('course', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->get();
        }

        // Faculty can see subjects in their department
        if ($user->hasRole('faculty') && $user->department_id) {
            return $query->whereHas('course', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->get();
        }

        // Students can see subjects in their department
        if ($user->hasRole('student') && $user->department_id) {
            return $query->whereHas('course', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->get();
        }

        // Return empty collection if no access
        return collect();
    }

    /**
     * Check if user has access to a specific subject
     */
    private function hasSubjectAccess(Subject $subject, User $user): bool
    {
        // Admins and principals have access to everything
        if ($user->hasAnyRole(['admin', 'principal'])) {
            return true;
        }

        // HODs have access to their department's subjects
        if ($user->hasRole('hod') && $user->department_id === $subject->course->department_id) {
            return true;
        }

        // Faculty have access to subjects in their department
        if ($user->hasRole('faculty') && $user->department_id === $subject->course->department_id) {
            return true;
        }

        // Students have access to subjects in their department
        if ($user->hasRole('student') && $user->department_id === $subject->course->department_id) {
            return true;
        }

        return false;
    }
}
