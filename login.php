<?php
/**
 * File: login.php
 * Fungsi: Halaman login aplikasi SiPegawai
 * Catatan: Halaman ini dibuat sebagai bagian proyek namun tidak
 *          diikutsertakan dalam demonstrasi ujian. Login menggunakan
 *          pengecekan username & password hardcoded (tanpa database user).
 * Menggunakan: Bootstrap 5, CSS custom (style.css)
 * Author: [Tiara]
 * Tanggal: [17-05-2026]
 * Versi: 1.0.0
 */

// Kredensial admin yang valid (hardcoded, tanpa tabel database)
$admin_email    = 'admin@sipegawai.com';
$admin_password = 'admin123';

// Variabel untuk menyimpan pesan error
$error = '';

// Mengecek apakah form login sudah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil input dari form
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi: cocokkan dengan kredensial yang sudah ditentukan
    if ($email === $admin_email && $password === $admin_password) {
        // Jika cocok, arahkan ke halaman dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Jika tidak cocok, tampilkan pesan error
        $error = "Email atau password salah. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiPegawai</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS - Library pre-existing untuk komponen UI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Custom aplikasi -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        /* Style khusus halaman login - layout split screen */
        body {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
        }

        /* Panel kiri: form login */
        .login-left {
            width: 480px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px;
            background: white;
        }

        /* Panel kanan: ilustrasi biru */
        .login-right {
            flex: 1;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            color: white;
            text-align: center;
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }

        .login-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0000FF, #000080);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 14px;
        }

        .login-logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #0000FF;
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }

        .login-label {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
            display: block;
        }

        .login-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border 0.15s;
            margin-bottom: 16px;
        }

        .login-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .login-input.error {
            border-color: #ef4444;
        }

        .login-btn {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #0000FF, #000080)
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 8px;
        }

        .login-btn:hover {
            opacity: 0.9
        }

        .login-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            color: #dc2626;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .right-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.3;
        }

        .right-desc {
            font-size: 14px;
            opacity: 0.85;
            line-height: 1.7;
            max-width: 340px;
        }

        .right-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            margin-top: 24px;
        }
    </style>
</head>
<body>

<!-- Panel Kiri: Form Login -->
<div class="login-left">

    <!-- Logo -->
    <div class="login-logo">
        <div class="login-logo-icon">SP</div>
        <span class="login-logo-text">SiPegawai</span>
    </div>

    <h1 class="login-title">Selamat Datang Kembali</h1>
    <p class="login-subtitle">Silakan masuk ke akun Anda untuk melanjutkan.</p>

    <!-- Pesan error jika login gagal -->
    <?php if ($error): ?>
    <div class="login-error">
        <!-- Icon warning -->
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="">

        <label class="login-label">Email Perusahaan</label>
        <input type="email"
               name="email"
               class="login-input <?= $error ? 'error' : '' ?>"
               placeholder="admin@sipegawai.com"
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
               required>

        <label class="login-label">Kata Sandi</label>
        <input type="password"
               name="password"
               class="login-input <?= $error ? 'error' : '' ?>"
               placeholder="Masukkan password"
               required>

        <button type="submit" class="login-btn">Masuk</button>

    </form>

</div>

<!-- Panel Kanan: Ilustrasi -->
<div class="login-right">
    <div class="right-title">Solusi Terpadu<br>Manajemen SDM</div>
    <p class="right-desc">
        Optimalkan efisiensi operasional perusahaan Anda dengan
        platform manajemen data pegawai yang modern, aman, dan
        mudah digunakan.
    </p>
    <div class="right-badge">
        ✦ Dipercaya oleh 500+ Perusahaan di Indonesia
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>