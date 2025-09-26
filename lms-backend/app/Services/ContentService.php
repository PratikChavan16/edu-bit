<?php

namespace App\Services;

use App\Models\Note;
use App\Models\Video;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ContentService
{
    /**
     * Generate S3 signed upload URL for notes
     */
    public function generateNoteUploadUrl(Subject $subject, User $user, array $fileData): array
    {
        $this->validateUploadPermissions($user);
        
        $fileName = $this->sanitizeFileName($fileData['filename']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        // Validate file type for notes
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid file type for notes. Allowed: ' . implode(', ', $allowedExtensions));
        }
        
        $uniqueFileName = sprintf(
            'notes/%s/%s/%s_%s.%s',
            $subject->course->department->code,
            $subject->code,
            Str::uuid(),
            time(),
            $fileExtension
        );
        
        // Generate presigned URL (expires in 1 hour)
        $disk = Storage::disk('s3');
        $url = $disk->temporaryUrl($uniqueFileName, Carbon::now()->addHour(), [
            'ContentType' => $fileData['content_type'] ?? 'application/octet-stream',
            'ContentLength' => $fileData['file_size'] ?? null,
        ]);
        
        return [
            'upload_url' => $url,
            'file_path' => $uniqueFileName,
            'expires_at' => Carbon::now()->addHour()->toISOString(),
            'upload_id' => Str::uuid(),
        ];
    }
    
    /**
     * Confirm note upload and create database entry
     */
    public function confirmNoteUpload(Subject $subject, User $user, array $uploadData): Note
    {
        $this->validateUploadPermissions($user);
        
        // Verify file exists in S3
        if (!Storage::disk('s3')->exists($uploadData['file_path'])) {
            throw new \Exception('File not found in storage. Upload may have failed.');
        }
        
        // Get file size from S3
        $fileSize = Storage::disk('s3')->size($uploadData['file_path']);
        
        return Note::create([
            'subject_id' => $subject->id,
            'title' => $uploadData['title'],
            'description' => $uploadData['description'] ?? null,
            'file_path' => $uploadData['file_path'],
            'file_size' => $fileSize,
            'file_type' => pathinfo($uploadData['file_path'], PATHINFO_EXTENSION),
            'uploaded_by' => $user->id,
            'is_active' => true,
        ]);
    }
    
    /**
     * Generate S3 signed upload URL for videos
     */
    public function generateVideoUploadUrl(Subject $subject, User $user, array $fileData): array
    {
        $this->validateUploadPermissions($user);
        
        $fileName = $this->sanitizeFileName($fileData['filename']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        // Validate file type for videos
        $allowedExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid file type for videos. Allowed: ' . implode(', ', $allowedExtensions));
        }
        
        $uniqueFileName = sprintf(
            'videos/raw/%s/%s/%s_%s.%s',
            $subject->course->department->code,
            $subject->code,
            Str::uuid(),
            time(),
            $fileExtension
        );
        
        // Generate presigned URL (expires in 2 hours for large video uploads)
        $disk = Storage::disk('s3');
        $url = $disk->temporaryUrl($uniqueFileName, Carbon::now()->addHours(2), [
            'ContentType' => $fileData['content_type'] ?? 'video/mp4',
            'ContentLength' => $fileData['file_size'] ?? null,
        ]);
        
        return [
            'upload_url' => $url,
            'file_path' => $uniqueFileName,
            'expires_at' => Carbon::now()->addHours(2)->toISOString(),
            'upload_id' => Str::uuid(),
        ];
    }
    
    /**
     * Confirm video upload and create database entry (will trigger processing)
     */
    public function confirmVideoUpload(Subject $subject, User $user, array $uploadData): Video
    {
        $this->validateUploadPermissions($user);
        
        // Verify file exists in S3
        if (!Storage::disk('s3')->exists($uploadData['file_path'])) {
            throw new \Exception('File not found in storage. Upload may have failed.');
        }
        
        // Get file size from S3
        $fileSize = Storage::disk('s3')->size($uploadData['file_path']);
        
        $video = Video::create([
            'subject_id' => $subject->id,
            'title' => $uploadData['title'],
            'description' => $uploadData['description'] ?? null,
            'file_path' => $uploadData['file_path'],
            'file_size' => $fileSize,
            'duration' => null, // Will be set after processing
            'hls_path' => null, // Will be set after HLS transcoding
            'thumbnail_path' => null, // Will be set after thumbnail generation
            'processing_status' => 'pending',
            'uploaded_by' => $user->id,
            'is_active' => true,
        ]);
        
        // TODO: Dispatch video processing job
        // ProcessVideoJob::dispatch($video);
        
        return $video;
    }
    
    /**
     * Get notes for a subject with pagination
     */
    public function getSubjectNotes(Subject $subject, array $filters = []): array
    {
        $query = Note::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->with(['uploader:id,first_name,last_name'])
            ->orderBy('created_at', 'desc');
        
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        $notes = $query->paginate($filters['per_page'] ?? 15);
        
        return [
            'data' => $notes->items(),
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'total_pages' => $notes->lastPage(),
                'total_count' => $notes->total(),
                'per_page' => $notes->perPage(),
            ]
        ];
    }
    
    /**
     * Get videos for a subject with pagination
     */
    public function getSubjectVideos(Subject $subject, array $filters = []): array
    {
        $query = Video::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->with(['uploader:id,first_name,last_name'])
            ->orderBy('created_at', 'desc');
        
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        // Only return processed videos for students
        if (isset($filters['include_processing']) && !$filters['include_processing']) {
            $query->where('processing_status', 'completed');
        }
        
        $videos = $query->paginate($filters['per_page'] ?? 15);
        
        return [
            'data' => $videos->items(),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'total_pages' => $videos->lastPage(),
                'total_count' => $videos->total(),
                'per_page' => $videos->perPage(),
            ]
        ];
    }
    
    /**
     * Validate that user has upload permissions
     */
    private function validateUploadPermissions(User $user): void
    {
        $allowedRoles = ['admin', 'principal', 'hod', 'faculty'];
        
        if (!$user->hasAnyRole($allowedRoles)) {
            throw new \UnauthorizedHttpException('', 'Only faculty and administrators can upload content.');
        }
    }
    
    /**
     * Sanitize filename for safe storage
     */
    private function sanitizeFileName(string $filename): string
    {
        // Remove any path traversal attempts and dangerous characters
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        return $filename;
    }
    
    /**
     * Generate download URL for a note
     */
    public function generateNoteDownloadUrl(Note $note, User $user): string
    {
        // Check if user has access to this subject
        $this->validateSubjectAccess($note->subject, $user);
        
        // Generate temporary download URL (expires in 1 hour)
        return Storage::disk('s3')->temporaryUrl(
            $note->file_path,
            Carbon::now()->addHour()
        );
    }
    
    /**
     * Generate streaming URL for a video
     */
    public function generateVideoStreamUrl(Video $video, User $user): array
    {
        // Check if user has access to this subject
        $this->validateSubjectAccess($video->subject, $user);
        
        if ($video->processing_status !== 'completed') {
            throw new \Exception('Video is still processing and not available for streaming.');
        }
        
        $response = [
            'hls_url' => $video->hls_path ? Storage::disk('s3')->url($video->hls_path) : null,
            'thumbnail_url' => $video->thumbnail_path ? Storage::disk('s3')->url($video->thumbnail_path) : null,
            'duration' => $video->duration,
        ];
        
        return $response;
    }
    
    /**
     * Validate that user has access to a subject
     */
    private function validateSubjectAccess(Subject $subject, User $user): void
    {
        // Admins and principals have access to everything
        if ($user->hasAnyRole(['admin', 'principal'])) {
            return;
        }
        
        // HODs have access to their department's subjects
        if ($user->hasRole('hod') && $user->department_id === $subject->course->department_id) {
            return;
        }
        
        // Faculty have access to subjects they teach or in their department
        if ($user->hasRole('faculty') && $user->department_id === $subject->course->department_id) {
            return;
        }
        
        // Students have access to subjects in their department
        if ($user->hasRole('student') && $user->department_id === $subject->course->department_id) {
            return;
        }
        
        throw new \UnauthorizedHttpException('', 'You do not have access to this subject.');
    }
}