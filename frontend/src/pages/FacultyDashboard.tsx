import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import apiService, { Subject, Note, Video } from '../services/apiService';
import './FacultyDashboard.css';

interface UploadState {
  uploading: boolean;
  progress: number;
  error: string | null;
}

const FacultyDashboard: React.FC = () => {
  const { user, logout } = useAuth();
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [selectedSubject, setSelectedSubject] = useState<Subject | null>(null);
  const [activeTab, setActiveTab] = useState<'notes' | 'videos' | 'upload'>('notes');
  const [uploadTab, setUploadTab] = useState<'note' | 'video'>('note');
  const [notes, setNotes] = useState<Note[]>([]);
  const [videos, setVideos] = useState<Video[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [uploadState, setUploadState] = useState<UploadState>({
    uploading: false,
    progress: 0,
    error: null,
  });

  // Form states
  const [noteForm, setNoteForm] = useState({
    title: '',
    description: '',
    file: null as File | null,
  });
  const [videoForm, setVideoForm] = useState({
    title: '',
    description: '',
    file: null as File | null,
  });

  useEffect(() => {
    loadSubjects();
  }, []);

  useEffect(() => {
    if (selectedSubject && activeTab !== 'upload') {
      if (activeTab === 'notes') {
        loadNotes();
      } else {
        loadVideos();
      }
    }
  }, [selectedSubject, activeTab]); // ESLint ignore - functions are stable

  const loadSubjects = async () => {
    try {
      setLoading(true);
      const subjectsData = await apiService.getSubjects();
      setSubjects(subjectsData);
      if (subjectsData.length > 0) {
        setSelectedSubject(subjectsData[0]);
      }
    } catch (error: any) {
      setError('Failed to load subjects');
    } finally {
      setLoading(false);
    }
  };

  const loadNotes = async () => {
    if (!selectedSubject) return;

    try {
      setLoading(true);
      const result = await apiService.getSubjectNotes(selectedSubject.id);
      setNotes(result.data);
    } catch (error: any) {
      setError('Failed to load notes');
    } finally {
      setLoading(false);
    }
  };

  const loadVideos = async () => {
    if (!selectedSubject) return;

    try {
      setLoading(true);
      const result = await apiService.getSubjectVideos(selectedSubject.id);
      setVideos(result.data);
    } catch (error: any) {
      setError('Failed to load videos');
    } finally {
      setLoading(false);
    }
  };

  const handleNoteUpload = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedSubject || !noteForm.file || !noteForm.title) return;

    try {
      setUploadState({ uploading: true, progress: 0, error: null });

      // Generate upload URL
      const { file_key } = await apiService.generateNoteUploadUrl(
        selectedSubject.id,
        {
          filename: noteForm.file.name,
          content_type: noteForm.file.type,
          file_size: noteForm.file.size,
        }
      );

      // Simulate upload progress
      const progressInterval = setInterval(() => {
        setUploadState(prev => ({
          ...prev,
          progress: Math.min(prev.progress + 20, 90),
        }));
      }, 500);

      // Upload file to S3 (simulated)
      await new Promise(resolve => setTimeout(resolve, 2500));
      clearInterval(progressInterval);

      setUploadState(prev => ({ ...prev, progress: 95 }));

      // Confirm upload
      await apiService.confirmNoteUpload(selectedSubject.id, {
        file_key,
        title: noteForm.title,
        description: noteForm.description,
      });

      setUploadState({ uploading: false, progress: 100, error: null });
      
      // Reset form
      setNoteForm({ title: '', description: '', file: null });
      
      // Reload notes
      if (activeTab === 'notes') {
        loadNotes();
      }

      alert('Note uploaded successfully!');
    } catch (error: any) {
      setUploadState({
        uploading: false,
        progress: 0,
        error: error.response?.data?.message || 'Upload failed',
      });
    }
  };

  const handleVideoUpload = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedSubject || !videoForm.file || !videoForm.title) return;

    try {
      setUploadState({ uploading: true, progress: 0, error: null });

      // Generate upload URL
      const { file_key } = await apiService.generateVideoUploadUrl(
        selectedSubject.id,
        {
          filename: videoForm.file.name,
          content_type: videoForm.file.type,
          file_size: videoForm.file.size,
        }
      );

      // Simulate upload progress
      const progressInterval = setInterval(() => {
        setUploadState(prev => ({
          ...prev,
          progress: Math.min(prev.progress + 15, 85),
        }));
      }, 800);

      // Upload file to S3 (simulated)
      await new Promise(resolve => setTimeout(resolve, 4000));
      clearInterval(progressInterval);

      setUploadState(prev => ({ ...prev, progress: 95 }));

      // Confirm upload
      await apiService.confirmVideoUpload(selectedSubject.id, {
        file_key,
        title: videoForm.title,
        description: videoForm.description,
      });

      setUploadState({ uploading: false, progress: 100, error: null });
      
      // Reset form
      setVideoForm({ title: '', description: '', file: null });
      
      // Reload videos
      if (activeTab === 'videos') {
        loadVideos();
      }

      alert('Video uploaded successfully and queued for processing!');
    } catch (error: any) {
      setUploadState({
        uploading: false,
        progress: 0,
        error: error.response?.data?.message || 'Upload failed',
      });
    }
  };

  const handleDeleteNote = async (noteId: number) => {
    if (!window.confirm('Are you sure you want to delete this note?')) return;

    try {
      await apiService.deleteNote(noteId);
      loadNotes();
      alert('Note deleted successfully!');
    } catch (error: any) {
      setError('Failed to delete note');
    }
  };

  const handleDeleteVideo = async (videoId: number) => {
    if (!window.confirm('Are you sure you want to delete this video?')) return;

    try {
      await apiService.deleteVideo(videoId);
      loadVideos();
      alert('Video deleted successfully!');
    } catch (error: any) {
      setError('Failed to delete video');
    }
  };

  const formatFileSize = (bytes: number): string => {
    const sizes = ['B', 'KB', 'MB', 'GB'];
    if (bytes === 0) return '0 B';
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${sizes[i]}`;
  };

  const formatDuration = (seconds?: number): string => {
    if (!seconds) return '00:00:00';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  };

  if (loading && subjects.length === 0) {
    return (
      <div className="loading-container">
        <p>Loading your subjects...</p>
      </div>
    );
  }

  return (
    <div className="faculty-dashboard">
      {/* Header */}
      <div className="dashboard-header">
        <div className="header-content">
          <h1>Faculty Dashboard</h1>
          <div className="user-info">
            <p>Welcome, {user?.first_name} {user?.last_name}</p>
            <p>Department: {user?.department?.name}</p>
          </div>
        </div>
        <button className="logout-btn" onClick={logout}>
          Logout
        </button>
      </div>

      {/* Subject Selector */}
      <div className="subject-selector">
        <label htmlFor="subject-select">Select Subject:</label>
        <select
          id="subject-select"
          value={selectedSubject?.id || ''}
          onChange={(e) => {
            const subject = subjects.find(s => s.id === parseInt(e.target.value));
            setSelectedSubject(subject || null);
          }}
        >
          <option value="">Select a subject...</option>
          {subjects.map(subject => (
            <option key={subject.id} value={subject.id}>
              {subject.code}: {subject.title}
            </option>
          ))}
        </select>
      </div>

      {selectedSubject && (
        <div className="subject-content">
          {/* Subject Info */}
          <div className="subject-info">
            <h2>{selectedSubject.code}: {selectedSubject.title}</h2>
            <p>Course: {selectedSubject.course.name}</p>
            <p>Department: {selectedSubject.course.department.name}</p>
          </div>

          {/* Tab Navigation */}
          <div className="tab-navigation">
            <button
              className={`tab ${activeTab === 'notes' ? 'active' : ''}`}
              onClick={() => setActiveTab('notes')}
            >
              Manage Notes ({notes.length})
            </button>
            <button
              className={`tab ${activeTab === 'videos' ? 'active' : ''}`}
              onClick={() => setActiveTab('videos')}
            >
              Manage Videos ({videos.length})
            </button>
            <button
              className={`tab ${activeTab === 'upload' ? 'active' : ''}`}
              onClick={() => setActiveTab('upload')}
            >
              Upload Content
            </button>
          </div>

          {/* Content Area */}
          <div className="content-area">
            {/* Upload Tab */}
            {activeTab === 'upload' && (
              <div className="upload-section">
                <h3>Upload Content</h3>

                {/* Upload Type Selector */}
                <div className="upload-tabs">
                  <button
                    className={`upload-tab ${uploadTab === 'note' ? 'active' : ''}`}
                    onClick={() => setUploadTab('note')}
                  >
                    Upload Note
                  </button>
                  <button
                    className={`upload-tab ${uploadTab === 'video' ? 'active' : ''}`}
                    onClick={() => setUploadTab('video')}
                  >
                    Upload Video
                  </button>
                </div>

                {/* Upload Progress */}
                {uploadState.uploading && (
                  <div className="upload-progress">
                    <p>Uploading... {uploadState.progress}%</p>
                    <div className="progress-bar">
                      <div
                        className="progress-fill"
                        style={{ width: `${uploadState.progress}%` }}
                      ></div>
                    </div>
                  </div>
                )}

                {/* Upload Error */}
                {uploadState.error && (
                  <div className="error-message">
                    {uploadState.error}
                  </div>
                )}

                {/* Upload Forms */}
                {uploadTab === 'note' ? (
                  <form className="upload-form" onSubmit={handleNoteUpload}>
                    <div className="form-group">
                      <label>Note Title *</label>
                      <input
                        type="text"
                        value={noteForm.title}
                        onChange={(e) => setNoteForm({ ...noteForm, title: e.target.value })}
                        required
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <div className="form-group">
                      <label>Description</label>
                      <textarea
                        value={noteForm.description}
                        onChange={(e) => setNoteForm({ ...noteForm, description: e.target.value })}
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <div className="form-group">
                      <label>File * (PDF, DOC, DOCX, PPT, PPTX, TXT)</label>
                      <input
                        type="file"
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.txt"
                        onChange={(e) => setNoteForm({ ...noteForm, file: e.target.files?.[0] || null })}
                        required
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <button
                      type="submit"
                      className="upload-btn"
                      disabled={uploadState.uploading || !noteForm.title || !noteForm.file}
                    >
                      {uploadState.uploading ? 'Uploading...' : 'Upload Note'}
                    </button>
                  </form>
                ) : (
                  <form className="upload-form" onSubmit={handleVideoUpload}>
                    <div className="form-group">
                      <label>Video Title *</label>
                      <input
                        type="text"
                        value={videoForm.title}
                        onChange={(e) => setVideoForm({ ...videoForm, title: e.target.value })}
                        required
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <div className="form-group">
                      <label>Description</label>
                      <textarea
                        value={videoForm.description}
                        onChange={(e) => setVideoForm({ ...videoForm, description: e.target.value })}
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <div className="form-group">
                      <label>Video File * (MP4, MOV, AVI, MKV, WEBM - Max 5GB)</label>
                      <input
                        type="file"
                        accept=".mp4,.mov,.avi,.mkv,.webm"
                        onChange={(e) => setVideoForm({ ...videoForm, file: e.target.files?.[0] || null })}
                        required
                        disabled={uploadState.uploading}
                      />
                    </div>
                    <button
                      type="submit"
                      className="upload-btn"
                      disabled={uploadState.uploading || !videoForm.title || !videoForm.file}
                    >
                      {uploadState.uploading ? 'Uploading...' : 'Upload Video'}
                    </button>
                  </form>
                )}
              </div>
            )}

            {/* Notes/Videos Content */}
            {activeTab !== 'upload' && (
              <>
                {loading ? (
                  <div className="loading">Loading content...</div>
                ) : error ? (
                  <div className="error-message">
                    <p>Error: {error}</p>
                    <button onClick={() => setError(null)}>Clear Error</button>
                  </div>
                ) : (
                  <div className="content-grid">
                    {activeTab === 'notes' && notes.length === 0 && (
                      <div className="no-content">No notes uploaded yet.</div>
                    )}
                    {activeTab === 'videos' && videos.length === 0 && (
                      <div className="no-content">No videos uploaded yet.</div>
                    )}

                    {activeTab === 'notes' &&
                      notes.map(note => (
                        <div key={note.id} className="content-item">
                          <h4>{note.title}</h4>
                          {note.description && <p>{note.description}</p>}
                          <div className="content-meta">
                            <span>Uploaded by: {note.uploader.first_name} {note.uploader.last_name}</span>
                            <span>Size: {formatFileSize(note.file_size)} | Type: {note.file_type.toUpperCase()}</span>
                          </div>
                          <div className="content-actions">
                            <button
                              className="delete-btn"
                              onClick={() => handleDeleteNote(note.id)}
                            >
                              Delete
                            </button>
                          </div>
                        </div>
                      ))}

                    {activeTab === 'videos' &&
                      videos.map(video => (
                        <div key={video.id} className="content-item">
                          <h4>{video.title}</h4>
                          {video.description && <p>{video.description}</p>}
                          <div className="content-meta">
                            <span>Uploaded by: {video.uploader.first_name} {video.uploader.last_name}</span>
                            <span>Duration: {formatDuration(video.duration)} | Size: {formatFileSize(video.file_size)}</span>
                          </div>
                          <div className="video-status">
                            <span className={`status-badge ${video.processing_status}`}>
                              {video.processing_status.toUpperCase()}
                            </span>
                          </div>
                          <div className="content-actions">
                            <button
                              className="delete-btn"
                              onClick={() => handleDeleteVideo(video.id)}
                            >
                              Delete
                            </button>
                          </div>
                        </div>
                      ))}
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default FacultyDashboard;