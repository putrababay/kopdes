<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Koperasi - Sistem Manajemen Digital</title>
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4e73df">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-ab.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* Tetap menggunakan style Anda tanpa merubah UI/UX */
        :root {
            --primary-color: #4e73df;
            --primary-dark: #2e59d9;
            --secondary-color: #f8f9fc;
            --text-color: #5a5c69;
            --light-gray: #e3e6f0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f5 100%);
            font-family: 'Inter', sans-serif; /* Sesuaikan dengan font-family link */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(58, 59, 69, 0.1);
            border-radius: 12px;
            overflow: hidden;
            background: white;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
        }

        .login-header img {
            height: 70px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .login-body { padding: 2.5rem; }

        .form-control {
            height: 50px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('images/logo-small.png') }}" alt="Logo">
            <h4>SISTEM INFORMASI KOPERASI</h4>
            <p>Masukkan akun Anda untuk mengakses sistem</p>
        </div>

        <div class="login-body">
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-medium">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required value="{{ old('username') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <span class="input-group-text bg-light" id="togglePassword" style="cursor: pointer;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-login mb-3">MASUK</button>

                <div id="installApp" class="mt-2 text-center" style="display: none;">
                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 btn-sm" id="btnInstall">
                        <i class="fa fa-download me-2"></i> Instal Aplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- 1. PWA Logic ---
        let deferredPrompt;
        const installBox = document.getElementById('installApp');
        const btnInstall = document.getElementById('btnInstall');

        // Deteksi instalasi
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBox) installBox.style.display = 'block';
        });

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log("PWA Active"))
                    .catch(err => console.log("PWA Error", err));
            });
        }

        if (btnInstall) {
            btnInstall.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') installBox.style.display = 'none';
                    deferredPrompt = null;
                }
            });
        }

        // --- 2. UI Logic (Password Toggle & Session) ---
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle
            const togglePassword = document.querySelector('#togglePassword');
            const passwordField = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.type === 'password' ? 'text' : 'password';
                    passwordField.type = type;
                    eyeIcon.classList.toggle('fa-eye');
                    eyeIcon.classList.toggle('fa-eye-slash');
                });
            }

            // Session Handler (24 Jam)
            @if(Session::has('nik'))
                const loginData = {
                    nik: "{{ Session::get('nik') }}",
                    expiry: new Date().getTime() + (24 * 60 * 60 * 1000)
                };
                localStorage.setItem('user_session', JSON.stringify(loginData));
            @endif

            const session = JSON.parse(localStorage.getItem('user_session'));
            if (session && window.location.pathname.includes('login')) {
                if (new Date().getTime() < session.expiry) {
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    localStorage.removeItem('user_session');
                }
            }
        });
    </script>
</body>
</html>