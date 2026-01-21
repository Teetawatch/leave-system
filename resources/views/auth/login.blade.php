<x-guest-layout>
    <div class="login-container">
        <!-- Animated Gradient Background -->
        <div class="gradient-bg">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="gradient-orb orb-4"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="grid-pattern"></div>

        <!-- Floating Particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <!-- Main Content -->
        <div class="login-wrapper">
            <div class="login-card">
                <!-- Decorative Top Bar -->
                <div class="card-top-bar"></div>
                
                <!-- Card Content -->
                <div class="card-content">
                    <!-- Logo Section -->
                    <div class="logo-section">
                        <div class="logo-glow"></div>
                        <div class="logo-container">
                            <img src="{{ asset('images/logonavy.png') }}" alt="Logo" class="logo-img">
                        </div>
                        <h1 class="welcome-title">ยินดีต้อนรับ</h1>
                        <p class="welcome-subtitle">
                            ระบบบริหารจัดการงานธุรการด้านกำลังพล<br>
                            <span class="org-name">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</span>
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="session-status" :status="session('status')" />

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="16" x="2" y="4" rx="2"/>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                </span>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    class="form-input" 
                                    placeholder=" "
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                >
                                <label for="email" class="floating-label">อีเมลของคุณ</label>
                                <div class="input-highlight"></div>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="error-message" />
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder=" "
                                    required 
                                    autocomplete="current-password"
                                >
                                <label for="password" class="floating-label">รหัสผ่าน</label>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg id="eye-off-icon" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                        <line x1="2" x2="22" y1="2" y2="22"/>
                                    </svg>
                                </button>
                                <div class="input-highlight"></div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-message" />
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="form-options">
                            <label class="remember-label">
                                <input type="checkbox" name="remember" class="remember-checkbox">
                                <span class="checkbox-custom"></span>
                                <span class="remember-text">จดจำฉันไว้</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    ลืมรหัสผ่าน?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="submit-btn">
                            <span class="btn-text">เข้าสู่ระบบ</span>
                            <span class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                    <polyline points="10 17 15 12 10 7"/>
                                    <line x1="15" x2="3" y1="12" y2="12"/>
                                </svg>
                            </span>
                            <div class="btn-shine"></div>
                        </button>
                    </form>
                </div>

                <!-- Card Footer -->
                <div class="card-footer">
                    <span class="footer-text">เป็นข้าราชการใหม่?</span>
                    <a href="{{ route('employee.register') }}" class="register-link">
                        ลงทะเบียนที่นี่
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Copyright -->
            <p class="copyright">
                © {{ date('Y') }} ระบบบริหารจัดการงานธุรการด้านกำลังพล<br>
                <span class="developer">ออกแบบและพัฒนาโดย จ.ท.ธีร์ธวัช พิพัฒน์เดชธน</span>
            </p>
        </div>
    </div>

    <style>
        /* ========== CSS Variables - Light Theme ========== */
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --accent: #0891b2;
            --accent-light: #06b6d4;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --bg-card: rgba(255, 255, 255, 0.85);
            --bg-input: rgba(241, 245, 249, 0.8);
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border-color: rgba(148, 163, 184, 0.3);
            --success: #10b981;
            --error: #ef4444;
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            --shadow-card: 0 20px 60px -15px rgba(79, 70, 229, 0.2);
            --radius-lg: 1.75rem;
            --radius-md: 1rem;
            --radius-sm: 0.5rem;
        }

        /* ========== Reset & Base ========== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ========== Main Container ========== */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 50%, #fdf4ff 100%);
            font-family: 'Kanit', 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
            padding: 1rem;
        }

        /* ========== Animated Background ========== */
        .gradient-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
            filter: blur(100px);
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            animation: float 10s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #a5b4fc 0%, #818cf8 100%);
            top: -15%;
            left: -10%;
            opacity: 0.5;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, #67e8f9 0%, #22d3ee 100%);
            top: 60%;
            right: -12%;
            opacity: 0.4;
            animation-delay: -2s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #f0abfc 0%, #e879f9 100%);
            bottom: -10%;
            left: 25%;
            opacity: 0.4;
            animation-delay: -4s;
        }

        .orb-4 {
            width: 280px;
            height: 280px;
            background: linear-gradient(135deg, #86efac 0%, #4ade80 100%);
            top: 30%;
            left: 60%;
            opacity: 0.3;
            animation-delay: -6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(40px, -40px) scale(1.05);
            }
            50% {
                transform: translate(-25px, 25px) scale(0.98);
            }
            75% {
                transform: translate(-35px, -15px) scale(1.02);
            }
        }

        /* ========== Grid Pattern ========== */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(79, 70, 229, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79, 70, 229, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* ========== Floating Particles ========== */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, var(--primary-light), var(--accent-light));
            border-radius: 50%;
            opacity: 0.5;
            animation: rise 12s infinite ease-in;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 14s; }
        .particle:nth-child(2) { left: 25%; animation-delay: -2s; animation-duration: 12s; }
        .particle:nth-child(3) { left: 50%; animation-delay: -4s; animation-duration: 16s; }
        .particle:nth-child(4) { left: 75%; animation-delay: -6s; animation-duration: 13s; }
        .particle:nth-child(5) { left: 90%; animation-delay: -8s; animation-duration: 15s; }

        @keyframes rise {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100px) scale(1);
                opacity: 0;
            }
        }

        /* ========== Login Wrapper ========== */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }

        /* ========== Login Card ========== */
        .login-card {
            background: var(--bg-card);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-card), 0 0 0 1px rgba(255, 255, 255, 0.3) inset;
            overflow: hidden;
            animation: cardEnter 0.6s ease-out;
        }

        @keyframes cardEnter {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-top-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent-light), var(--primary-light), var(--primary));
            background-size: 300% 100%;
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }

        .card-content {
            padding: 2.5rem 2rem;
        }

        /* ========== Logo Section ========== */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -60%);
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            opacity: 0.2;
            filter: blur(25px);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.2; transform: translate(-50%, -60%) scale(1); }
            50% { opacity: 0.35; transform: translate(-50%, -60%) scale(1.1); }
        }

        .logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.25rem;
        }

        .logo-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            filter: drop-shadow(0 8px 25px rgba(79, 70, 229, 0.25));
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05) rotate(3deg);
        }

        .welcome-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .welcome-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .org-name {
            color: var(--primary);
            font-weight: 500;
        }

        /* ========== Session Status ========== */
        .session-status {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #059669;
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ========== Form ========== */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .form-group {
            position: relative;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-muted);
            transition: color 0.3s ease;
            z-index: 2;
            display: flex;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: var(--bg-input);
            border: 2px solid transparent;
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            background: var(--bg-white);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-input:focus + .floating-label,
        .form-input:not(:placeholder-shown) + .floating-label {
            transform: translateY(-2.6rem) translateX(-0.5rem) scale(0.85);
            color: var(--primary);
            background: var(--bg-white);
            padding: 0 0.5rem;
        }

        .form-input:focus ~ .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .floating-label {
            position: absolute;
            left: 3rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9375rem;
            pointer-events: none;
            transition: all 0.3s ease;
            transform-origin: left;
        }

        .input-highlight {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent-light));
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .form-input:focus ~ .input-highlight {
            left: 0;
            width: 100%;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .hidden {
            display: none;
        }

        /* Error Message */
        .error-message {
            margin-top: 0.5rem;
            color: var(--error);
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* ========== Form Options ========== */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .checkbox-custom::after {
            content: '';
            width: 10px;
            height: 6px;
            border: 2.5px solid white;
            border-top: none;
            border-right: none;
            transform: rotate(-45deg) scale(0);
            transition: transform 0.2s ease;
            position: absolute;
            top: 3px;
        }

        .remember-checkbox:checked + .checkbox-custom {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-checkbox:checked + .checkbox-custom::after {
            transform: rotate(-45deg) scale(1);
        }

        .remember-label:hover .checkbox-custom {
            border-color: var(--primary-light);
        }

        .remember-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* ========== Submit Button ========== */
        .submit-btn {
            position: relative;
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            overflow: hidden;
            margin-top: 0.5rem;
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.5);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px -10px rgba(79, 70, 229, 0.6);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .btn-icon {
            display: flex;
            transition: transform 0.3s ease;
        }

        .submit-btn:hover .btn-icon {
            transform: translateX(4px);
        }

        .btn-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: left 0.5s ease;
        }

        .submit-btn:hover .btn-shine {
            left: 100%;
        }

        /* ========== Card Footer ========== */
        .card-footer {
            padding: 1.25rem 2rem;
            background: rgba(241, 245, 249, 0.6);
            border-top: 1px solid rgba(148, 163, 184, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .footer-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .register-link {
            font-size: 0.875rem;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: var(--primary);
            gap: 0.625rem;
        }

        /* ========== Copyright ========== */
        .copyright {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .developer {
            opacity: 0.8;
        }

        /* ========== Responsive Design ========== */
        @media (max-width: 480px) {
            .login-container {
                padding: 0.75rem;
            }

            .card-content {
                padding: 2rem 1.5rem;
            }

            .logo-img {
                width: 70px;
                height: 70px;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .welcome-subtitle {
                font-size: 0.8125rem;
            }

            .form-input {
                padding: 0.875rem 0.875rem 0.875rem 2.75rem;
                font-size: 0.9375rem;
            }

            .floating-label {
                left: 2.75rem;
                font-size: 0.875rem;
            }

            .input-icon {
                left: 0.875rem;
            }

            .input-icon svg {
                width: 18px;
                height: 18px;
            }

            .submit-btn {
                padding: 0.875rem 1.5rem;
                font-size: 0.9375rem;
            }

            .card-footer {
                padding: 1rem 1.5rem;
                flex-direction: column;
                gap: 0.25rem;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.625rem;
            }

            .copyright {
                font-size: 0.6875rem;
            }
        }

        @media (max-width: 360px) {
            .card-content {
                padding: 1.5rem 1.25rem;
            }

            .logo-img {
                width: 60px;
                height: 60px;
            }

            .welcome-title {
                font-size: 1.375rem;
            }
        }

        /* ========== Prefers Reduced Motion ========== */
        @media (prefers-reduced-motion: reduce) {
            .gradient-orb,
            .particle,
            .logo-glow,
            .card-top-bar {
                animation: none;
            }

            .submit-btn:hover {
                transform: none;
            }

            .btn-shine {
                display: none;
            }
        }
    </style>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // Add ripple effect on button click
        document.querySelector('.submit-btn').addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;

            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });

        // Add ripple animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-guest-layout>
