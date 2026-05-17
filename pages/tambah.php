<?php
/**
 * File    : pages/tambah.php
 * Fungsi  : Form penambahan data pegawai baru
 *           Menggunakan class Pegawai (OOP) untuk operasi database
 * Library : Bootstrap 5 (UI), MySQLi (database)
 * OOP     : Instansiasi class Pegawai, memanggil method tambah()
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once '../config/db.php';

// Memuat class Pegawai untuk implementasi OOP
require_once '../config/PegawaiClass.php';

// Membuat objek dari class Pegawai (instansiasi)
$pegawai = new Pegawai($conn);

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'pegawai';

// Variabel untuk menyimpan pesan error validasi
$error = '';

// Mengecek apakah form sudah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil dan membersihkan input dari form
    $nama                = trim($_POST['nama']);
    $jenis_kelamin       = $_POST['jenis_kelamin'];
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'];
    $usia                = (int) $_POST['usia'];
    $jabatan             = trim($_POST['jabatan']);
    $tanggal_bergabung   = $_POST['tanggal_bergabung'];

    // Validasi: semua field wajib diisi
    if (empty($nama) || empty($jabatan) || $usia <= 0 ||
        empty($jenis_kelamin) || empty($pendidikan_terakhir) ||
        empty($tanggal_bergabung)) {
        $error = "Semua field wajib diisi dengan benar.";

    } else {
        // Menggunakan method tambah() dari class Pegawai (OOP)
        // Data dikemas dalam array asosiatif sebagai struktur data
        $data_baru = [
            'nama'                => $nama,
            'jenis_kelamin'       => $jenis_kelamin,
            'pendidikan_terakhir' => $pendidikan_terakhir,
            'usia'                => $usia,
            'tanggal_bergabung'   => $tanggal_bergabung,
            'jabatan'             => $jabatan
        ];

        $hasil = $pegawai->tambah($data_baru);

        if ($hasil) {
            header("Location: ../index.php?pesan=tambah");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}

// Prefix path untuk sidebar (file ini ada di /pages/)
$dari_pages = true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai - SiPegawai</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

    <?php require_once '../includes/sidebar.php'; ?>

    <div class="main-content">

        <!-- Header -->
        <header class="page-header">
            <div class="page-header-left">
                <!-- Breadcrumb navigasi -->
                <div class="breadcrumb">
                    <a href="../index.php">Data Pegawai</a>
                    <span>›</span>
                    <span>Tambah Pegawai</span>
                </div>
                <h1>Tambah Pegawai Baru</h1>
                <p>Lengkapi seluruh informasi pegawai di bawah ini</p>
            </div>
            <div class="page-header-right">
                <div class="header-avatar">AD</div>
            </div>
        </header>

        <!-- Konten utama -->
        <div class="page-body">

            <!-- Pesan error validasi -->
            <?php if ($error): ?>
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px;
                        padding:12px 16px; color:#dc2626; font-size:13px;
                        margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <!-- Card Form -->
            <div class="card">
                <div class="card-body">

                    <!-- Judul section -->
                    <div style="margin-bottom:24px;">
                        <span class="form-section-title">Informasi Pegawai</span>
                    </div>

                    <!-- Form input -->
                    <form method="POST" action="">

                        <div class="form-grid">

                            <!-- Field Nama Lengkap -->
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama lengkap"
                                       value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>"
                                       required>
                            </div>

                            <!-- Field Jabatan -->
                            <div class="form-group">
                                <label class="form-label">Jabatan</label>
                                <input type="text"
                                       name="jabatan"
                                       class="form-control"
                                       placeholder="Masukkan jabatan"
                                       value="<?= isset($_POST['jabatan']) ? htmlspecialchars($_POST['jabatan']) : '' ?>"
                                       required>
                            </div>

                            <!-- Field Jenis Kelamin -->
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki"
                                        <?= (isset($_POST['jenis_kelamin']) &&
                                            $_POST['jenis_kelamin'] === 'Laki-laki')
                                            ? 'selected' : '' ?>>
                                        Laki-laki
                                    </option>
                                    <option value="Perempuan"
                                        <?= (isset($_POST['jenis_kelamin']) &&
                                            $_POST['jenis_kelamin'] === 'Perempuan')
                                            ? 'selected' : '' ?>>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <!-- Field Usia -->
                            <div class="form-group">
                                <label class="form-label">Usia</label>
                                <input type="number"
                                       name="usia"
                                       class="form-control"
                                       placeholder="Masukkan usia"
                                       min="17" max="70"
                                       value="<?= isset($_POST['usia']) ? htmlspecialchars($_POST['usia']) : '' ?>"
                                       required>
                            </div>

                            <!-- Field Pendidikan Terakhir -->
                            <div class="form-group full-width">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir"
                                        class="form-control"
                                        style="max-width:300px;"
                                        required>
                                    <option value="">Pilih Pendidikan</option>
                                    <?php
                                    // Array struktur data untuk pilihan pendidikan
                                    $pendidikan_list = ['SMA', 'D3', 'S1', 'S2', 'S3'];
                                    foreach ($pendidikan_list as $p):
                                    ?>
                                    <option value="<?= $p ?>"
                                        <?= (isset($_POST['pendidikan_terakhir']) &&
                                            $_POST['pendidikan_terakhir'] === $p)
                                            ? 'selected' : '' ?>>
                                        <?= $p ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Field Tanggal Bergabung -->
                            <div class="form-group full-width">
                                <label class="form-label">Tanggal Bergabung</label>
                                <input type="date"
                                    name="tanggal_bergabung"
                                    class="form-control"
                                    style="max-width:300px;"
                                    value="<?= isset($_POST['tanggal_bergabung']) ? htmlspecialchars($_POST['tanggal_bergabung']) : date('Y-m-d') ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Tombol aksi -->
                        <div style="display:flex; gap:10px; margin-top:28px;
                                    justify-content:flex-end;">
                            <a href="../index.php" class="btn-secondary">Batal</a>
                            <button type="submit" class="btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>