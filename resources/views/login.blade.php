<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — ClientHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #1e1b4b;
            --primary-hover: #2e287a;
            --accent: #4f46e5;
            --accent-gold: #d97706;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --font-display: 'Fraunces', serif;
            --font-body: 'Inter', -apple-system, sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
        }

        body {
            font-family: var(--font-body);
            background: #ffffff;
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        .split-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== Left Panel: Form ===== */
        .auth-form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 56px;
            background: #ffffff;
            max-width: 580px;
            width: 100%;
        }

        .auth-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
        }

        .brand-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -0.02em;
            box-shadow: 0 4px 10px rgba(30, 27, 75, 0.2);
        }

        .brand-name {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
            letter-spacing: -0.01em;
        }

        .auth-form-container {
            max-width: 400px;
            width: 100%;
            margin: auto 0;
            padding: 24px 0;
        }

        .auth-heading {
            font-family: var(--font-display);
            font-size: 30px;
            font-weight: 600;
            color: var(--primary);
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .auth-subtext {
            font-size: 14.5px;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .auth-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 22px;
            line-height: 1.4;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14.5px;
            font-family: inherit;
            color: var(--text);
            background: #ffffff;
            transition: all 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .password-toggle-btn:hover {
            color: var(--text);
        }

        .field-error {
            margin: 6px 0 0;
            font-size: 12.5px;
            color: #dc2626;
            font-weight: 500;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13.5px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(30, 27, 75, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 27, 75, 0.22);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-footer {
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--muted);
        }

        .auth-footer-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-weight: 500;
        }

        /* ===== Right Panel: Showcase ===== */
        .auth-showcase-side {
            flex: 1.15;
            background: #0b0924;
            background-image: 
                radial-gradient(circle at 15% 25%, rgba(79, 70, 229, 0.3) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(217, 119, 6, 0.18) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(30, 27, 75, 0.5) 0%, transparent 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Subtle grid background */
        .showcase-grid-bg {
            position: absolute;
            inset: 0;
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            pointer-events: none;
        }

        .showcase-content {
            max-width: 520px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .showcase-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #cbd5e1;
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
        }

        .showcase-title {
            font-family: var(--font-display);
            font-size: 38px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .showcase-title em {
            font-style: italic;
            color: #a5b4fc;
        }

        .showcase-desc {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        /* Testimonial Quote Card */
        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 24px 28px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            margin-bottom: 32px;
        }

        .stars-row {
            display: flex;
            gap: 4px;
            margin-bottom: 14px;
        }

        .testimonial-quote {
            font-size: 15px;
            line-height: 1.6;
            color: #e2e8f0;
            font-style: italic;
            margin-bottom: 18px;
        }

        .testimonial-author-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .testimonial-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .testimonial-author-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .testimonial-author-role {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .showcase-features-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .showcase-feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #cbd5e1;
        }

        .showcase-check-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.25);
            color: #a5b4fc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ===== Responsive Breakpoints ===== */
        @media (max-width: 960px) {
            .auth-showcase-side {
                display: none;
            }
            .auth-form-side {
                max-width: 100%;
                padding: 40px 24px;
                align-items: center;
            }
            .auth-header {
                width: 100%;
                max-width: 400px;
            }
            .auth-footer {
                width: 100%;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>

    <div class="split-layout">

        {{-- Left: Login Form --}}
        <div class="auth-form-side">

            <div class="auth-header">
                <a href="/" class="brand-link">
                    <span class="brand-badge">CH</span>
                    <span class="brand-name">ClientHub</span>
                </a>
            </div>

            <div class="auth-form-container">
                <h1 class="auth-heading">Welcome back</h1>
                <p class="auth-subtext">Sign in to your client portal to review deliverables and invoices.</p>

                @if (session('success'))
                    <div class="auth-alert alert-success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert alert-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.process') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <div class="input-wrapper">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                autocomplete="email"
                                required
                                autofocus
                                class="form-control"
                            >
                        </div>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                                class="form-control"
                                style="padding-right: 40px;"
                            >
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeOffIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                    <line x1="2" x2="22" y1="2" y2="22"></line>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Sign In</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="auth-footer">
                <span>&copy; {{ date('Y') }} ClientHub</span>
                <span class="auth-footer-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>256-bit Encrypted</span>
                </span>
            </div>

        </div>

        {{-- Right: Showcase Panel --}}
        <div class="auth-showcase-side">
            <div class="showcase-grid-bg"></div>

            <div class="showcase-content">
                <div class="showcase-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span>Client Portal Workspace</span>
                </div>

                <h2 class="showcase-title">
                    The transparent portal for <em>agencies & clients</em>.
                </h2>
                <p class="showcase-desc">
                    Collaborate on deliverables, approve milestones in real time, and handle payments inside one unified workspace.
                </p>

                {{-- Client Testimonial Card --}}
                <div class="testimonial-card">
                    <div class="stars-row">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>

                    <p class="testimonial-quote">
                        &ldquo;ClientHub gave our clients total clarity into project milestones and deliverables. Sign-offs and payments that used to take weeks now happen seamlessly.&rdquo;
                    </p>

                    <div class="testimonial-author-row">
                        <div class="testimonial-avatar">AR</div>
                        <div>
                            <div class="testimonial-author-name">Alex Rivera</div>
                            <div class="testimonial-author-role">Founder &middot; Apex Digital Agency</div>
                        </div>
                    </div>
                </div>

                {{-- Highlights List --}}
                <div class="showcase-features-list">
                    <div class="showcase-feature-item">
                        <span class="showcase-check-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>1-Click Milestone Approvals & Revisions</span>
                    </div>
                    <div class="showcase-feature-item">
                        <span class="showcase-check-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Secure File Exchange & Automated Reminders</span>
                    </div>
                    <div class="showcase-feature-item">
                        <span class="showcase-check-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Integrated eSewa Invoicing with PDF Receipts</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Password visibility toggle
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.style.display = isPassword ? 'none' : 'block';
                eyeOffIcon.style.display = isPassword ? 'block' : 'none';
            });
        }
    </script>

</body>
</html>
