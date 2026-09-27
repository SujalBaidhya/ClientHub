# ClientHub

A client portal built with Laravel, where clients can track their project's progress, milestones, files, and invoices — and admins can manage all of it without touching the database directly.

## Features

### Client Side
- Login / logout with "remember me"
- Forgot password / reset password via email
- Account settings (update name, change password)
- Dashboard showing project status, progress bar, and key dates
- Support for multiple projects per client, with a project switcher
- View and mark milestones as complete
- Download project files
- Download invoices as PDF

### Admin Side
- Overview dashboard with key stats (total clients, total projects, revenue collected, outstanding amount, project status breakdown)
- Full create / edit / delete for projects
- Full create / edit / delete for milestones
- Full create / edit / delete for invoices
- Create client accounts (auto-sends a welcome email)
- Upload and manage project files
- Search and filter projects by name or client

### Notifications
Emails are sent automatically for:
- New client account created
- Password reset requested
- New invoice added
- Milestone marked complete

## Tech Stack
- **Backend:** Laravel 12 (PHP 8.4)
- **Database:** SQLite
- **PDF generation:** barryvdh/laravel-dompdf
- **Templating:** Blade
- **Testing:** Pest

## Getting Started

### 1. Install dependencies
```bash
composer install
```

### 2. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Run migrations
```bash
php artisan migrate
```

### 4. Create an admin user
```bash
php artisan tinker
```
```php
$user = App\Models\User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
]);
```

### 5. Start the server
```bash
php artisan serve
```
Visit `http://127.0.0.1:8000`.

**Windows shortcut:** double-click `start-server.bat` in the project root to start the server without typing any commands.

### Note on emails
By default, `MAIL_MAILER` is set to `log` in `.env.example` — emails are written to `storage/logs/laravel.log` instead of actually being sent, which is fine for local development and testing.

## 📧 Email & Queue Configuration

All outgoing emails (invitations, milestone alerts, and payment receipts) implement `ShouldQueue` to prevent blocking web requests.

### 1. Gmail SMTP Setup
To send real emails through Gmail SMTP:
1. Log into your Google account.
2. Ensure **2-Step Verification** is turned ON (`Google Account` > `Security`).
3. Go to [Google App Passwords](https://myaccount.google.com/apppasswords).
4. Create a new App Password named `ClientHub` and copy the 16-character string.
5. Configure your `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hello.clienthub@gmail.com
MAIL_PASSWORD=your_16_character_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello.clienthub@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

Because emails are queued in the database, you must have the queue worker running in a separate terminal:

php artisan queue:work

Ensure your .env specifies:

QUEUE_CONNECTION=database


🔐 Client Invitation Flow
Admin Creation: Go to /admin/clients, enter only the client's Name and Email (no password needed).

Token Dispatch: The app creates a unique token and queues a ClientInvitationMail.

Activation: The client receives the link (/invitation/{token}), sets their password, and is automatically logged in.

**Never commit real credentials.** `.env` is already git-ignored — keep it that way.

## Running Tests
```bash
php artisan test
```

## Project Structure Notes
- `app/Http/Controllers/Admin/` — all admin-only controllers (protected by the `admin` role middleware)
- `app/Mail/` — email classes for the 4 notification types
- `resources/views/admin/` — admin panel views
- `resources/views/emails/` — email templates