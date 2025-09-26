# Backend Setup (Laravel 10)

## Prerequisites
- PHP 8.2+
- Composer 2.x
- MySQL 8
- Redis (local)
- Node.js (for mix/vite if needed later)

## Initial Project Creation
```bash
composer create-project laravel/laravel backend
```
(If already inside repo, run inside `backend/` directory.)

## Environment File
Copy example:
```bash
cd backend
cp .env.example .env
```
Key variables to adjust:
```
APP_NAME=BitflowLMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitflow_lms
DB_USERNAME=root
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

FILESYSTEM_DISK=local
AWS_BUCKET=your-bucket
AWS_DEFAULT_REGION=ap-south-1

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

## Dependencies
```bash
composer require laravel/sanctum spatie/laravel-permission laravel/scout league/flysystem-aws-s3-v3 predis/predis
```
(Scout reserved for future search integration.)

## Sanctum Setup
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```
Add SPA cookie domain config in `config/sanctum.php` as needed.

## Permissions Package (Spatie)
Publish config & migration (if not auto):
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

## Queue & Horizon (Optional Early)
```bash
composer require laravel/horizon
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"
```

## Migrations Ordering Strategy
1. Core identity: users, roles, permissions, pivot tables.
2. Academic structure: departments, programs, academic_years, cohorts, subjects, competencies, subject_offerings.
3. Content & assessments.
4. Attendance & timetable.
5. Fees & documents.
6. Announcements, favorites, notifications, badges.
7. Audit, alerts, workload snapshots.

## Seeding Strategy
- Roles & permissions map.
- Admin user.
- Departments/programs/subjects sample.
- Test cohorts & offerings.

Example seeder snippet (pseudo):
```php
Role::create(['name' => 'admin']);
Role::create(['name' => 'student']);
// ...
Permission::create(['name' => 'content.create']);
// ...
$admin->assignRole('admin');
```

## API Structure
Use route groups:
```
Route::prefix('v1')->group(function () {
  Route::post('/auth/login', ...);
  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/me', ...);
    Route::apiResource('content', ContentController::class);
  });
});
```

## Versioning
Prefix all endpoints with `/api/v1`. Keep OpenAPI synced (`docs/openapi.yaml`).

## Validation
Use Form Request classes; standardize error format:
```json
{ "success": false, "error": { "code": "VALIDATION_ERROR", "details": { ... } } }
```

## Error Handling
Customize `render()` in `App\Exceptions\Handler` for JSON API responses.

## Logging & Audit
- Use events for significant domain changes → listener writes to `audit_logs`.
- Add middleware to capture actor, IP, UA.

## Background Jobs
- Content video transcoding dispatch job stub.
- Badge evaluation scheduled command.

## Next Actions
1. Implement migrations skeleton.
2. Add Role/Permission seeder.
3. Implement Auth (login/logout/me) with Sanctum.
4. Add Subject & Content endpoints.
5. Wire OpenAPI doc generation (e.g., `vyuldashev/laravel-openapi` or `darkaonline/l5-swagger`).

(End of backend setup doc)
