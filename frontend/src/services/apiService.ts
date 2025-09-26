import axios, { AxiosInstance, AxiosResponse } from 'axios';

// Types for API responses
interface ApiResponse<T = any> {
  message: string;
  data?: T;
  error?: string;
}

interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  current_year?: number;
  enrollment_number?: string;
  photo_url?: string;
  is_active: boolean;
  department?: {
    id: number;
    name: string;
    code: string;
    description?: string;
  };
  role: {
    id: number;
    name: string;
    display_name: string;
    description?: string;
  };
  permissions: string[];
  role_names: string[];
}

interface LoginCredentials {
  email: string;
  password: string;
}

interface Subject {
  id: number;
  code: string;
  title: string;
  description?: string;
  course: {
    id: number;
    name: string;
    department: {
      id: number;
      name: string;
    };
  };
}

interface Note {
  id: number;
  title: string;
  description?: string;
  file_path: string;
  file_size: number;
  file_type: string;
  uploader: {
    first_name: string;
    last_name: string;
  };
  created_at: string;
}

interface Video {
  id: number;
  title: string;
  description?: string;
  file_path: string;
  file_size: number;
  duration?: number;
  processing_status: 'pending' | 'processing' | 'completed' | 'failed';
  uploader: {
    first_name: string;
    last_name: string;
  };
  created_at: string;
}

class ApiService {
  private api: AxiosInstance;
  private token: string | null = null;

  constructor() {
    this.api = axios.create({
      baseURL: 'http://localhost:8000/api',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Load token from localStorage on initialization
    this.token = localStorage.getItem('auth_token');
    if (this.token) {
      this.setAuthToken(this.token);
    }

    // Response interceptor for handling errors
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          this.logout();
        }
        return Promise.reject(error);
      }
    );
  }

  private setAuthToken(token: string) {
    this.token = token;
    this.api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    localStorage.setItem('auth_token', token);
  }

  private removeAuthToken() {
    this.token = null;
    delete this.api.defaults.headers.common['Authorization'];
    localStorage.removeItem('auth_token');
  }

  // Authentication
  async login(credentials: LoginCredentials): Promise<{ user: User; token: string }> {
    const response: AxiosResponse<ApiResponse<{ user: User; token: string }>> = 
      await this.api.post('/auth/login', credentials);
    
    const { user, token } = response.data.data!;
    this.setAuthToken(token);
    return { user, token };
  }

  async logout(): Promise<void> {
    try {
      await this.api.post('/auth/logout');
    } catch (error) {
      // Handle logout error silently
    } finally {
      this.removeAuthToken();
    }
  }

  async getCurrentUser(): Promise<User> {
    const response: AxiosResponse<ApiResponse<User>> = 
      await this.api.get('/auth/me');
    return response.data.data!;
  }

  // Subjects
  async getSubjects(): Promise<Subject[]> {
    console.log('Getting subjects with token:', this.token ? 'Token exists' : 'No token');
    console.log('Authorization header:', this.api.defaults.headers.common['Authorization']);
    
    const response: AxiosResponse<ApiResponse<Subject[]>> = 
      await this.api.get('/subjects');
    return response.data.data!;
  }

  async getSubject(id: number): Promise<Subject> {
    const response: AxiosResponse<ApiResponse<Subject>> = 
      await this.api.get(`/subjects/${id}`);
    return response.data.data!;
  }

  // Notes
  async getSubjectNotes(subjectId: number, filters?: { search?: string; per_page?: number }): Promise<{
    data: Note[];
    current_page: number;
    total: number;
    per_page: number;
  }> {
    const params = new URLSearchParams();
    if (filters?.search) params.append('search', filters.search);
    if (filters?.per_page) params.append('per_page', filters.per_page.toString());

    const response: AxiosResponse<ApiResponse<{
      data: Note[];
      current_page: number;
      total: number;
      per_page: number;
    }>> = await this.api.get(`/subjects/${subjectId}/notes?${params}`);
    
    return response.data.data!;
  }

  async generateNoteUploadUrl(subjectId: number, data: {
    filename: string;
    content_type: string;
    file_size: number;
  }): Promise<{
    upload_url: string;
    file_key: string;
  }> {
    const response: AxiosResponse<ApiResponse<{
      upload_url: string;
      file_key: string;
    }>> = await this.api.post(`/subjects/${subjectId}/notes/upload-url`, data);
    
    return response.data.data!;
  }

  async confirmNoteUpload(subjectId: number, data: {
    file_key: string;
    title: string;
    description?: string;
  }): Promise<Note> {
    const response: AxiosResponse<ApiResponse<Note>> = 
      await this.api.post(`/subjects/${subjectId}/notes/confirm-upload`, data);
    return response.data.data!;
  }

  async downloadNote(noteId: number): Promise<string> {
    const response: AxiosResponse<ApiResponse<{ download_url: string }>> = 
      await this.api.get(`/notes/${noteId}/download`);
    return response.data.data!.download_url;
  }

  async deleteNote(noteId: number): Promise<void> {
    await this.api.delete(`/notes/${noteId}`);
  }

  // Videos
  async getSubjectVideos(subjectId: number, filters?: { search?: string; per_page?: number }): Promise<{
    data: Video[];
    current_page: number;
    total: number;
    per_page: number;
  }> {
    const params = new URLSearchParams();
    if (filters?.search) params.append('search', filters.search);
    if (filters?.per_page) params.append('per_page', filters.per_page.toString());

    const response: AxiosResponse<ApiResponse<{
      data: Video[];
      current_page: number;
      total: number;
      per_page: number;
    }>> = await this.api.get(`/subjects/${subjectId}/videos?${params}`);
    
    return response.data.data!;
  }

  async generateVideoUploadUrl(subjectId: number, data: {
    filename: string;
    content_type: string;
    file_size: number;
  }): Promise<{
    upload_url: string;
    file_key: string;
  }> {
    const response: AxiosResponse<ApiResponse<{
      upload_url: string;
      file_key: string;
    }>> = await this.api.post(`/subjects/${subjectId}/videos/upload-url`, data);
    
    return response.data.data!;
  }

  async confirmVideoUpload(subjectId: number, data: {
    file_key: string;
    title: string;
    description?: string;
  }): Promise<Video> {
    const response: AxiosResponse<ApiResponse<Video>> = 
      await this.api.post(`/subjects/${subjectId}/videos/confirm-upload`, data);
    return response.data.data!;
  }

  async getVideoStreamUrl(videoId: number): Promise<string> {
    const response: AxiosResponse<ApiResponse<{ stream_url: string }>> = 
      await this.api.get(`/videos/${videoId}/stream`);
    return response.data.data!.stream_url;
  }

  async deleteVideo(videoId: number): Promise<void> {
    await this.api.delete(`/videos/${videoId}`);
  }

  // Utility methods
  isAuthenticated(): boolean {
    return !!this.token;
  }

  getToken(): string | null {
    return this.token;
  }
}

// Export singleton instance
export const apiService = new ApiService();
export default apiService;

// Export types
export type {
  User,
  LoginCredentials,
  Subject,
  Note,
  Video,
  ApiResponse
};