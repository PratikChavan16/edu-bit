# Feature Specification (Unified Production Scope)

## Portals / Roles
- Student
- Teacher (Faculty: Professor, Associate Professor)
- HOD
- Principal
- Admin

## Core Functional Domains
1. User & Identity
2. Academic Structure (Programs, Cohorts, Subjects, Competencies)
3. Content Delivery (Notes, Videos)
4. Assessments & Question Bank
5. Grading & Feedback
6. Attendance & Barcode Scanning
7. Timetable Management
8. Documents & Compliance
9. Fees & Billing
10. Announcements & Communication
11. Analytics & Dashboards
12. Notifications & Messaging
13. Gamification (Badges)
14. Audit & Governance
15. Search & Discovery
16. Calendar & Events

---
## Student Portal Features
- Dashboard: Year auto-subjects, today classes, upcoming assessments, deadlines, unread announcements, attendance %, fees summary, favorites, badge progress.
- Notes: Subject-wise list, filters (subject, teacher, type), teacher attribution, upload timestamp, preview, download (permissioned), version notes (later internal).
- Videos: Streaming (HLS), completion tracking, playback rate, teacher attribution; transcripts (optional later) placeholder.
- Assessments: MCQ (auto scoring), Short/Long (text + file/image), timers, open/close windows, attempt limit, competency mapping, instructions.
- Assessment Submission: Draft save (short/long), file attach (pdf, images), multi-image combine.
- Results & Feedback: Auto-score MCQ, manual score + feedback for others, competency mastery chart, per-question review when released.
- Favorites/Bookmarks: Any content, assessment, announcement.
- Search: Global scoped search (content, assessments metadata, announcements) with filters.
- Digital ID: Profile data, dynamic rotating barcode/QR (30s), anti-replay token.
- Attendance: Subject calendar, daily view, categorized statuses, summary metrics, export.
- Timetable: Daily & weekly view, teacher & room info, conflict highlight.
- Documents: Required vs submitted, statuses (pending/review/approved/rejected), rejection reason, re-upload.
- Faculty Directory: Name, designation, subjects, email, phone (masked until action), office hours.
- Fees: Summary, invoice list, receipt download, outstanding indicators, reminders.
- Announcements: Filterable feed, read/unread, pinned, private comment to author.
- Profile: Personal data (editable fields with approval), security settings (password, future 2FA), photo update.
- Calendar: Combined academic events + exams + personal deadlines.
- Gamification: Badges listing, earned & in-progress.
- Notifications: In-app center; preferences for categories.
- Accessibility: WCAG, keyboard navigation, alt text enforcement.

## Teacher (Faculty) Portal
- Dashboard: Today classes, pending grading count, upcoming assessments, recent submissions, content freshness.
- Content Management: Create/update notes & videos (draft/publish/archive), metadata (subject, competency tags optional), version tracking.
- Bulk Question Upload: Templates for MCQ, short, long; validation report.
- Assessment Builder: Define assessment, scheduling, competency mapping, question randomization, scoring rules, negative marking toggle.
- Question Bank: Reusable questions, tagging (difficulty, competency), cloning.
- Grading Workspace: Submission queue filters, inline scoring, rubric application, batch publish, feedback annotations.
- Attendance Tools: Mark attendance (manual adjustments with justification), scan console (token validation display), export.
- Timetable Interaction: View assigned slots; propose changes.
- Announcements: Post scoped to department/year; schedule; pin.
- Student Performance View: Per subject attendance & assessment overview, at-risk flags.
- Communication: Respond to private student comments.
- Favorites: Pin active assessments/content.
- Metrics: Grading turnaround time, assessment participation rate.

## HOD Portal
- Department Dashboard: Aggregated attendance, performance variance, content activity, grading delays.
- Comparative Analytics: Cohort vs cohort; subject performance distribution.
- Approvals: Timetable change requests, attendance adjustment overrides, content escalations.
- Faculty Oversight: Workload metrics, inactivity detection (no new content X days).
- Announcements: Department-level; pin/curate.
- Drill-Down: Department → subject → section → student.
- Alerts Center: Low attendance clusters, delayed grading, missing competencies coverage.
- Subject-Teacher Assignment: Create/modify offerings.
- Exports: Department summaries.

## Principal Portal
- Institution Dashboard: Cross-department KPIs (attendance %, pass rates, fees outstanding, compliance status).
- Comparative Analytics: Department vs department; year-over-year trends.
- Global Announcements: Institute-wide with priority flag.
- Risk Monitoring: Highlight underperforming departments / at-risk cohorts.
- Alerts Center: Systemic issues (widespread low attendance, ungraded backlog).
- Accreditation/Compliance Reports: Structured export templates.

## Admin Portal
- User Provisioning: Single & bulk (CSV/XLSX) creation, reset passwords, deactivate/reactivate.
- Role & Permission Management: Assign roles; fine-grain overrides.
- Master Data: Programs, academic years, departments, subjects, competencies.
- Fees Management: Define structures, generate invoices, record payments, concessions, receipts (PDF), reminders.
- Document Requirements: Configure required documents per program/year, reminders.
- Notifications Engine: Batch sends (fee due, document pending) + templates.
- Announcements Oversight: Moderate/remove flagged items.
- Academic Calendar Management: Global & scoped events (holiday, exam, fee deadline).
- Cohort Promotion & Archival: Year increment, graduation marking.
- Audit Logs: Filter/search actions, export.
- Data Export: CSV for users, attendance, performance, fees, documents.
- Security Controls: Force password reset, session invalidation.

## Shared / System Features
- Authentication: Sanctum/JWT, password reset, email verification.
- RBAC: Roles + permissions map; middleware enforcement.
- File Storage: S3 direct uploads (pre-signed), size & type validation.
- Media Processing: Asynchronous video transcoding to HLS; thumbnails.
- Attendance Barcode: Rotating signed token (nonce, expiry), replay protection.
- Notifications: Event bus → in-app + (later) email/SMS/push adapters.
- Search Index: Content, announcements, assessments metadata.
- Analytics Engine: Aggregation jobs writing summary tables.
- Badge Engine: Rule evaluation scheduler, progress tracking.
- Alert Engine: Threshold detection & lifecycle (open/ack/resolved).
- Audit Trail: Append-only log of sensitive actions.
- Observability: Structured logging, metrics, error tracking hooks.
- Rate Limiting: Per-IP & per-user on sensitive endpoints.
- Data Protection: PII separation (future encryption at rest columns where needed).
- Localization Framework Ready: i18n scaffolding.

## Non-Functional Requirements (Outline)
- Performance: P95 < 400ms for standard reads, < 800ms for heavy reports (cached).
- Scalability: Horizontal scaling via stateless Laravel + Redis session/cache.
- Security: OWASP adherence, strict validation (backend & frontend), audit coverage.
- Availability: Target 99.5% initial (single-region), roadmap to multi-AZ.
- Observability: Log correlation IDs, metrics (requests, latency, errors), trace hooks.
- Data Integrity: Transactions for multi-entity writes; background jobs idempotent.
- Backups: Daily DB snapshots, object storage lifecycle policies.

## Initial Backlog (Execution Order)
1. Auth & RBAC foundation
2. Academic structure & user provisioning
3. Content (notes/videos) base
4. Assessments + question model
5. Submissions & grading basics
6. Attendance & barcode service
7. Timetable
8. Documents management
9. Fees & invoices
10. Announcements & comments
11. Notifications center
12. Analytics summaries
13. Search integration
14. Gamification & alerts

(End of spec)
