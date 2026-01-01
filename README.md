<h1 align="center">Vanina Villa Admin Portal</h1>

Role-based admin and user portal built with Laravel 11, featuring custom authentication flows, an admin dashboard, and activity logging.

## Stack
- PHP 8.2+, Laravel 11
- MySQL (or any Laravel-supported DB)
- Node 18+ with npm (Vite, Bootstrap 5, Tailwind utilities)

## Features
- Email/password auth with password reset
- Role-based access control (admin vs user)
- Admin dashboard shell for content modules
- Admin activity logging middleware/service
- Vite-powered assets with Bootstrap and icons

## Quick start
1) Install dependencies
```
composer install
npm install
```

2) Configure environment
```
cp .env.example .env
php artisan key:generate
```
Update `.env` with your DB credentials (host, database, user, password) and app URL.

3) Run migrations and seeds (creates users table, sessions, password resets, admin activity log)
```
php artisan migrate
```

4) Build or run assets
```
npm run dev   # hot reload
# or
npm run build
```

5) Serve the app
```
php artisan serve
```
Visit the URL shown (default http://localhost:8000).

## Testing
```
php artisan test
```

## Project structure notes
- app/Http/Controllers/AuthController.php: login/register/reset flows and dashboard routing
- app/Http/Middleware/*: auth guards, roles, secure headers, session timeout, activity logging
- resources/views/: auth pages, layout, dashboard/admin UI
- database/migrations: users, sessions, password resets, admin activity logs

## Common environment tweaks
- Mail: set MAIL_MAILER, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD for password reset emails
- URL: set APP_URL to your local domain if using Valet/XAMPP
- Session: update SESSION_DRIVER/SESSION_LIFETIME to match your setup

## Deployment basics
- Set APP_ENV=production, APP_DEBUG=false
- Run php artisan config:cache route:cache view:cache
- Ensure storage is writable (storage, bootstrap/cache)

---
Maintained for the Vanina Villa project. Contributions welcome via PRs.
