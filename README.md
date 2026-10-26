# ClientHub

A client portal built with Laravel. Clients track their projects, milestones, files and invoices, and pay invoices through eSewa. Admins manage everything from an admin panel.

## Features

### Client side
- Login / logout with "remember me", forgot / reset password
- Invitation link to activate an account (valid 48 hours)
- Dashboard with project status, progress, key dates, and a project switcher
- Approve milestones, request revisions, and comment on milestones
- Upload, view and download project files
- Download invoices as PDF and pay them via eSewa
- Activity feed of project updates

### Admin side
- Overview dashboard (clients, projects, revenue, outstanding amount)
- Create / edit / delete projects (with starter templates), milestones, invoices and files
- Invite clients by email, resend invitations
- Payment history with search and CSV export
- Activity log with per-admin read state, and global search
- Daily check that marks unpaid invoices overdue and notifies the client

## Tech stack
- Laravel 12, PHP 8.2 or newer
- SQLite locally, PostgreSQL when deployed
- barryvdh/laravel-dompdf for invoice PDFs
- Blade templates, Pest tests
- eSewa for payments, Brevo (API) or SMTP for email

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan tinker
```
In tinker, create an admin:
```php
App\Models\User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
]);
```
Then start the server with `php artisan serve` (Windows: double-click `start-server.bat`).

## Emails
Emails are sent immediately during the request. If sending fails, the error is logged and the page still works; the admin is told when an invitation could not be sent.

- Local: `MAIL_MAILER=log` writes emails to `storage/logs/laravel.log`.
- Production: `MAIL_MAILER=brevo` with `BREVO_API_KEY` (an API key starting with `xkeysib-`) and a sender address verified in Brevo.

## Scheduled task
`php artisan schedule:work` runs the hourly overdue-invoice check. The Docker image starts it automatically. Locally you can run `php artisan invoices:mark-overdue` by hand.

## Deployment (Render, Docker)
The `Dockerfile` builds the app. On start it caches config and routes, runs migrations, and starts the scheduler, PHP-FPM and nginx. Set these environment variables on the host:

`APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `DB_*` (PostgreSQL), `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `BREVO_API_KEY`, `ESEWA_MERCHANT_CODE`, `ESEWA_SECRET_KEY`, `ESEWA_BASE_URL`, `ESEWA_STATUS_URL`.

Note: uploaded files are stored on the server's local disk. On hosts with temporary disks they are lost on redeploy unless a persistent disk or cloud storage is used.

## Tests
```bash
php artisan test
```