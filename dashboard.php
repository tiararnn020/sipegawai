<?php
/**
 * File: dashboard.php
 * Fungsi: Menampilkan ringkasan statistik data pegawai dalam bentuk grafik
 * Menggunakan: Bootstrap 5, Chart.js (library pre-existing), MySQLi prosedural
 * Grafik: Perbandingan Gender, Tingkat Pendidikan, Distribusi Usia
 * Author: [Tiara]
 * Tanggal: [17-05-2026]
 * Versi: 1.0.0
 */

// Memanggil file koneksi database
require_once 'config/db.php';

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'dashboard';

// =============================================
// QUERY 1: Total pegawai keseluruhan
// =============================================
$q_total  = "SELECT COUNT(*) as total FROM pegawai";
$r_total  = mysqli_query($conn, $q_total);
$total    = mysqli_fetch_assoc($r_total)['total'];

// =============================================
// QUERY 2: Jumlah pegawai laki-laki
// =============================================
$q_laki  = "SELECT COUNT(*) as jumlah FROM pegawai WHERE jenis_kelamin = 'Laki-laki'";
$r_laki  = mysqli_query($conn, $q_laki);
$laki    = mysqli_fetch_assoc($r_laki)['jumlah'];

// =============================================
// QUERY 3: Jumlah pegawai perempuan
// =============================================
$q_perempuan = "SELECT COUNT(*) as jumlah FROM pegawai WHERE jenis_kelamin = 'Perempuan'";
$r_perempuan = mysqli_query($conn, $q_perempuan);
$perempuan   = mysqli_fetch_assoc($r_perempuan)['jumlah'];

// =============================================
// QUERY 4: Jumlah per jenis kelamin (untuk grafik)
// Menggunakan GROUP BY sebagai struktur data agregasi
// =============================================
$q_jk    = "SELECT jenis_kelamin, COUNT(*) as jumlah FROM pegawai GROUP BY jenis_kelamin";
$r_jk    = mysqli_query($conn, $q_jk);
$label_jk = [];
$data_jk  = [];
while ($row = mysqli_fetch_assoc($r_jk)) {
    $label_jk[] = $row['jenis_kelamin'];
    $data_jk[]  = (int) $row['jumlah'];
}

// =============================================
// QUERY 5: Jumlah per pendidikan terakhir (untuk grafik)
// =============================================
$q_pend    = "SELECT pendidikan_terakhir, COUNT(*) as jumlah
              FROM pegawai
              GROUP BY pendidikan_terakhir
              ORDER BY FIELD(pendidikan_terakhir,'SMA','D3','S1','S2','S3')";
$r_pend    = mysqli_query($conn, $q_pend);
$label_pend = [];
$data_pend  = [];
while ($row = mysqli_fetch_assoc($r_pend)) {
    $label_pend[] = $row['pendidikan_terakhir'];
    $data_pend[]  = (int) $row['jumlah'];
}

// =============================================
// QUERY 6: Jumlah per kelompok usia (untuk grafik)
// Menggunakan CASE WHEN untuk pengelompokan
// =============================================
$q_usia = "SELECT
    CASE
        WHEN usia BETWEEN 20 AND 30 THEN '20-30'
        WHEN usia BETWEEN 31 AND 40 THEN '31-40'
        WHEN usia BETWEEN 41 AND 50 THEN '41-50'
        ELSE '51+'
    END AS kelompok,
    COUNT(*) as jumlah
    FROM pegawai
    GROUP BY kelompok
    ORDER BY MIN(usia) ASC";
$r_usia    = mysqli_query($conn, $q_usia);
$label_usia = [];
$data_usia  = [];
while ($row = mysqli_fetch_assoc($r_usia)) {
    $label_usia[] = $row['kelompok'];
    $data_usia[]  = (int) $row['jumlah'];
}

// Mengubah array PHP ke JSON untuk Chart.js
$json_label_jk   = json_encode($label_jk);
$json_data_jk    = json_encode($data_jk);
$json_label_pend = json_encode($label_pend);
$json_data_pend  = json_encode($data_pend);
$json_label_usia = json_encode($label_usia);
$json_data_usia  = json_encode($data_usia);

// Menghitung persentase (hindari pembagian dengan nol)
$persen_laki      = $total > 0 ? round(($laki / $total) * 100, 1) : 0;
$persen_perempuan = $total > 0 ? round(($perempuan / $total) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SiPegawai</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

    <?php require_once 'includes/sidebar.php'; ?>

    <div class="main-content">

        <!-- Header -->
        <header class="page-header">
            <div class="page-header-left">
                <h1>Dashboard</h1>
                <p>Ringkasan data seluruh pegawai</p>
            </div>
            <div class="page-header-right">
                <!-- Tanggal hari ini -->
                <span id="waktuSekarang" style="font-size:13px; color:#64748b;">
                    <?= date('l, d F Y') ?>
                </span>
                <!-- Avatar admin -->
                <div class="header-avatar">AD</div>
            </div>
        </header>

        <!-- Konten utama -->
        <div class="page-body">

    <!-- Toast -->
    <?php if (isset($_GET['pesan'])): ?>
    <div id="toastContainer" style="position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;">
        <div id="toastMsg" style="display:flex;align-items:center;gap:10px;padding:14px 18px;background:white;border-left:4px solid #01F108;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.12);font-size:14px;font-weight:500;color:#006400;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#01F108" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Selamat datang di Dashboard.</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">

        <!-- Total Pegawai -->
        <div style="background:white; border-radius:12px; border:1px solid #e2e8f0;
                    border-left:4px solid #0000FF; padding:20px 24px;
                    display:flex; justify-content:space-between; align-items:center;
                    position:relative; overflow:hidden;">
            <div style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                        width:70px;height:70px;border-radius:50%;
                        background:radial-gradient(circle,rgba(0,0,255,0.12),transparent);
                        filter:blur(10px);pointer-events:none;"></div>
            <div>
                <div style="font-size:13px;color:#64748b;margin-bottom:8px;">Total Pegawai</div>
                <div style="font-size:28px;font-weight:700;color:#1e293b;"><?= $total ?></div>
            </div>
            <div style="width:40px;height:40px;border-radius:10px;
                        background:linear-gradient(135deg,#0000FF,#000080);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>

        <!-- Laki-laki -->
        <div style="background:white; border-radius:12px; border:1px solid #e2e8f0;
                    border-left:4px solid #0000FF; padding:20px 24px;
                    display:flex; justify-content:space-between; align-items:center;
                    position:relative; overflow:hidden;">
            <div style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                        width:70px;height:70px;border-radius:50%;
                        background:radial-gradient(circle,rgba(0,0,255,0.12),transparent);
                        filter:blur(10px);pointer-events:none;"></div>
            <div>
                <div style="font-size:13px;color:#64748b;margin-bottom:8px;">Pegawai Laki-laki</div>
                <div style="font-size:28px;font-weight:700;color:#0000FF;"><?= $laki ?></div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;"><?= $persen_laki ?>% dari total</div>
            </div>
            <div style="width:40px;height:40px;border-radius:10px;
                        background:linear-gradient(135deg,#0000FF,#000080);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M20 21a8 8 0 1 0-16 0"/>
                </svg>
            </div>
        </div>

        <!-- Perempuan -->
        <div style="background:white; border-radius:12px; border:1px solid #e2e8f0;
                    border-left:4px solid #00AB21; padding:20px 24px;
                    display:flex; justify-content:space-between; align-items:center;
                    position:relative; overflow:hidden;">
            <div style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                        width:70px;height:70px;border-radius:50%;
                        background:radial-gradient(circle,rgba(0,171,33,0.12),transparent);
                        filter:blur(10px);pointer-events:none;"></div>
            <div>
                <div style="font-size:13px;color:#64748b;margin-bottom:8px;">Pegawai Perempuan</div>
                <div style="font-size:28px;font-weight:700;color:#00AB21;"><?= $perempuan ?></div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;"><?= $persen_perempuan ?>% dari total</div>
            </div>
            <div style="width:40px;height:40px;border-radius:10px;
                        background:linear-gradient(135deg,#00AB21,#01F108);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M20 21a8 8 0 1 0-16 0"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- 3 Grafik -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">

        <!-- Grafik 1 -->
        <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;padding:20px 24px;">
            <div style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:16px;">
                Perbandingan Gender
            </div>
            <canvas id="grafikJK" height="220"></canvas>
        </div>

        <!-- Grafik 2 -->
        <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;padding:20px 24px;">
            <div style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:16px;">
                Tingkat Pendidikan Terakhir
            </div>
            <canvas id="grafikPend" height="220"></canvas>
        </div>

        <!-- Grafik 3 -->
        <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;padding:20px 24px;">
            <div style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:16px;">
                Distribusi Usia Pegawai
            </div>
            <canvas id="grafikUsia" height="220"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/**
 * Script: Inisialisasi 3 grafik menggunakan Chart.js
 * Grafik 1: Doughnut - Perbandingan Gender
 * Grafik 2: Horizontal Bar - Pendidikan Terakhir
 * Grafik 3: Vertical Bar - Distribusi Usia
 * Prinsip DRY: fungsi buatGrafik() dipakai ulang
 */

const labelJK   = <?= $json_label_jk ?>;
const dataJK    = <?= $json_data_jk ?>;
const labelPend = <?= $json_label_pend ?>;
const dataPend  = <?= $json_data_pend ?>;
const labelUsia = <?= $json_label_usia ?>;
const dataUsia  = <?= $json_data_usia ?>;

// Grafik 1: Vertical Bar - Gender
new Chart(document.getElementById('grafikJK'), {
    type: 'bar',
    data: {
        labels: labelJK,
        datasets: [{
            label: 'Jumlah Pegawai',
            data: dataJK,
            backgroundColor: ['#0000FF', '#00AB21'],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 11 } },
                grid: { display: false }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});

// Grafik 2: Horizontal Bar - Pendidikan (5 kategori, horizontal lebih mudah dibaca)
new Chart(document.getElementById('grafikPend'), {
    type: 'bar',
    data: {
        labels: labelPend,
        datasets: [{
            label: 'Jumlah Pegawai',
            data: dataPend,
            backgroundColor: [
                '#03045a',
                '#0172b0',
                '#0090bf',
                '#45c2db',
                '#c3e7ef'
            ],
            borderRadius: 4,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 11 } },
                grid: { display: false }
            },
            y: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});

// Grafik 3: Vertical Bar - Usia (distribusi kelompok usia)
new Chart(document.getElementById('grafikUsia'), {
    type: 'bar',
    data: {
        labels: labelUsia,
        datasets: [{
            label: 'Jumlah Pegawai',
            data: dataUsia,
            backgroundColor: ['#0000FF', '#000080', '#00AB21', '#01F108'],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 11 } },
                grid: { display: false }
            },
            x: {
                ticks: { font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
</script>
<script>
/**
 * Script: Jam Real-time
 * Fungsi: Menampilkan tanggal dan waktu yang berjalan setiap detik
 */
function updateWaktu() {
    const sekarang = new Date();

    // Array nama hari dalam bahasa Indonesia
    const namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    // Array nama bulan dalam bahasa Indonesia
    const namaBulan = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];

    const hari    = namaHari[sekarang.getDay()];
    const tanggal = sekarang.getDate();
    const bulan   = namaBulan[sekarang.getMonth()];
    const tahun   = sekarang.getFullYear();

    // Format jam:menit:detik dengan padding nol di depan jika perlu
    const jam     = String(sekarang.getHours()).padStart(2, '0');
    const menit   = String(sekarang.getMinutes()).padStart(2, '0');
    const detik   = String(sekarang.getSeconds()).padStart(2, '0');

    const teks = hari + ', ' + tanggal + ' ' + bulan + ' ' + tahun +
                 ' — ' + jam + ':' + menit + ':' + detik;

    document.getElementById('waktuSekarang').textContent = teks;
}

// Jalankan sekali langsung, lalu update setiap 1 detik
updateWaktu();
setInterval(updateWaktu, 1000);
</script>
</body>
</html>