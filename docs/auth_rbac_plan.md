# Auth & RBAC Plan

## Authentication Strategy
- Laravel Sanctum for SPA + mobile token use (personal access tokens for mobile, session cookies for web).
- Endpoints use `auth:sanctum` middleware.
- Password reset & email verification via Laravel built-ins.
- Potential future: Issue short-lived JWT for barcode/QR ephemeral tokens (separate signing key) while keeping Sanctum for main auth.

## Roles
- admin
- principal
- hod
- teacher
- student

## Permission Names (Initial Set)
Grouped by domain (prefix optional):
- user.view_self
- user.update_self
- user.manage (admin)
- academic.manage_structure (admin)
- academic.view
- content.create
- content.update_own
- content.publish_own
- content.view
- assessment.create
- assessment.update_own
- assessment.publish_own
- assessment.view
- submission.create
- submission.view_own
- submission.grade (teacher/hod)
- attendance.mark (teacher)
- attendance.view_subject (teacher)
- attendance.view_self (student)
- attendance.view_department (hod)
- timetable.manage (hod)
- document.manage_requirements (admin)
- document.upload_own (student)
- document.review (admin)
- fees.manage (admin)
- fees.view_own (student)
- fees.view_department (hod)
- announcement.create
- announcement.moderate (admin)
- announcement.view
- notification.view
- badge.view
- analytics.view_department (hod)
- analytics.view_institution (principal)
- alert.manage (hod, principal, admin)

## Mapping Roles → Permissions (High-Level)
- student: user.view_self, user.update_self, content.view, assessment.view, submission.create, submission.view_own, attendance.view_self, document.upload_own, fees.view_own, announcement.view, notification.view, badge.view
- teacher: + content.create, content.update_own, content.publish_own, assessment.create, assessment.update_own, assessment.publish_own, submission.grade, attendance.mark, attendance.view_subject, announcement.create
- hod: + timetable.manage, attendance.view_department, analytics.view_department, alert.manage
- principal: + analytics.view_institution, alert.manage
- admin: user.manage, academic.manage_structure, document.manage_requirements, document.review, fees.manage, announcement.moderate, alert.manage

## Implementation Steps
1. Install & configure Sanctum.
2. Install Spatie Permission; publish config.
3. Migrations for any custom tables (audit_logs, etc.).
4. Seeder: create roles & permissions, assign to admin user.
5. Middleware registration: `Route::middleware(['auth:sanctum','permission:content.create'])` where needed or policy-based.
6. Define Policies for contextual ownership (e.g., update own content, grade only own subject submissions).
7. Add a `HasPermissions` trait usage from Spatie to User model.
8. Authorization tests (PHPUnit) for representative endpoints.

## Ownership Rules
- Content/Assessment modification allowed only if `teacher_user_id == auth()->id()` OR role escalated (hod/admin) with appropriate permission.
- Submission viewing restricted: student sees own; teacher sees for subject(s) they own; hod sees department subjects; principal with institution permission sees all.

## Barcode / QR Tokens
- Separate issue endpoint `/attendance/token` (auth required) producing ephemeral signed payload (user_id, issued_at, expires_at, nonce).
- Validation endpoint verifies signature + expiry + nonce single-use (Redis set). Not tied to Sanctum session to reduce overhead.

## Session & Security Notes
- Force password reset on initial login for bulk-created users (flag column `must_reset_password`).
- Optionally store last_login_at and failed login attempts for lockout.

## Logging & Auditing
- On role/permission assignment changes log diff.
- On grade publication create audit log entry with before/after scores if adjusted.

## Open Questions
- Multi-role support per user simultaneously? (Current plan: yes, pivot table; UI should constrain typical combinations.)
- Need hierarchical permission inference (e.g., admin implies all)? (Simplify by explicit grants.)

(End of plan)
