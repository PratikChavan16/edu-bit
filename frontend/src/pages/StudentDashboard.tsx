import React, { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import apiService, { Subject, Note, Video } from '../services/apiService';
import './StudentDashboard.css';

const StudentDashboard: React.FC = () => {
  const { user, logout } = useAuth();
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [selectedSubject, setSelectedSubject] = useState<Subject | null>(null);
  const [activeTab, setActiveTab] = useState<'notes' | 'videos'>('notes');
  const [notes, setNotes] = useState<Note[]>([]);
  const [videos, setVideos] = useState<Video[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadSubjects();
  }, []);

  useEffect(() => {
    if (selectedSubject) {
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
      setError(null);
      console.log('Loading subjects...');
      const subjectsData = await apiService.getSubjects();
      console.log('Subjects loaded:', subjectsData);
      setSubjects(subjectsData);
      if (subjectsData.length > 0) {
        setSelectedSubject(subjectsData[0]);
      }
    } catch (error: any) {
      console.error('Failed to load subjects:', error);
      console.error('Error response:', error.response?.data);
      console.error('Error status:', error.response?.status);
      setError('Failed to load subjects: ' + (error.response?.data?.message || error.message));
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

  const handleDownloadNote = async (noteId: number) => {
    try {
      const downloadUrl = await apiService.downloadNote(noteId);
      window.open(downloadUrl, '_blank');
    } catch (error: any) {
      setError('Failed to download note');
    }
  };

  const handlePlayVideo = async (videoId: number) => {
    try {
      const streamUrl = await apiService.getVideoStreamUrl(videoId);
      window.open(streamUrl, '_blank');
    } catch (error: any) {
      setError('Failed to play video');
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

  if (error && subjects.length === 0) {
    return (
      <div className="error-container">
        <p>Error: {error}</p>
        <button onClick={() => { setError(null); loadSubjects(); }}>
          Retry
        </button>
      </div>
    );
  }

  return (
    <div className="student-dashboard">
      {/* Header */}
      <div className="dashboard-header">
        <div className="header-content">
          <h1>Student Dashboard</h1>
          <div className="user-info">
            <p>Welcome, {user?.first_name} {user?.last_name}</p>
            <p>Department: {user?.department?.name} | Year: {user?.current_year}</p>
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
              Notes ({notes.length})
            </button>
            <button
              className={`tab ${activeTab === 'videos' ? 'active' : ''}`}
              onClick={() => setActiveTab('videos')}
            >
              Videos ({videos.length})
            </button>
          </div>

          {/* Content Area */}
          <div className="content-area">
            {loading ? (
              <div className="loading">Loading content...</div>
            ) : error ? (
              <div className="error-message">
                <p>Error: {error}</p>
                <button onClick={() => setError(null)}>Clear Error</button>
              </div>
            ) : (
              <>
                {activeTab === 'notes' && (
                  <div className="content-grid">
                    {notes.length === 0 ? (
                      <div className="no-content">No notes available for this subject.</div>
                    ) : (
                      notes.map(note => (
                        <div key={note.id} className="content-item">
                          <h4>{note.title}</h4>
                          {note.description && <p>{note.description}</p>}
                          <div className="content-meta">
                            <span>By: {note.uploader.first_name} {note.uploader.last_name}</span>
                            <span>Size: {formatFileSize(note.file_size)} | Type: {note.file_type.toUpperCase()}</span>
                          </div>
                          <div className="content-actions">
                            <button
                              className="download-btn"
                              onClick={() => handleDownloadNote(note.id)}
                            >
                              Download
                            </button>
                          </div>
                        </div>
                      ))
                    )}
                  </div>
                )}

                {activeTab === 'videos' && (
                  <div className="content-grid">
                    {videos.length === 0 ? (
                      <div className="no-content">No videos available for this subject.</div>
                    ) : (
                      videos.map(video => (
                        <div key={video.id} className="content-item">
                          <h4>{video.title}</h4>
                          {video.description && <p>{video.description}</p>}
                          <div className="content-meta">
                            <span>By: {video.uploader.first_name} {video.uploader.last_name}</span>
                            <span>Duration: {formatDuration(video.duration)} | Size: {formatFileSize(video.file_size)}</span>
                          </div>
                          <div className="video-status">
                            <span className={`status-badge ${video.processing_status}`}>
                              {video.processing_status.toUpperCase()}
                            </span>
                          </div>
                          <div className="content-actions">
                            {video.processing_status === 'completed' ? (
                              <button
                                className="play-btn"
                                onClick={() => handlePlayVideo(video.id)}
                              >
                                Play Video
                              </button>
                            ) : (
                              <button className="play-btn" disabled>
                                Processing...
                              </button>
                            )}
                          </div>
                        </div>
                      ))
                    )}
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

export default StudentDashboard;