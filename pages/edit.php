<?php
/**
 * File    : pages/edit.php
 * Fungsi  : Form edit dan update data pegawai berdasarkan ID
 *           Menggunakan class Pegawai (OOP) untuk operasi database
 * Library : Bootstrap 5 (UI), MySQLi (database)
 * OOP     : Instansiasi class Pegawai, memanggil method
 *           ambilBerdasarkanId() dan ubah()
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once '../config/db.php';

// Memuat class Pegawai untuk implementasi OOP
require_once '../config/PegawaiClass.php';

// Membuat objek dari class Pegawai (instansiasi)
$pegawai = new Pegawai($conn);

$halaman_aktif = 'pegawai';
$dari_pages    = true;

// Mengambil ID dari URL dan validasi
$id = (int) $_GET['id'];
if ($id <= 0) {
    header("Location: ../index.php");
    exit();
}

// Menggunakan method ambilBerdasarkanId() dari class Pegawai (OOP)
$data = $pegawai->ambilBerdasarkanId($id);

// Jika data tidak ditemukan, kembali ke index
if (!$data) {
    header("Location: ../index.php");
    exit();
}

$error = '';

// Memproses form ketika dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama                = trim($_POST['nama']);
    $jenis_kelamin       = $_POST['jenis_kelamin'];
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'];
    $usia                = (int) $_POST['usia'];
    $jabatan             = trim($_POST['jabatan']);
    $tanggal_bergabung   = $_POST['tanggal_bergabung'];

    // Validasi input
    if (empty($nama) || empty($jabatan) || $usia <= 0 ||
        empty($jenis_kelamin) || empty($pendidikan_terakhir)) {
        $error = "Semua field wajib diisi dengan benar.";
    } else {
        // Menggunakan method ubah() dari class Pegawai (OOP)
        // Data dikemas dalam array asosiatif sebagai struktur data
        $data_update = [
            'nama'                => $nama,
            'jenis_kelamin'       => $jenis_kelamin,
            'pendidikan_terakhir' => $pendidikan_terakhir,
            'usia'                => $usia,
            'tanggal_bergabung'   => $tanggal_bergabung,
            'jabatan'             => $jabatan
        ];

        $hasil = $pegawai->ubah($id, $data_update);

        if ($hasil) {
            header("Location: ../index.php?pesan=edit");
            exit();
        } else {
            $error = "Gagal mengubah data: " . mysqli_error($conn);
        }
    }

    // Update nilai $data agar form tetap terisi dengan input terbaru saat error
    $data['nama']                = $_POST['nama'];
    $data['jenis_kelamin']       = $_POST['jenis_kelamin'];
    $data['pendidikan_terakhir'] = $_POST['pendidikan_terakhir'];
    $data['usia']                = $_POST['usia'];
    $data['jabatan']             = $_POST['jabatan'];
    $data ['tanggal_bergabung']  = $_POST['tanggal_bergabung'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai - SiPegawai</title>
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
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="../index.php">Data Pegawai</a>
                    <span>›</span>
                    <span>Edit Pegawai</span>
                </div>
                <h1>Edit Data Pegawai</h1>
                <p>Perbarui informasi pegawai yang diperlukan</p>
            </div>
            <div class="page-header-right">
                <div class="header-avatar">AD</div>
            </div>
        </header>

        <!-- Konten -->
        <div class="page-body">

            <!-- Pesan error -->
            <?php if ($error): ?>
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;
                        padding:12px 16px;color:#dc2626;font-size:13px;
                        margin-bottom:20px;display:flex;align-items:center;gap:8px;">
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

                    <div style="margin-bottom:24px;">
                        <span class="form-section-title">Informasi Pegawai</span>
                        <p style="font-size:12px;color:#94a3b8;margin-top:6px;">
                            Terakhir diperbarui: <?= date('d F Y') ?> oleh Administrator
                        </p>
                    </div>

                    <form method="POST" action="">

                        <div class="form-grid">

                            <!-- Nama Lengkap -->
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nama']) ?>"
                                       required>
                            </div>

                            <!-- Jabatan -->
                            <div class="form-group">
                                <label class="form-label">Jabatan</label>
                                <input type="text"
                                       name="jabatan"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['jabatan']) ?>"
                                       required>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="Laki-laki"
                                        <?= $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>
                                        Laki-laki
                                    </option>
                                    <option value="Perempuan"
                                        <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <!-- Pendidikan Terakhir -->
                            <div class="form-group">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="form-control" required>
                                    <?php
                                    // Array struktur data pilihan pendidikan
                                    $pendidikan_list = ['SMA', 'D3', 'S1', 'S2', 'S3'];
                                    foreach ($pendidikan_list as $p):
                                    ?>
                                    <option value="<?= $p ?>"
                                        <?= $data['pendidikan_terakhir'] === $p ? 'selected' : '' ?>>
                                        <?= $p ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Usia -->
                            <div class="form-group full-width">
                                <label class="form-label">Usia</label>
                                <input type="number"
                                       name="usia"
                                       class="form-control"
                                       style="max-width:200px;"
                                       value="<?= htmlspecialchars($data['usia']) ?>"
                                       min="17" max="70"
                                       required>
                            </div>

                            <!-- Field Tanggal Bergabung -->
                            <div class="form-group full-width">
                                <label class="form-label">Tanggal Bergabung</label>
                                <input type="date"
                                    name="tanggal_bergabung"
                                    class="form-control"
                                    style="max-width:300px;"
                                    value="<?= htmlspecialchars($data['tanggal_bergabung']) ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Tombol aksi -->
                        <div style="display:flex;gap:10px;margin-top:28px;justify-content:flex-end;">
                            <a href="../index.php" class="btn-secondary">Batal</a>
                            <button type="submit" class="btn-primary">
                                Update Data
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