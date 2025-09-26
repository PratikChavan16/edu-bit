<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContentService;
use App\Models\Subject;
use App\Models\Note;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContentController extends Controller
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Get notes for a specific subject
     * GET /api/subjects/{subject}/notes
     */
    public function getNotes(Request $request, Subject $subject): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'per_page']);
            $result = $this->contentService->getSubjectNotes($subject, $filters);

            return response()->json([
                'message' => 'Notes retrieved successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate upload URL for notes
     * POST /api/subjects/{subject}/notes/upload-url
     */
    public function generateNoteUploadUrl(Request $request, Subject $subject): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string',
            'file_size' => 'required|integer|min:1|max:104857600', // 100MB max
        ]);

        try {
            $uploadData = $this->contentService->generateNoteUploadUrl(
                $subject,
                $request->user(),
                $request->only(['filename', 'content_type', 'file_size'])
            );

            return response()->json([
                'message' => 'Upload URL generated successfully',
                'data' => $uploadData,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Invalid file type',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate upload URL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm note upload and create database entry
     * POST /api/subjects/{subject}/notes/confirm-upload
     */
    public function confirmNoteUpload(Request $request, Subject $subject): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file_path' => 'required|string',
            'upload_id' => 'required|string',
        ]);

        try {
            $note = $this->contentService->confirmNoteUpload(
                $subject,
                $request->user(),
                $request->only(['title', 'description', 'file_path', 'upload_id'])
            );

            return response()->json([
                'message' => 'Note uploaded successfully',
                'data' => $note->load('uploader:id,first_name,last_name'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to confirm note upload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get videos for a specific subject
     * GET /api/subjects/{subject}/videos
     */
    public function getVideos(Request $request, Subject $subject): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'per_page']);
            
            // Students can only see completed videos
            if ($request->user()->hasRole('student')) {
                $filters['include_processing'] = false;
            } else {
                $filters['include_processing'] = true;
            }

            $result = $this->contentService->getSubjectVideos($subject, $filters);

            return response()->json([
                'message' => 'Videos retrieved successfully',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve videos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate upload URL for videos
     * POST /api/subjects/{subject}/videos/upload-url
     */
    public function generateVideoUploadUrl(Request $request, Subject $subject): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'content_type' => 'required|string',
            'file_size' => 'required|integer|min:1|max:5368709120', // 5GB max
        ]);

        try {
            $uploadData = $this->contentService->generateVideoUploadUrl(
                $subject,
                $request->user(),
                $request->only(['filename', 'content_type', 'file_size'])
            );

            return response()->json([
                'message' => 'Video upload URL generated successfully',
                'data' => $uploadData,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Invalid file type',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate upload URL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm video upload and create database entry
     * POST /api/subjects/{subject}/videos/confirm-upload
     */
    public function confirmVideoUpload(Request $request, Subject $subject): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file_path' => 'required|string',
            'upload_id' => 'required|string',
        ]);

        try {
            $video = $this->contentService->confirmVideoUpload(
                $subject,
                $request->user(),
                $request->only(['title', 'description', 'file_path', 'upload_id'])
            );

            return response()->json([
                'message' => 'Video uploaded successfully and queued for processing',
                'data' => $video->load('uploader:id,first_name,last_name'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to confirm video upload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get download URL for a note
     * GET /api/notes/{note}/download
     */
    public function downloadNote(Request $request, Note $note): JsonResponse
    {
        try {
            $downloadUrl = $this->contentService->generateNoteDownloadUrl($note, $request->user());

            return response()->json([
                'message' => 'Download URL generated successfully',
                'data' => [
                    'download_url' => $downloadUrl,
                    'expires_at' => now()->addHour()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate download URL',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Get streaming URL for a video
     * GET /api/videos/{video}/stream
     */
    public function streamVideo(Request $request, Video $video): JsonResponse
    {
        try {
            $streamData = $this->contentService->generateVideoStreamUrl($video, $request->user());

            return response()->json([
                'message' => 'Stream URLs generated successfully',
                'data' => $streamData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate stream URLs',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Delete a note (soft delete)
     * DELETE /api/notes/{note}
     */
    public function deleteNote(Request $request, Note $note): JsonResponse
    {
        try {
            // Only the uploader or admin can delete
            if ($note->uploaded_by !== $request->user()->id && !$request->user()->hasRole(['admin', 'principal'])) {
                return response()->json([
                    'message' => 'You can only delete your own notes',
                ], 403);
            }

            $note->update(['is_active' => false]);

            return response()->json([
                'message' => 'Note deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a video (soft delete)
     * DELETE /api/videos/{video}
     */
    public function deleteVideo(Request $request, Video $video): JsonResponse
    {
        try {
            // Only the uploader or admin can delete
            if ($video->uploaded_by !== $request->user()->id && !$request->user()->hasRole(['admin', 'principal'])) {
                return response()->json([
                    'message' => 'You can only delete your own videos',
                ], 403);
            }

            $video->update(['is_active' => false]);

            return response()->json([
                'message' => 'Video deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
