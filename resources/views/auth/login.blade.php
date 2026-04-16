<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login Koperasi</title>
	<link rel="icon" type="image/png" href="images/logo-ab.png">

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<style>
		:root {
			--primary-color: #4e73df;
			--primary-dark: #2e59d9;
			--secondary-color: #f8f9fc;
			--text-color: #5a5c69;
			--light-gray: #e3e6f0;
		}

		body {
			background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f5 100%);
			font-family: 'Poppins', sans-serif;
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
			transition: all 0.3s ease;
		}

		.login-header {
			background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
			color: white;
			padding: 2rem 1.5rem;
			text-align: center;
			position: relative;
			overflow: hidden;
		}

		.login-header::before {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
			transform: rotate(30deg);
		}

		.login-header img {
			height: 70px;
			margin-bottom: 15px;
			filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
			transition: transform 0.3s ease;
		}

		.login-header:hover img {
			transform: scale(1.05);
		}

		.login-header h4 {
			font-weight: 600;
			margin-bottom: 0;
			letter-spacing: 0.5px;
		}

		.login-header p {
			opacity: 0.9;
			font-size: 0.9rem;
			margin-top: 5px;
		}

		.login-body {
			padding: 2.5rem;
		}

		.form-control {
			height: 50px;
			border-radius: 8px;
			padding: 0.75rem 1rem;
			border: 1px solid var(--light-gray);
			font-size: 0.95rem;
			transition: all 0.3s;
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
		}

		.input-group-text {
			background: transparent;
			border-left: none;
			cursor: pointer;
			color: var(--text-color);
		}

		.input-group .form-control {
			border-right: none;
		}

		.input-group .form-control:focus+.input-group-text {
			border-color: var(--primary-color);
			color: var(--primary-color);
		}

		.btn-login {
			background-color: var(--primary-color);
			border: none;
			padding: 0.85rem;
			font-weight: 600;
			width: 100%;
			border-radius: 8px;
			letter-spacing: 0.5px;
			text-transform: uppercase;
			font-size: 0.95rem;
			transition: all 0.3s;
			box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
		}

		.btn-login:hover {
			background-color: var(--primary-dark);
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
		}

		.btn-login:active {
			transform: translateY(0);
		}

		.forgot-link {
			color: var(--text-color);
			text-decoration: none;
			font-size: 0.9rem;
			transition: color 0.3s;
		}

		.forgot-link:hover {
			color: var(--primary-color);
		}

		.alert {
			border-radius: 8px;
		}

		/* Responsive adjustments */
		@media (max-width: 768px) {
			body {
				padding: 15px;
				display: flex;
				justify-content: center;
				align-items: center;
			}

			.login-container {
				width: 100%;
				margin: 0 auto;
			}

			.login-header {
				padding: 1.75rem 1.5rem;
			}

			.login-body {
				padding: 2rem;
			}
		}

		@media (max-width: 576px) {
			.login-header {
				padding: 1.5rem;
			}

			.login-body {
				padding: 1.75rem;
			}

			.login-header img {
				height: 60px;
			}

			.login-header h4 {
				font-size: 1.3rem;
			}
		}

		@media (max-width: 400px) {
			.login-header img {
				height: 55px;
			}

			.login-header h4 {
				font-size: 1.25rem;
			}

			.login-body {
				padding: 1.5rem;
			}
		}

		/* Animation */
		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(20px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.login-container {
			animation: fadeIn 0.5s ease-out forwards;
		}
	</style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('images/logo-small.png') }}" alt="Logo Koperasi">
            <h4>SISTEM INFORMASI KOPERASI</h4>
            <p>Masukkan akun Anda untuk mengakses sistem</p>
        </div>

        <div class="login-body">
            {{-- Notifikasi Error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf {{-- WAJIB di Laravel --}}
                <div class="mb-4">
                    <label for="username" class="form-label fw-medium">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username" required value="{{ old('username') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        <span class="input-group-text bg-light" id="togglePassword" style="cursor: pointer;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>MASUK
                </button>
            </form>
        </div>
    </div>

   <!-- Bootstrap 5 JS Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

	<!-- Password Toggle Script -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const togglePassword = document.querySelector('#togglePassword');
			const password = document.querySelector('#password');
			const eyeIcon = document.querySelector('#eyeIcon');

			togglePassword.addEventListener('click', function() {
				// Toggle the type attribute
				const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
				password.setAttribute('type', type);

				// Toggle the eye icon
				eyeIcon.classList.toggle('fa-eye');
				eyeIcon.classList.toggle('fa-eye-slash');

				// Add animation
				this.style.transform = 'scale(1.2)';
				setTimeout(() => {
					this.style.transform = 'scale(1)';
				}, 200);
			});

			// Add focus effects
			password.addEventListener('focus', function() {
				togglePassword.style.borderColor = '#4e73df';
				togglePassword.style.color = '#4e73df';
			});

			password.addEventListener('blur', function() {
				togglePassword.style.borderColor = '';
				togglePassword.style.color = '';
			});
		});
	</script>
</body>
</html>