<?php
/**
 * File    : settings.php
 * Fungsi  : Halaman pengaturan sistem dan informasi profil administrator
 *           Menampilkan statistik sistem dan panduan penggunaan
 * Library : Bootstrap 5 (UI), MySQLi (database)
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once 'config/db.php';
$halaman_aktif = 'settings';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - SiPegawai</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <?php require_once 'includes/sidebar.php'; ?>
    <div class="main-content">
        <header class="page-header">
            <div class="page-header-left">
                <h1>Pengaturan</h1>
                <p>Kelola konfigurasi sistem dan profil admin</p>
            </div>
            <div class="page-header-right">
                <div class="header-avatar">AD</div>
            </div>
        </header>
        <div class="page-body">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

                <!-- Profil Admin -->
                <div class="card">
                    <div class="card-body">
                        <div style="font-size:15px;font-weight:700;color:#1e293b;
                                    border-bottom:2px solid #0000FF;display:inline-block;
                                    padding-bottom:8px;margin-bottom:20px;">
                            Profil Administrator
                        </div>
                        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
                            <div style="width:64px;height:64px;border-radius:50%;
                                        background:linear-gradient(135deg,#0000FF,#000080);
                                        display:flex;align-items:center;justify-content:center;
                                        color:white;font-size:22px;font-weight:700;">AD</div>
                            <div>
                                <div style="font-weight:700;font-size:16px;color:#1e293b;">Administrator</div>
                                <div style="font-size:13px;color:#64748b;">admin@sipegawai.com</div>
                                <div style="font-size:12px;color:#0000FF;margin-top:2px;">Super Admin</div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label">Nama Admin</label>
                            <input type="text" class="form-control" value="Administrator" readonly>
                        </div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="admin@sipegawai.com" readonly>
                        </div>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="Super Administrator" readonly>
                        </div>
                    </div>
                </div>

                <!-- Info Sistem -->
                <div class="card">
                    <div class="card-body">
                        <div style="font-size:15px;font-weight:700;color:#1e293b;
                                    border-bottom:2px solid #0000FF;display:inline-block;
                                    padding-bottom:8px;margin-bottom:20px;">
                            Informasi Sistem
                        </div>
                        <?php
                        // Query informasi statistik sistem
                        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pegawai"))['t'];
                        $laki  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pegawai WHERE jenis_kelamin='Laki-laki'"))['t'];
                        $perempuan = $total - $laki;
                        ?>
                        <div style="display:flex;flex-direction:column;gap:14px;">
                            <?php
                            // Array struktur data untuk info sistem
                            $info = [
                                ['label' => 'Nama Sistem',       'value' => 'SiPegawai Management System'],
                                ['label' => 'Versi',             'value' => 'v1.0.0'],
                                ['label' => 'Total Pegawai',     'value' => $total . ' orang'],
                                ['label' => 'Pegawai Laki-laki', 'value' => $laki . ' orang'],
                                ['label' => 'Pegawai Perempuan', 'value' => $perempuan . ' orang'],
                                ['label' => 'Database',          'value' => 'MySQL (db_pegawai)'],
                                ['label' => 'Server',            'value' => 'Apache (XAMPP)'],
                                ['label' => 'Bahasa',            'value' => 'PHP Native'],
                            ];
                            foreach ($info as $item):
                            ?>
                            <div style="display:flex;justify-content:space-between;
                                        align-items:center;padding:10px 0;
                                        border-bottom:1px solid #f1f5f9;">
                                <span style="font-size:13px;color:#64748b;"><?= $item['label'] ?></span>
                                <span style="font-size:13px;font-weight:600;color:#1e293b;"><?= $item['value'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Panduan Penggunaan -->
            <div class="card" style="margin-top:20px;">
                <div class="card-body">
                    <div style="font-size:15px;font-weight:700;color:#1e293b;
                                border-bottom:2px solid #0000FF;display:inline-block;
                                padding-bottom:8px;margin-bottom:20px;">
                        Panduan Penggunaan Sistem
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                        <?php
                        // Array struktur data panduan fitur
                        $panduan = [
                            ['icon' => '👥', 'judul' => 'Kelola Data Pegawai',
                             'desc' => 'Tambah, lihat, ubah, dan hapus data pegawai melalui menu Data Pegawai.'],
                            ['icon' => '📊', 'judul' => 'Dashboard Statistik',
                             'desc' => 'Lihat ringkasan data pegawai dalam bentuk grafik di halaman Dashboard.'],
                            ['icon' => '🔍', 'judul' => 'Pencarian & Filter',
                             'desc' => 'Gunakan search bar untuk mencari pegawai secara real-time tanpa perlu Enter.'],
                        ];
                        foreach ($panduan as $p):
                        ?>
                        <div style="background:#f8fafc;border-radius:10px;padding:16px;">
                            <div style="font-size:24px;margin-bottom:10px;"><?= $p['icon'] ?></div>
                            <div style="font-weight:600;font-size:14px;color:#1e293b;margin-bottom:6px;">
                                <?= $p['judul'] ?>
                            </div>
                            <div style="font-size:13px;color:#64748b;line-height:1.6;">
                                <?= $p['desc'] ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>