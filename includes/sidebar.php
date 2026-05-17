<?php
/**
 * File: includes/sidebar.php
 * Fungsi: Komponen sidebar navigasi yang digunakan di semua halaman
 * Prinsip: DRY (Don't Repeat Yourself) - dibuat sekali, dipanggil di banyak halaman
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

// Menentukan prefix path berdasarkan lokasi file pemanggil
// Halaman di /pages/ butuh '../' untuk naik satu level
// Halaman di root (index.php, dashboard.php) tidak butuh prefix
$prefix = (isset($dari_pages) && $dari_pages === true) ? '../' : '';
?>

<aside class="sidebar">

    <!-- Logo dan nama aplikasi -->
    <div class="sidebar-brand">
        <div class="sidebar-logo">SP</div>
        <div class="sidebar-brand-text">
            <span class="brand-name">SiPegawai</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>

    <!-- Menu navigasi utama -->
    <nav class="sidebar-nav">
        <span class="sidebar-nav-label">MENU UTAMA</span>

        <!-- Menu Dashboard -->
        <a href="<?= $prefix ?>dashboard.php"
           class="sidebar-nav-item <?= ($halaman_aktif === 'dashboard') ? 'active' : '' ?>">
            <!-- Icon dashboard (SVG inline) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Menu Data Pegawai -->
        <a href="<?= $prefix ?>index.php"
           class="sidebar-nav-item <?= ($halaman_aktif === 'pegawai') ? 'active' : '' ?>">
            <!-- Icon pegawai (SVG inline) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Data Pegawai</span>
        </a>

        <!-- Menu Settings -->
        <a href="<?= $prefix ?>settings.php"
   class="sidebar-nav-item <?= ($halaman_aktif === 'settings') ? 'active' : '' ?>">
            <!-- Icon settings (SVG inline) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06
                         a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09
                         A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83
                         l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                         A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83
                         l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09
                         a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83
                         l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09
                         a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Tombol Logout di bawah sidebar -->
    <div class="sidebar-footer">
        <a href="<?= $prefix ?>login.php" class="sidebar-logout">
            <!-- Icon logout (SVG inline) -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Logout</span>
        </a>
    </div>

</aside>