# 📁 BITFLOW LMS - COMPLETE FILE STRUCTURE & USAGE GUIDE

## 🏗️ PROJECT ROOT STRUCTURE

```
C:\Project\Bitflow_LMS\
├── 📂 lms-backend\          # Laravel Backend Application
├── 📂 frontend\             # React Frontend Application  
├── 📂 docs\                 # Project Documentation
├── 📄 README.md             # Main project documentation
├── 📄 SETUP_COMPLETE.md     # Setup completion guide
└── 📄 PROJECT_STATUS.md     # Basic status overview
```

---

## 🚀 BACKEND (Laravel) - COMPLETE FILE BREAKDOWN

### **📂 `lms-backend/` - Main Backend Directory**

#### **🏗️ Core Laravel Files**
```
├── 📄 artisan               # Laravel command-line tool
├── 📄 composer.json         # PHP dependencies & autoload
├── 📄 composer.lock         # Locked dependency versions
├── 📄 .env                  # Environment configuration (API keys, DB config)
├── 📄 .env.example          # Environment template
├── 📄 phpunit.xml           # PHP testing configuration
└── 📄 vite.config.js        # Frontend asset compilation
```

#### **📂 `app/` - Application Logic**

##### **🎮 Controllers (`app/Http/Controllers/Api/`)**
```
├── 📄 AuthController.php
│   ├── Purpose: User authentication & session management
│   ├── Methods: login(), logout(), me()
│   └── Features: JWT token management, role validation
│
├── 📄 UserController.php
│   ├── Purpose: User profile & management
│   ├── Methods: me(), index(), show(), update()
│   └── Features: User CRUD, profile updates
│
├── 📄 SubjectController.php
│   ├── Purpose: Subject management & access control
│   ├── Methods: index(), show()
│   └── Features: Role-based subject filtering, department access
│
└── 📄 ContentController.php
    ├── Purpose: File upload/download & content management
    ├── Methods: 
    │   ├── Notes: getNotes(), generateNoteUploadUrl(), confirmNoteUpload(), downloadNote(), deleteNote()
    │   └── Videos: getVideos(), generateVideoUploadUrl(), confirmVideoUpload(), streamVideo(), deleteVideo()
    └── Features: S3 integration ready, file validation, progress tracking
```

##### **🏛️ Models (`app/Models/`)**
```
├── 📄 User.php
│   ├── Purpose: User entity with roles & permissions
│   ├── Relationships: belongsTo(Department), hasMany(Notes, Videos, Assessments)
│   ├── Features: Spatie Permissions integration, role checking
│   └── Scopes: Active users, by department, by role
│
├── 📄 Department.php
│   ├── Purpose: Medical department entity
│   ├── Relationships: hasMany(Users, Courses)
│   └── Data: Anatomy, Physiology, Biochemistry, etc.
│
├── 📄 Course.php
│   ├── Purpose: MBBS year-wise course entity
│   ├── Relationships: belongsTo(Department), hasMany(Subjects)
│   └── Data: MBBS 1st Year, 2nd Year, 3rd Year, 4th Year, Internship
│
├── 📄 Subject.php
│   ├── Purpose: Individual subject entity
│   ├── Relationships: belongsTo(Course), hasMany(Notes, Videos, Assessments)
│   └── Features: Credit system, semester organization
│
├── 📄 Note.php
│   ├── Purpose: Study material file entity
│   ├── Relationships: belongsTo(Subject, User)
│   ├── Features: File metadata, upload tracking, version control
│   └── Attributes: file_path, file_size, file_type, download_count
│
├── 📄 Video.php
│   ├── Purpose: Video lecture entity
│   ├── Relationships: belongsTo(Subject, User)
│   ├── Features: Processing status, duration tracking, streaming
│   └── Attributes: file_path, duration, processing_status, view_count
│
├── 📄 Assessment.php
│   ├── Purpose: Quiz/exam entity
│   ├── Relationships: belongsTo(Subject), hasMany(Questions, Submissions)
│   ├── Features: Time limits, attempt tracking, auto-grading
│   └── Types: MCQ, SAQ, LAQ, practical
│
├── 📄 Question.php
│   ├── Purpose: Assessment question entity
│   ├── Relationships: belongsTo(Assessment)
│   ├── Features: Multiple question types, scoring, explanation
│   └── Types: multiple_choice, true_false, short_answer, essay
│
├── 📄 Submission.php
│   ├── Purpose: Student assessment submission entity
│   ├── Relationships: belongsTo(Assessment, User)
│   ├── Features: Attempt tracking, scoring, feedback
│   └── Attributes: answers, score, submitted_at, time_taken
│
├── 📄 Role.php & 📄 RoleDetail.php
│   ├── Purpose: User role system (extends Spatie)
│   ├── Features: Extended role information, dashboard routing
│   └── Data: student, faculty, hod, principal, admin
```

##### **🔧 Services (`app/Services/`)**
```
└── 📄 AuthService.php
    ├── Purpose: Authentication business logic
    ├── Methods: login(), logout(), me(), formatUserData()
    ├── Features: Token management, role-based response formatting
    └── Security: Password validation, account status checking
```

##### **⚙️ Console Commands (`app/Console/Commands/`)**
```
├── 📄 CheckUserData.php
│   ├── Command: `php artisan check:user-data {email}`
│   ├── Purpose: Debug user roles, departments, subject access
│   └── Usage: Troubleshooting authentication issues
│
└── 📄 ListTables.php
    ├── Command: `php artisan list:tables`
    ├── Purpose: List all database tables
    └── Usage: Database schema verification
```

#### **🗄️ Database (`database/`)**

##### **📋 Migrations (`database/migrations/`)**
```
├── 📄 *_create_users_table.php           # User accounts with MBBS fields
├── 📄 *_create_departments_table.php     # Medical departments
├── 📄 *_create_courses_table.php         # MBBS year courses
├── 📄 *_create_subjects_table.php        # Individual subjects
├── 📄 *_create_notes_table.php           # Study materials
├── 📄 *_create_videos_table.php          # Video lectures
├── 📄 *_create_assessments_table.php     # Quizzes & exams
├── 📄 *_create_questions_table.php       # Assessment questions
├── 📄 *_create_submissions_table.php     # Student submissions
├── 📄 *_permission_tables.php            # Spatie permission system
├── 📄 *_create_role_details_table.php    # Extended role information
└── 📄 *_create_additional_lms_tables.php # Advanced features
    ├── question_types              # Question type definitions
    ├── assessment_attempts         # Attempt tracking
    ├── student_answers            # Individual answers
    ├── assignments                # File-based assignments
    ├── assignment_submissions     # Assignment submissions
    ├── learning_progress          # Progress tracking
    ├── announcements              # System announcements
    ├── announcement_reads         # Read status tracking
    ├── messages                   # Direct messaging
    ├── notifications              # In-app notifications
    ├── system_settings            # Configuration
    └── audit_logs                 # Activity logging
```

##### **🌱 Seeders (`database/seeders/`)**
```
├── 📄 DatabaseSeeder.php           # Main seeder orchestrator
├── 📄 MBBSDataSeeder.php          # MBBS-specific sample data
└── 📄 RolesAndPermissionsSeeder.php # Role & permission setup
```

##### **🗄️ Database File**
```
└── 📄 database.sqlite              # SQLite database file (development)
    ├── Size: ~2MB with sample data
    ├── Tables: 25+ tables with relationships
    └── Data: Sample users, subjects, roles, permissions
```

#### **🛣️ Routes (`routes/`)**
```
└── 📄 api.php
    ├── Purpose: All API endpoint definitions
    ├── Middleware: Sanctum authentication, CORS
    ├── Structure:
    │   ├── Authentication routes (public)
    │   ├── Protected user routes
    │   ├── Subject management routes
    │   ├── Content management routes (notes/videos)
    │   └── Assessment routes (basic)
    └── Features: Route model binding, middleware groups
```

#### **🔧 Configuration (`config/`)**
```
├── 📄 auth.php          # Authentication configuration
├── 📄 database.php      # Database connections
├── 📄 cors.php          # CORS settings for API
├── 📄 permission.php    # Spatie permission config
└── 📄 sanctum.php       # API token configuration
```

#### **📦 Storage (`storage/`)**
```
├── 📂 app/
│   ├── 📂 public/       # Publicly accessible files
│   ├── 📂 notes/        # Uploaded note files
│   └── 📂 videos/       # Uploaded video files
├── 📂 logs/             # Application logs
└── 📂 framework/        # Laravel framework cache
```

---

## 🎨 FRONTEND (React) - COMPLETE FILE BREAKDOWN

### **📂 `frontend/` - Main Frontend Directory**

#### **🏗️ Core React Files**
```
├── 📄 package.json           # Dependencies & scripts
├── 📄 package-lock.json      # Locked dependency versions
├── 📄 tsconfig.json          # TypeScript configuration
├── 📄 .eslintrc.json         # Code quality rules
└── 📂 public/
    ├── 📄 index.html         # HTML template
    ├── 📄 favicon.ico        # Site icon
    └── 📄 manifest.json      # PWA configuration
```

#### **📂 `src/` - Application Source Code**

##### **🎯 Main Application Files**
```
├── 📄 index.tsx
│   ├── Purpose: React app entry point
│   ├── Features: StrictMode, root rendering
│   └── Imports: App component, global styles
│
├── 📄 App.tsx
│   ├── Purpose: Main application component & routing
│   ├── Features: 
│   │   ├── React Router configuration
│   │   ├── Authentication provider wrapper
│   │   ├── Protected route components
│   │   └── Role-based dashboard routing
│   ├── Routes:
│   │   ├── /login → LoginForm component
│   │   ├── /dashboard → DashboardRouter (role-based)
│   │   └── / → Redirect to dashboard
│   └── Components: AuthProvider, ProtectedRoute, DashboardRouter
│
└── 📄 App.css
    ├── Purpose: Global application styles
    ├── Features: CSS variables, layout utilities
    └── Theme: Medical professional color scheme
```

##### **🧩 Components (`src/components/`)**
```
├── 📄 LoginForm.tsx & 📄 LoginForm.css
│   ├── Purpose: User authentication interface
│   ├── Features:
│   │   ├── Email/password form validation
│   │   ├── Role-based redirect after login
│   │   ├── Loading states & error handling
│   │   ├── Demo credentials display
│   │   └── Responsive design
│   ├── State Management: Local state with hooks
│   ├── API Integration: useAuth hook
│   └── Navigation: useNavigate for redirects
│
└── 📄 LoadingSpinner.tsx & 📄 LoadingSpinner.css
    ├── Purpose: Loading state indicator
    ├── Features: Animated spinner, customizable size
    └── Usage: Throughout app for async operations
```

##### **🏠 Pages (`src/pages/`)**
```
├── 📄 StudentDashboard.tsx & 📄 StudentDashboard.css
│   ├── Purpose: Student portal interface
│   ├── Features:
│   │   ├── Subject selection dropdown
│   │   ├── Content tabs (Notes/Videos)
│   │   ├── File download functionality
│   │   ├── Video streaming integration
│   │   ├── Progress tracking display
│   │   ├── Search & filtering
│   │   └── Responsive grid layout
│   ├── State Management:
│   │   ├── subjects[], selectedSubject, activeTab
│   │   ├── notes[], videos[], loading, error
│   │   └── progress tracking state
│   ├── API Calls: getSubjects(), getSubjectNotes(), getSubjectVideos()
│   └── User Experience: Loading states, error boundaries, infinite scroll ready
│
└── 📄 FacultyDashboard.tsx & 📄 FacultyDashboard.css
    ├── Purpose: Faculty portal interface
    ├── Features:
    │   ├── Content management interface
    │   ├── File upload with progress tracking
    │   ├── Subject selection & filtering
    │   ├── Content listing & management
    │   ├── Upload tab (Notes/Videos)
    │   ├── File validation & preview
    │   ├── Delete functionality with confirmation
    │   └── Drag & drop upload (ready)
    ├── State Management:
    │   ├── uploadState (progress, error, status)
    │   ├── noteForm, videoForm state
    │   ├── content listings with refresh
    │   └── file upload progress tracking
    ├── API Calls:
    │   ├── Upload workflow: generateUploadUrl() → uploadToS3() → confirmUpload()
    │   ├── Content management: delete(), refresh()
    │   └── File streaming & download
    └── User Experience: Upload progress, success/error feedback, form validation
```

##### **⚡ Contexts (`src/contexts/`)**
```
└── 📄 AuthContext.tsx
    ├── Purpose: Global authentication state management
    ├── Features:
    │   ├── User authentication state
    │   ├── Token management & persistence
    │   ├── Auto-logout on token expiry
    │   ├── Role-based access control
    │   └── Error handling & user feedback
    ├── State Structure:
    │   ├── user: User | null
    │   ├── isAuthenticated: boolean
    │   ├── isLoading: boolean
    │   └── error: string | null
    ├── Actions:
    │   ├── login(email, password)
    │   ├── logout()
    │   ├── clearError()
    │   └── checkAuth()
    ├── Reducer Pattern: authReducer with action types
    └── Persistence: localStorage for token storage
```

##### **🔌 Services (`src/services/`)**
```
└── 📄 apiService.ts
    ├── Purpose: Centralized API communication layer
    ├── Features:
    │   ├── Axios instance with base configuration
    │   ├── Automatic authentication header management
    │   ├── Request/response interceptors
    │   ├── Error handling & token refresh
    │   ├── TypeScript interfaces for all entities
    │   └── Comprehensive logging & debugging
    ├── API Methods:
    │   ├── Authentication:
    │   │   ├── login(credentials) → {user, token}
    │   │   ├── logout() → void
    │   │   └── getCurrentUser() → User
    │   ├── Subjects:
    │   │   ├── getSubjects() → Subject[]
    │   │   └── getSubject(id) → Subject
    │   ├── Notes Management:
    │   │   ├── getSubjectNotes(subjectId, filters?) → PaginatedNotes
    │   │   ├── generateNoteUploadUrl(data) → {upload_url, file_key}
    │   │   ├── confirmNoteUpload(data) → Note
    │   │   ├── downloadNote(noteId) → download_url
    │   │   └── deleteNote(noteId) → void
    │   └── Videos Management:
    │       ├── getSubjectVideos(subjectId, filters?) → PaginatedVideos
    │       ├── generateVideoUploadUrl(data) → {upload_url, file_key}
    │       ├── confirmVideoUpload(data) → Video
    │       ├── getVideoStreamUrl(videoId) → stream_url
    │       └── deleteVideo(videoId) → void
    ├── TypeScript Interfaces:
    │   ├── User (with role_names, department, permissions)
    │   ├── Subject, Note, Video, Assessment
    │   ├── LoginCredentials, ApiResponse<T>
    │   └── Paginated response types
    └── Configuration:
        ├── Base URL: http://localhost:8000/api
        ├── Headers: Content-Type, Accept, Authorization
        └── Interceptors: 401 auto-logout, error logging
```

#### **📱 TypeScript Configuration**
```
├── 📄 tsconfig.json
│   ├── Purpose: TypeScript compilation rules
│   ├── Features: Strict mode, JSX support, path mapping
│   └── Target: ES2015 with modern React features
│
├── 📄 react-app-env.d.ts
│   ├── Purpose: React app type declarations
│   └── Features: Create React App type definitions
│
└── 📄 .eslintrc.json
    ├── Purpose: Code quality & style enforcement
    ├── Rules: React hooks, TypeScript, accessibility
    └── Custom Rules: Window object usage, dependency arrays
```

---

## 📚 DOCUMENTATION FILES

### **📂 `docs/` - Project Documentation**
```
├── 📄 SYSTEM_ARCHITECTURE.md
│   ├── Purpose: Complete system design & user flows
│   ├── Content: Role definitions, feature specifications, technical architecture
│   └── Audience: Developers, stakeholders, system architects
│
├── 📄 PROJECT_STATUS_REPORT.md
│   ├── Purpose: Comprehensive current status (this file)
│   ├── Content: Implementation status, file purposes, completion metrics
│   └── Audience: Project managers, developers, stakeholders
│
├── 📄 NODEJS_INSTALLATION.md
│   ├── Purpose: Node.js setup guide for development
│   ├── Content: Installation steps, PATH configuration, troubleshooting
│   └── Audience: Developers setting up the environment
│
└── 📄 DATABASE_SCHEMA.md (pending)
    ├── Purpose: Complete database documentation
    ├── Content: Table relationships, data types, constraints
    └── Audience: Database administrators, backend developers
```

### **📂 Root Documentation**
```
├── 📄 README.md
│   ├── Purpose: Main project overview & quick start
│   ├── Content: Project description, setup instructions, basic usage
│   └── Audience: New developers, project overview
│
├── 📄 SETUP_COMPLETE.md
│   ├── Purpose: Setup completion confirmation
│   ├── Content: Installation verification, next steps
│   └── Audience: Developers completing initial setup
│
└── 📄 PROJECT_STATUS.md
    ├── Purpose: High-level project status
    ├── Content: Basic completion status, key milestones
    └── Audience: Quick status check
```

---

## 🔧 CONFIGURATION FILES

### **Backend Configuration**
```
├── 📄 .env
│   ├── Purpose: Environment-specific configuration
│   ├── Content: Database URL, API keys, debug settings
│   ├── Security: Not versioned, contains sensitive data
│   └── Required Variables:
│       ├── APP_KEY, APP_URL, APP_ENV
│       ├── DB_CONNECTION, DB_DATABASE
│       ├── SANCTUM_STATEFUL_DOMAINS
│       └── CORS_ALLOWED_ORIGINS
│
├── 📄 composer.json
│   ├── Purpose: PHP dependencies & autoload configuration
│   ├── Key Dependencies:
│   │   ├── laravel/framework (^11.0)
│   │   ├── laravel/sanctum (authentication)
│   │   ├── spatie/laravel-permission (roles)
│   │   └── development tools (phpunit, faker)
│   └── Scripts: test, format, analyze
│
└── 📄 phpunit.xml
    ├── Purpose: PHP testing configuration
    ├── Features: Test database, coverage settings
    └── Environment: Testing-specific variables
```

### **Frontend Configuration**
```
├── 📄 package.json
│   ├── Purpose: Node.js dependencies & scripts
│   ├── Key Dependencies:
│   │   ├── react (^18.2.0), typescript (^4.9.5)
│   │   ├── react-router-dom (routing)
│   │   ├── axios (API calls)
│   │   └── @types/* (TypeScript definitions)
│   ├── Scripts:
│   │   ├── start: Development server
│   │   ├── build: Production build
│   │   ├── test: Run tests
│   │   └── eject: Expose configuration
│   └── Browserslist: Target browser support
│
└── 📄 tsconfig.json
    ├── Purpose: TypeScript compilation configuration
    ├── Settings: Strict mode, JSX, ES2015 target
    └── Paths: Module resolution, base URL
```

---

## 🎯 FILE USAGE MATRIX

### **🔄 Most Frequently Modified Files**
1. **`StudentDashboard.tsx`** - Student interface updates
2. **`FacultyDashboard.tsx`** - Faculty interface updates  
3. **`apiService.ts`** - API endpoint additions
4. **`AuthContext.tsx`** - Authentication logic updates
5. **`routes/api.php`** - New API route definitions

### **⚙️ Configuration Files (Modify with Caution)**
1. **`.env`** - Environment configuration
2. **`database.sqlite`** - Development database
3. **`composer.json`** - PHP dependencies
4. **`package.json`** - Node.js dependencies
5. **Migration files** - Database schema (never modify, create new)

### **📋 Files by Development Phase**

#### **Phase 1: Core Development**
- Controllers, Models, Services (Backend)
- Components, Pages, Contexts (Frontend)
- Basic migrations and seeders

#### **Phase 2: Feature Enhancement**
- Additional API endpoints
- Advanced UI components
- Assessment system implementation
- Progress tracking features

#### **Phase 3: Production Preparation**
- Environment configuration
- Security hardening
- Performance optimization
- Deployment scripts

---

## 🚀 DEVELOPMENT WORKFLOW

### **🔧 Backend Development**
1. **New Feature Development:**
   - Create migration: `php artisan make:migration`
   - Create model: `php artisan make:model`
   - Create controller: `php artisan make:controller`
   - Add routes in `routes/api.php`
   - Test with Postman/curl

2. **Database Changes:**
   - Always create new migrations
   - Never modify existing migration files
   - Run: `php artisan migrate`
   - Seed data: `php artisan db:seed`

### **🎨 Frontend Development**
1. **New Component Development:**
   - Create component in `src/components/`
   - Add corresponding CSS file
   - Export from index file (if needed)
   - Import and use in pages

2. **API Integration:**
   - Add method to `apiService.ts`
   - Define TypeScript interfaces
   - Implement in component with proper error handling
   - Test with actual backend endpoints

### **📋 Testing Strategy**
1. **Backend:** PHPUnit tests for APIs
2. **Frontend:** Jest + React Testing Library
3. **Integration:** Postman collections
4. **Manual:** User journey testing

---

This comprehensive file structure guide provides complete visibility into every file's purpose, usage, and relationship within the Bitflow LMS system. The project is well-organized with clear separation of concerns and follows industry best practices for both Laravel and React development.