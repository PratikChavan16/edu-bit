# Database Schema Draft (MySQL 8)

> Iterative draft; will refine as entities gain detail. Naming: snake_case tables, singular where natural for pivot tables, plural otherwise. All tables have: `id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`, `created_at`, `updated_at` (unless stated), soft deletes where appropriate.

## 1. Identity & RBAC
### users
- id
- name
- email (unique, indexed)
- email_verified_at (nullable)
- phone (nullable, indexed)
- password (hashed)
- status ENUM('active','inactive','pending','locked')
- avatar_url (nullable)
- dob DATE (nullable)
- gender ENUM('male','female','other','unknown') (nullable)
- address TEXT (nullable)
- guardian_contact JSON (nullable)
- remember_token (nullable)

Indexes: (email), (phone), composite on (status)

### roles
- id
- name (unique) e.g., student, teacher, hod, principal, admin
- description (nullable)

### permissions
- id
- code (unique) e.g., content.create, assessment.publish
- description (nullable)

### role_permissions (pivot)
- role_id FK roles.id
- permission_id FK permissions.id
- UNIQUE(role_id, permission_id)

### user_roles (pivot)
- user_id FK users.id
- role_id FK roles.id
- UNIQUE(user_id, role_id)

## 2. Academic Structure
### departments
- id
- name (unique)
- code (unique)
- hod_user_id FK users.id (nullable)

### programs
- id
- name
- code (unique)
- duration_years TINYINT
- department_id FK departments.id
- UNIQUE(code)

### academic_years
- id
- label (e.g., 2025-2026) UNIQUE
- starts_on DATE
- ends_on DATE

### cohorts
- id
- program_id FK programs.id
- academic_year_id FK academic_years.id
- year_number TINYINT (1..N)
- section VARCHAR(10) (nullable)
- UNIQUE(program_id, academic_year_id, year_number, section)

### subjects
- id
- name
- code (unique)
- department_id FK departments.id
- credits DECIMAL(4,2) (nullable)

### competencies
- id
- subject_id FK subjects.id
- code
- description
- UNIQUE(subject_id, code)

### subject_offerings
- id
- subject_id FK subjects.id
- cohort_id FK cohorts.id
- teacher_user_id FK users.id
- active BOOLEAN default true
- UNIQUE(subject_id, cohort_id, teacher_user_id)

## 3. Content Delivery
### content_items
- id
- subject_offering_id FK subject_offerings.id
- teacher_user_id FK users.id
- type ENUM('note','video')
- title
- description TEXT (nullable)
- storage_path (for file or video manifest)
- status ENUM('draft','published','archived')
- version INT default 1
- published_at DATETIME (nullable)
- metadata JSON (nullable) (e.g., duration for video)
- checksum VARCHAR(128) (nullable)
Indexes: (subject_offering_id, status), (teacher_user_id, status), FULLTEXT(title, description) (InnoDB FULLTEXT)

### content_versions (optional future)
- id
- content_item_id FK content_items.id
- version INT
- diff_summary TEXT (nullable)
- storage_path
- created_at

## 4. Assessments & Questions
### assessments
- id
- subject_offering_id FK subject_offerings.id
- title
- type ENUM('mcq','short','long','mixed')
- status ENUM('draft','published','archived')
- instructions TEXT (nullable)
- open_at DATETIME (nullable)
- close_at DATETIME (nullable)
- duration_minutes INT (nullable)
- attempt_limit TINYINT (nullable)
- randomize BOOLEAN default false
- negative_marking BOOLEAN default false
- total_marks DECIMAL(6,2) (nullable)
- published_at DATETIME (nullable)

### questions
- id
- assessment_id FK assessments.id
- type ENUM('mcq','short','long')
- text TEXT
- competency_id FK competencies.id (nullable)
- difficulty ENUM('easy','medium','hard') (nullable)
- marks DECIMAL(5,2) (nullable)
- order_index INT
- metadata JSON (nullable)
- UNIQUE(assessment_id, order_index)

### question_options
- id
- question_id FK questions.id
- text TEXT
- is_correct BOOLEAN default false
- order_index INT
- UNIQUE(question_id, order_index)

### submissions
- id
- assessment_id FK assessments.id
- student_user_id FK users.id
- started_at DATETIME (nullable)
- submitted_at DATETIME (nullable)
- status ENUM('in_progress','submitted','graded','returned')
- auto_score DECIMAL(6,2) (nullable)
- final_score DECIMAL(6,2) (nullable)
- attempt_number TINYINT default 1
- UNIQUE(assessment_id, student_user_id, attempt_number)
Indexes: (assessment_id, status), (student_user_id, status)

### answers
- id
- submission_id FK submissions.id
- question_id FK questions.id
- response_text TEXT (nullable)
- response_media_path (nullable)
- auto_mark DECIMAL(5,2) (nullable)
- manual_mark DECIMAL(5,2) (nullable)
- feedback TEXT (nullable)
- UNIQUE(submission_id, question_id)

## 5. Grading & Feedback
### grading_rubrics (future)
- id
- subject_id FK subjects.id
- name
- structure JSON

## 6. Attendance
### timetable_slots
- id
- subject_offering_id FK subject_offerings.id
- weekday TINYINT (0=Sun)
- start_time TIME
- end_time TIME
- room VARCHAR(50) (nullable)
- active BOOLEAN default true
- UNIQUE(subject_offering_id, weekday, start_time)

### attendance_records
- id
- subject_offering_id FK subject_offerings.id
- student_user_id FK users.id
- date DATE
- status ENUM('present','absent','late','excused')
- captured_at DATETIME
- method ENUM('barcode','manual')
- UNIQUE(subject_offering_id, student_user_id, date)
Indexes: (date, status), (student_user_id, date)

### barcode_tokens
- id
- user_id FK users.id
- token_hash VARCHAR(128)
- expires_at DATETIME
- used_at DATETIME (nullable)
- purpose ENUM('attendance')
- UNIQUE(token_hash)
Indexes: (user_id, expires_at)

## 7. Documents & Compliance
### document_requirements
- id
- program_id FK programs.id (nullable)
- year_number TINYINT (nullable)
- name
- instructions TEXT (nullable)
- mandatory BOOLEAN default true
- UNIQUE(program_id, year_number, name)

### document_submissions
- id
- requirement_id FK document_requirements.id
- student_user_id FK users.id
- file_path
- status ENUM('pending','under_review','approved','rejected')
- reviewer_user_id FK users.id (nullable)
- reviewed_at DATETIME (nullable)
- rejection_reason TEXT (nullable)
- version INT default 1
Indexes: (student_user_id, status)

## 8. Fees & Billing
### fee_structures
- id
- program_id FK programs.id
- year_number TINYINT
- data JSON (component breakdown)
- active BOOLEAN default true
- UNIQUE(program_id, year_number, active)

### invoices
- id
- student_user_id FK users.id
- fee_structure_id FK fee_structures.id
- amount DECIMAL(10,2)
- due_date DATE
- status ENUM('unpaid','partial','paid','overdue')
- paid_at DATETIME (nullable)
- reference_no VARCHAR(64) (nullable)
Indexes: (student_user_id, status), (due_date, status)

### payments (optional future detail)
- id
- invoice_id FK invoices.id
- amount DECIMAL(10,2)
- paid_at DATETIME
- method ENUM('cash','card','online','upi')
- txn_ref VARCHAR(128)

## 9. Announcements & Communication
### announcements
- id
- scope_type ENUM('cohort','department','institution','teachers')
- scope_id BIGINT (nullable)
- author_user_id FK users.id
- title
- body TEXT
- status ENUM('draft','published','archived')
- pinned BOOLEAN default false
- publish_at DATETIME (nullable)
- published_at DATETIME (nullable)
Indexes: (scope_type, scope_id, status), FULLTEXT(title, body)

### announcement_comments
- id
- announcement_id FK announcements.id
- user_id FK users.id
- body TEXT
- private_to_author BOOLEAN default true
- created_at
Indexes: (announcement_id), (user_id)

## 10. Analytics & Gamification
### badges
- id
- code UNIQUE
- name
- description
- rule JSON (criteria definition)
- active BOOLEAN default true

### user_badges
- id
- user_id FK users.id
- badge_id FK badges.id
- earned_at DATETIME
- progress JSON (nullable)
- UNIQUE(user_id, badge_id)

### alerts
- id
- type VARCHAR(64)
- severity ENUM('info','warning','critical')
- target_scope_type ENUM('student','cohort','department','institution')
- target_scope_id BIGINT (nullable)
- metric_ref VARCHAR(64)
- threshold_value DECIMAL(10,2) (nullable)
- current_value DECIMAL(10,2) (nullable)
- state ENUM('open','ack','resolved')
- owner_user_id FK users.id (nullable)
- resolved_at DATETIME (nullable)
Indexes: (state), (type, state)

### workload_snapshots
- id
- teacher_user_id FK users.id
- snapshot_date DATE
- lectures_conducted INT
- lectures_planned INT
- grading_pending INT
- avg_grading_delay_hours DECIMAL(6,2)
- UNIQUE(teacher_user_id, snapshot_date)

## 11. Favorites & Search
### favorites
- id
- user_id FK users.id
- entity_type ENUM('content','assessment','announcement')
- entity_id BIGINT
- created_at DATETIME
- UNIQUE(user_id, entity_type, entity_id)
Indexes: (user_id, entity_type)

## 12. Calendar & Events
### calendar_events
- id
- type ENUM('holiday','exam','fee_deadline','custom')
- title
- description TEXT (nullable)
- start_at DATETIME
- end_at DATETIME (nullable)
- scope_type ENUM('institution','department','cohort','user')
- scope_id BIGINT (nullable)
Indexes: (scope_type, scope_id, start_at)

## 13. Notifications
### notifications
- id
- user_id FK users.id
- type VARCHAR(64)
- payload JSON
- read_at DATETIME (nullable)
- created_at
Indexes: (user_id, read_at), (type)

## 14. Security & Audit
### audit_logs
- id
- actor_user_id FK users.id (nullable for system)
- action VARCHAR(128)
- entity_type VARCHAR(64)
- entity_id BIGINT (nullable)
- before_json JSON (nullable)
- after_json JSON (nullable)
- ip_address VARCHAR(64) (nullable)
- user_agent VARCHAR(255) (nullable)
- created_at DATETIME
Indexes: (entity_type, entity_id), (actor_user_id, created_at)

### rate_limit_counters (optional)
- id
- key VARCHAR(128)
- window_start DATETIME
- count INT
- UNIQUE(key, window_start)

## 15. System & Queue Support
### jobs (Laravel default)
### failed_jobs (Laravel default)
### media_jobs (if separate) future

## 16. Full-Text / Search Notes
- Use MySQL InnoDB FULLTEXT on content_items(title, description) and announcements(title, body)
- External search (Elastic/OpenSearch) optional later for scaling.

## 17. Indexing & Performance Guidelines
- Foreign key columns indexed automatically.
- Composite indexes for high-volume filters (attendance_records(date, status), submissions(assessment_id, status)).
- Consider partial indexes (MySQL 8 functional) for active items if needed.

## 18. Data Retention & Archival
- Archive strategy: Move old submissions & attendance into yearly partition tables (future optimization).

---
## Pending Clarifications
- Do we require multi-section within a cohort beyond single letter? (If yes, add separate sections table.)
- Need encryption for sensitive columns (PII) at application layer? (Potential future.)
- Plagiarism integrations placeholders not yet modeled.

(End of draft)
