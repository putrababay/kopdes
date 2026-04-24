<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login Koperasi - Sistem Manajemen Pinjaman</title>
	<meta name="description" content="Sistem Informasi Koperasi Desa. Kelola pinjaman, angsuran, dan data nasabah dengan mudah dan aman.">
	<meta name="keywords" content="koperasi, pinjaman pulsa, manajemen nasabah, koperasi desa">
	<meta name="author" content="Nama Koperasi Anda">
	<meta property="og:type" content="website">
	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:title" content="Login Koperasi - Sistem Manajemen Digital">
	<meta property="og:description" content="Akses portal manajemen koperasi untuk pengelolaan nasabah dan transaksi pinjaman pulsa secara real-time.">
	<meta property="og:image" content="{{ asset('images/logo-ab.png') }}">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="Login Koperasi - Sistem Manajemen Digital">
	<meta name="twitter:description" content="Kelola transaksi koperasi dengan lebih mudah dan transparan.">
	<meta name="twitter:image" content="{{ asset('images/logo-ab.png') }}">

	<link rel="icon" type="image/png" href="{{ asset('images/logo-ab.png') }}">

	<link rel="manifest" href="{{ asset('manifest.json') }}">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="apple-mobile-web-app-title" content="Kopdes">
	<meta name="mobile-web-app-capable" content="yes">

	@vite(['resources/css/app.css', 'resources/js/app.js'])

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
				@csrf
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
						<span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
						<input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
						<span class="input-group-text bg-light" id="togglePassword">
							<i class="fas fa-eye" id="eyeIcon"></i>
						</span>
					</div>
				</div>

				<button type="submit" class="btn btn-primary btn-login mb-3">
					<i class="fas fa-sign-in-alt me-2"></i>MASUK
				</button>

				<div id="installApp" class="mt-3 text-center" style="display: none;">
					<button type="button" class="btn btn-outline-primary rounded-pill w-100" id="btnInstall">
						<i class="bi bi-download me-2"></i> Instal Aplikasi Koperasi
					</button>
				</div>
			</form>
		</div>
	</div>

	<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>


	<script>
		// --- 1. Inisialisasi Variabel Global ---

		let deferredPrompt;
		const installBox = document.getElementById('installApp');
		const btnInstall = document.getElementById('btnInstall');

		console.log("Script PWA berjalan, menunggu event...");

		window.addEventListener('beforeinstallprompt', (e) => {
			console.log("Event beforeinstallprompt TERDETEKSI!");
			e.preventDefault();
			deferredPrompt = e;

			// Paksa munculkan div
			installBox.style.setProperty('display', 'block', 'important');
		});


		// --- 2. Registrasi Service Worker (PWA) ---
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', () => {
				navigator.serviceWorker.register("{{ asset('sw.js') }}")
					.then(reg => console.log("SW Berhasil Terdaftar"))
					.catch(err => console.error("SW Gagal:", err));
			});
		}

		// // --- 3. Logika Instalasi PWA ---
		// window.addEventListener('beforeinstallprompt', (e) => {
		// 	// Mencegah browser memunculkan prompt otomatis
		// 	e.preventDefault();
		// 	// Simpan event agar bisa dipicu nanti via tombol
		// 	deferredPrompt = e;
		// 	// Tampilkan tombol instalasi kita
		// 	if (installBox) {
		// 		installBox.style.setProperty('display', 'block', 'important');
		// 	}
		// 	console.log("PWA siap diinstal");
		// });

		if (btnInstall) {
			btnInstall.addEventListener('click', async () => {
				if (deferredPrompt) {
					deferredPrompt.prompt();
					const {
						outcome
					} = await deferredPrompt.userChoice;
					if (outcome === 'accepted') {
						installBox.style.display = 'none';
					}
					deferredPrompt = null;
				}
			});
		}

		// --- 4. Logika Login 24 Jam & UI (DOM Ready) ---
		document.addEventListener('DOMContentLoaded', function() {

			// A. Simpan Sesi ke LocalStorage (Jika Laravel Session Aktif)
			@if(Session::has('nik'))
			const loginData = {
				nik: "{{ Session::get('nik') }}",
				expiry: new Date().getTime() + (24 * 60 * 60 * 1000)
			};
			localStorage.setItem('user_session', JSON.stringify(loginData));
			@endif

			// B. Cek Auto-Redirect (Hanya jika sedang di halaman login)
			const session = JSON.parse(localStorage.getItem('user_session'));
			if (session) {
				const now = new Date().getTime();
				if (now < session.expiry) {
					// Sesi valid, langsung ke dashboard
					window.location.href = "{{ route('dashboard') }}";
					return; // Berhenti eksekusi sisa script jika redirect
				} else {
					// Sesi basi, hapus
					localStorage.removeItem('user_session');
				}
			}

			// C. Logika Toggle Password
			const togglePassword = document.querySelector('#togglePassword');
			const passwordField = document.querySelector('#password');
			const eyeIcon = document.querySelector('#eyeIcon');

			if (togglePassword && passwordField) {
				togglePassword.addEventListener('click', function() {
					const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
					passwordField.setAttribute('type', type);

					if (eyeIcon) {
						eyeIcon.classList.toggle('fa-eye');
						eyeIcon.classList.toggle('fa-eye-slash');
					}

					// Efek Animasi Kecil
					this.style.transform = 'scale(1.2)';
					setTimeout(() => {
						this.style.transform = 'scale(1)';
					}, 200);
				});

				// Efek Fokus Input
				passwordField.addEventListener('focus', () => {
					togglePassword.style.borderColor = '#4e73df';
					togglePassword.style.color = '#4e73df';
				});
				passwordField.addEventListener('blur', () => {
					togglePassword.style.borderColor = '';
					togglePassword.style.color = '';
				});
			}
		});
	</script>
</body>

</html>