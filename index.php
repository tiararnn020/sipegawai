<?php
/**
 * File    : index.php
 * Fungsi  : Halaman utama daftar data pegawai
 *           Fitur: pencarian real-time, filter kolom per kategori,
 *                  pagination, modal hapus dua tahap, toast notifikasi,
 *                  export Excel, print
 * Library : Bootstrap 5 (UI), PhpSpreadsheet (export Excel),
 *           MySQLi (database)
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once 'config/db.php';

$halaman_aktif = 'pegawai';

// =============================================
// FITUR PENCARIAN
// Mengambil kata kunci dari URL (parameter GET)
// =============================================
$cari  = isset($_GET['cari']) ? trim($_GET['cari']) : '';

// Filter kolom
$filter_jk   = isset($_GET['jk'])   ? $_GET['jk']   : '';
$filter_pend = isset($_GET['pend']) ? $_GET['pend'] : '';
$filter_usia = isset($_GET['usia']) ? $_GET['usia'] : '';
$filter_jab  = isset($_GET['jab'])  ? trim($_GET['jab'])  : '';

// =============================================
// FITUR PAGINATION
// Menentukan berapa data per halaman dan halaman mana yang aktif
// =============================================
$per_halaman  = 50;
$halaman_ini  = isset($_GET['hal']) ? (int) $_GET['hal'] : 1;
if ($halaman_ini < 1) $halaman_ini = 1;
$offset       = ($halaman_ini - 1) * $per_halaman;

// Membangun kondisi WHERE berdasarkan filter yang aktif
$where_parts = [];

if ($cari !== '') {
    $cari_aman     = mysqli_real_escape_string($conn, $cari);
    $where_parts[] = "(nama LIKE '%$cari_aman%' OR jabatan LIKE '%$cari_aman%')";
}
if ($filter_jk !== '') {
    $jk_aman       = mysqli_real_escape_string($conn, $filter_jk);
    $where_parts[] = "jenis_kelamin = '$jk_aman'";
}
if ($filter_pend !== '') {
    $pend_aman     = mysqli_real_escape_string($conn, $filter_pend);
    $where_parts[] = "pendidikan_terakhir = '$pend_aman'";
}
if ($filter_usia !== '') {
    // Pengelompokan usia menggunakan CASE WHEN
    switch ($filter_usia) {
        case '17-25': $where_parts[] = "usia BETWEEN 17 AND 25"; break;
        case '26-35': $where_parts[] = "usia BETWEEN 26 AND 35"; break;
        case '36-45': $where_parts[] = "usia BETWEEN 36 AND 45"; break;
        case '46+':   $where_parts[] = "usia >= 46"; break;
    }
}
if ($filter_jab !== '') {
    $jab_aman      = mysqli_real_escape_string($conn, $filter_jab);
    $where_parts[] = "jabatan LIKE '%$jab_aman%'";
}

$where_sql = count($where_parts) > 0 ? "WHERE " . implode(" AND ", $where_parts) : "";

$q_total     = "SELECT COUNT(*) as total FROM pegawai $where_sql";
$r_total     = mysqli_query($conn, $q_total);
$total_data  = mysqli_fetch_assoc($r_total)['total'];
$total_halaman = ceil($total_data / $per_halaman);

$query = "SELECT * FROM pegawai $where_sql ORDER BY id ASC LIMIT $per_halaman OFFSET $offset";
$result = mysqli_query($conn, $query);

// =============================================
// FUNGSI: Membuat warna avatar dari nama
// Menggunakan array struktur data warna
// =============================================
function getAvatarColor($nama) {
    // Array struktur data warna untuk avatar
    $warna = ['#2563eb','#7c3aed','#db2777','#059669','#d97706','#dc2626'];
    // Mengambil indeks berdasarkan karakter pertama nama
    $index = ord(strtoupper($nama[0])) % count($warna);
    return $warna[$index];
}

// =============================================
// FUNGSI: Mengambil 2 huruf inisial dari nama
// =============================================
function getInisial($nama) {
    $kata = explode(' ', trim($nama));
    if (count($kata) >= 2) {
        // Jika nama terdiri dari 2 kata atau lebih, ambil huruf pertama masing-masing
        return strtoupper($kata[0][0] . $kata[1][0]);
    }
    // Jika hanya 1 kata, ambil 2 huruf pertama
    return strtoupper(substr($kata[0], 0, 2));
}

// Mengambil pesan notifikasi dari URL (dikirim setelah tambah/edit/hapus)
$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - SiPegawai</title>
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
                <h1>Data Pegawai</h1>
                <p>Kelola seluruh data pegawai perusahaan</p>
            </div>
            <div class="page-header-right">
                <!-- Search bar pencarian pegawai -->
                <form method="GET" action="" style="margin:0;">
                    <div class="header-search">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text"
                            id="inputCari"
                            placeholder="Cari nama pegawai..."
                            value="<?= htmlspecialchars($cari) ?>"
                            autocomplete="off">
                    </div>
                </form>
                <!-- Avatar admin -->
                <div class="header-avatar">AD</div>
            </div>
        </header>

        <!-- Konten utama -->
        <div class="page-body">

            <!-- Toast Notification -->
            <?php if ($pesan): ?>
            <div id="toastContainer" style="position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;">
                <div id="toastMsg" style="
                    display:flex;
                    align-items:center;
                    gap:12px;
                    padding:14px 20px;
                    background: linear-gradient(135deg, rgba(0,171,33,0.15), rgba(1,241,8,0.10));
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(0,171,33,0.3);
                    border-left: 4px solid #00AB21;
                    border-radius: 14px;
                    box-shadow: 0 8px 32px rgba(0,171,33,0.15);
                    font-size:14px;
                    font-weight:600;
                    color:#005010;
                ">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00AB21" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span><?php
                        if ($pesan === 'tambah') echo 'Data pegawai berhasil ditambahkan.';
                        if ($pesan === 'edit')   echo 'Data pegawai berhasil diubah.';
                        if ($pesan === 'hapus')  echo 'Data pegawai berhasil dihapus.';
                    ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Toolbar: tombol aksi -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">

                <!-- Kiri: info filter aktif -->
                <div style="font-size:13px;color:#64748b;">
                    <?php if ($filter_jk || $filter_pend || $filter_usia || $cari): ?>
                    <span>Filter aktif —
                        <a href="index.php" style="color:#0000FF;font-weight:500;">Reset semua</a>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Kanan: tombol-tombol aksi -->
                <div style="display:flex;gap:10px;align-items:center;">

                    <!-- Tombol Print -->
                    <button type="button"
                            onclick="window.print()"
                            style="display:inline-flex;align-items:center;gap:6px;
                                padding:9px 18px;background:white;color:#1e293b;
                                border:1px solid #e2e8f0;border-radius:50px;
                                font-size:14px;font-weight:600;cursor:pointer;
                                transition:background 0.15s;font-family:Urbanist,sans-serif;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Print
                    </button>

                    <!-- Tombol Export Excel -->
                    <a href="export_excel.php"
                    style="display:inline-flex;align-items:center;gap:6px;
                            padding:9px 18px;background:linear-gradient(135deg,#00AB21,#007015);
                            color:white;border:none;border-radius:50px;
                            font-size:14px;font-weight:600;cursor:pointer;
                            text-decoration:none;transition:opacity 0.15s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Export Excel
                    </a>

                    <!-- Tombol Tambah Pegawai -->
                    <a href="pages/tambah.php" class="btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah Pegawai
                    </a>

                </div>
            </div>
            
            <!-- Tabel Data Pegawai -->
            <div class="card">
                <div class="card-body" style="padding:0;">
                    <table class="table-pegawai">
                        <thead>
                            <tr>
                                <th style="width:50px;padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">NO.</th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">NAMA PEGAWAI</th>

                                <!-- Header Jenis Kelamin dengan filter -->
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span>JENIS KELAMIN</span>
                                        <div style="position:relative;display:inline-block;">
                                            <button type="button"
                                                    onclick="toggleDropdown('ddJK')"
                                                    style="width:22px;height:22px;border-radius:6px;border:1px solid <?= $filter_jk ? '#0000FF' : '#e2e8f0' ?>;background:<?= $filter_jk ? '#eff6ff' : 'white' ?>;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="<?= $filter_jk ? '#0000FF' : '#94a3b8' ?>" stroke-width="2.5">
                                                    <line x1="4" y1="6" x2="20" y2="6"/>
                                                    <line x1="6" y1="12" x2="18" y2="12"/>
                                                    <line x1="8" y1="18" x2="16" y2="18"/>
                                                </svg>
                                            </button>
                                            <div id="ddJK"
                                                style="display:none;position:absolute;top:28px;left:0;
                                                        background:white;border:1px solid #e2e8f0;border-radius:12px;
                                                        box-shadow:0 8px 24px rgba(0,0,0,0.12);z-index:500;
                                                        min-width:160px;padding:8px;margin-top:2px;">
                                                <div style="font-size:11px;font-weight:600;color:#94a3b8;padding:4px 8px 8px;text-transform:uppercase;letter-spacing:0.05em;">Filter Gender</div>
                                                <?php foreach (['' => 'Semua Gender', 'Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'] as $val => $label): ?>
                                                <div onclick="terapkanFilter('jk','<?= $val ?>')"
                                                    style="padding:9px 12px;border-radius:8px;font-size:13px;cursor:pointer;
                                                            display:flex;align-items:center;justify-content:space-between;
                                                            font-weight:<?= $filter_jk===$val?'600':'400' ?>;
                                                            color:<?= $filter_jk===$val?'#0000FF':'#1e293b' ?>;
                                                            background:<?= $filter_jk===$val?'#eff6ff':'transparent' ?>;"
                                                    onmouseover="this.style.background='#f8fafc'"
                                                    onmouseout="this.style.background='<?= $filter_jk===$val?'#eff6ff':'transparent' ?>'">
                                                    <span><?= $label ?></span>
                                                    <?php if ($filter_jk===$val): ?>
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0000FF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <!-- Header Pendidikan dengan filter -->
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span>PENDIDIKAN</span>
                                        <div style="position:relative;display:inline-block;">
                                            <button type="button"
                                                    onclick="toggleDropdown('ddPend')"
                                                    style="width:22px;height:22px;border-radius:6px;border:1px solid <?= $filter_pend ? '#0000FF' : '#e2e8f0' ?>;background:<?= $filter_pend ? '#eff6ff' : 'white' ?>;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="<?= $filter_pend ? '#0000FF' : '#94a3b8' ?>" stroke-width="2.5">
                                                    <line x1="4" y1="6" x2="20" y2="6"/>
                                                    <line x1="6" y1="12" x2="18" y2="12"/>
                                                    <line x1="8" y1="18" x2="16" y2="18"/>
                                                </svg>
                                            </button>
                                            <div id="ddPend"
                                                style="display:none;position:absolute;top:28px;left:0;
                                                        background:white;border:1px solid #e2e8f0;border-radius:12px;
                                                        box-shadow:0 8px 24px rgba(0,0,0,0.12);z-index:500;
                                                        min-width:150px;padding:8px;margin-top:2px;">
                                                <div style="font-size:11px;font-weight:600;color:#94a3b8;padding:4px 8px 8px;text-transform:uppercase;letter-spacing:0.05em;">Filter Pendidikan</div>
                                                <?php foreach (['' => 'Semua', 'SMA'=>'SMA','D3'=>'D3','S1'=>'S1','S2'=>'S2','S3'=>'S3'] as $val => $label): ?>
                                                <div onclick="terapkanFilter('pend','<?= $val ?>')"
                                                    style="padding:9px 12px;border-radius:8px;font-size:13px;cursor:pointer;
                                                            display:flex;align-items:center;justify-content:space-between;
                                                            font-weight:<?= $filter_pend===$val?'600':'400' ?>;
                                                            color:<?= $filter_pend===$val?'#0000FF':'#1e293b' ?>;
                                                            background:<?= $filter_pend===$val?'#eff6ff':'transparent' ?>;"
                                                    onmouseover="this.style.background='#f8fafc'"
                                                    onmouseout="this.style.background='<?= $filter_pend===$val?'#eff6ff':'transparent' ?>'">
                                                    <span><?= $label ?></span>
                                                    <?php if ($filter_pend===$val): ?>
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0000FF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <!-- Header Usia dengan filter -->
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span>USIA</span>
                                        <div style="position:relative;display:inline-block;">
                                            <button type="button"
                                                    onclick="toggleDropdown('ddUsia')"
                                                    style="width:22px;height:22px;border-radius:6px;border:1px solid <?= $filter_usia ? '#0000FF' : '#e2e8f0' ?>;background:<?= $filter_usia ? '#eff6ff' : 'white' ?>;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="<?= $filter_usia ? '#0000FF' : '#94a3b8' ?>" stroke-width="2.5">
                                                    <line x1="4" y1="6" x2="20" y2="6"/>
                                                    <line x1="6" y1="12" x2="18" y2="12"/>
                                                    <line x1="8" y1="18" x2="16" y2="18"/>
                                                </svg>
                                            </button>
                                            <div id="ddUsia"
                                                style="display:none;position:absolute;top:28px;left:0;
                                                        background:white;border:1px solid #e2e8f0;border-radius:12px;
                                                        box-shadow:0 8px 24px rgba(0,0,0,0.12);z-index:500;
                                                        min-width:170px;padding:8px;margin-top:2px;">
                                                <div style="font-size:11px;font-weight:600;color:#94a3b8;padding:4px 8px 8px;text-transform:uppercase;letter-spacing:0.05em;">Filter Usia</div>
                                                <?php foreach (['' => 'Semua Usia','17-25'=>'17 - 25 tahun','26-35'=>'26 - 35 tahun','36-45'=>'36 - 45 tahun','46+'=>'46+ tahun'] as $val => $label): ?>
                                                <div onclick="terapkanFilter('usia','<?= $val ?>')"
                                                    style="padding:9px 12px;border-radius:8px;font-size:13px;cursor:pointer;
                                                            display:flex;align-items:center;justify-content:space-between;
                                                            font-weight:<?= $filter_usia===$val?'600':'400' ?>;
                                                            color:<?= $filter_usia===$val?'#0000FF':'#1e293b' ?>;
                                                            background:<?= $filter_usia===$val?'#eff6ff':'transparent' ?>;"
                                                    onmouseover="this.style.background='#f8fafc'"
                                                    onmouseout="this.style.background='<?= $filter_usia===$val?'#eff6ff':'transparent' ?>'">
                                                    <span><?= $label ?></span>
                                                    <?php if ($filter_usia===$val): ?>
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0000FF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">TGL BERGABUNG</th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">JABATAN</th>
                                <th style="width:160px;padding:12px 16px;text-align:center;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0):
                            $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                // Menghasilkan warna dan inisial avatar
                                $warna   = getAvatarColor($row['nama']);
                                $inisial = getInisial($row['nama']);
                        ?>
                        <tr>
                            <!-- Nomor urut -->
                            <td style="color:#64748b; font-size:13px;">
                                <?= $no++ ?>
                            </td>

                            <!-- Nama + avatar inisial -->
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="avatar-initials"
                                         style="background:<?= $warna ?>;">
                                        <?= $inisial ?>
                                    </div>
                                    <span class="employee-name">
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Jenis kelamin dengan badge warna -->
                            <td>
                                <?php if ($row['jenis_kelamin'] === 'Laki-laki'): ?>
                                    <span class="badge-gender badge-laki">Laki-laki</span>
                                <?php else: ?>
                                    <span class="badge-gender badge-perempuan">Perempuan</span>
                                <?php endif; ?>
                            </td>

                            <!-- Pendidikan -->
                            <td style="font-size:14px; color:#475569;">
                                <?= htmlspecialchars($row['pendidikan_terakhir']) ?>
                            </td>

                            <!-- Usia -->
                            <td style="font-size:14px; color:#475569;">
                                <?= htmlspecialchars($row['usia']) ?> thn
                            </td>

                            <!-- Tanggal Bergabung -->
                            <td style="font-size:14px; color:#475569;">
                                <?= $row['tanggal_bergabung']
                                    ? date('d M Y', strtotime($row['tanggal_bergabung']))
                                    : '-' ?>
                            </td>

                            <!-- Jabatan -->
                            <td style="font-size:14px; color:#475569;">
                                <?= htmlspecialchars($row['jabatan']) ?>
                            </td>

                            <!-- Tombol aksi -->
                                <td style="text-align:center;vertical-align:middle;padding:14px 16px;">
                                    <div style="display:flex;gap:6px;justify-content:center;align-items:center;">
                                    <!-- Tombol Edit -->
                                    <a href="pages/edit.php?id=<?= $row['id'] ?>" 
                                       class="btn-edit">
                                         <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4-3z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Tombol Hapus (trigger modal) -->
                                    <button type="button" class="btn-hapus" onclick="bukaModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:48px;
                                                   color:#94a3b8;">
                                <?php if ($cari): ?>
                                    Tidak ada pegawai dengan nama "<strong><?= htmlspecialchars($cari) ?></strong>".
                                <?php else: ?>
                                    Belum ada data pegawai.
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php
                // Membangun string parameter URL untuk pagination dan filter
                $url_params = '';
                if ($cari !== '')       $url_params .= '&cari='  . urlencode($cari);
                if ($filter_jk !== '')  $url_params .= '&jk='    . urlencode($filter_jk);
                if ($filter_pend !== '') $url_params .= '&pend=' . urlencode($filter_pend);
                if ($filter_usia !== '') $url_params .= '&usia=' . urlencode($filter_usia);
                if ($filter_jab !== '')  $url_params .= '&jab='  . urlencode($filter_jab);
                ?>

                <!-- Footer tabel: info + pagination -->
                <?php if ($total_data > 0): ?>
                <div style="display:flex; justify-content:space-between; align-items:center;
                            padding:14px 20px; border-top:1px solid #e2e8f0;">

                    <!-- Info jumlah data -->
                    <span style="font-size:13px; color:#64748b;">
                        Menampilkan <?= $offset + 1 ?>–<?= min($offset + $per_halaman, $total_data) ?>
                        dari <?= $total_data ?> data
                    </span>

                    <!-- Tombol Pagination -->
                    <div class="pagination">
                        <!-- Tombol Previous -->
                        <?php if ($halaman_ini > 1): ?>
                        <a href="?hal=<?= $halaman_ini - 1 ?><?= $url_params ?>"
                           class="page-btn">‹</a>
                        <?php endif; ?>

                        <!-- Nomor halaman -->
                        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="?hal=<?= $i ?><?= $url_params ?>"
                           class="page-btn <?= $i === $halaman_ini ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>

                        <!-- Tombol Next -->
                        <?php if ($halaman_ini < $total_halaman): ?>
                        <a href="?hal=<?= $halaman_ini + 1 ?><?= $url_params ?>"
                           class="page-btn">›</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tahap 1: Konfirmasi Awal -->
<div class="modal-overlay" id="modalHapus1">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="modal-title">Hapus Data Pegawai?</div>
        <div class="modal-desc">
            Anda akan menghapus data pegawai<br>
            <span class="modal-name" id="modalNama1"></span>
        </div>
        <div class="modal-warning">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Tindakan ini tidak dapat dibatalkan secara permanen.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-batal" onclick="tutupModal()">Batal</button>
            <button class="btn-modal-hapus" onclick="lanjutKonfirmasi()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<!-- Modal Tahap 2: Validasi Konfirmasi Akhir -->
<div class="modal-overlay" id="modalHapus2">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;
                    background:linear-gradient(135deg,#920000,#D50000);
                    display:flex;align-items:center;justify-content:center;
                    margin:0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                 stroke="white" stroke-width="2.5">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
        </div>
        <div class="modal-title" style="color:#920000;">Konfirmasi Akhir</div>
        <div class="modal-desc" style="margin-bottom:6px;">
            Ketik nama pegawai
            <strong id="modalNama2Target"></strong>
            untuk mengonfirmasi penghapusan.
        </div>
        <input type="text"
               id="inputKonfirmasi"
               placeholder="Ketik nama pegawai di sini"
               style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;
                      border-radius:50px;font-size:14px;outline:none;
                      text-align:center;margin:12px 0;transition:border 0.15s;">
        <div id="pesanValidasi"
             style="font-size:12px;color:#dc2626;margin-bottom:12px;display:none;">
            Nama tidak sesuai. Ketik nama pegawai dengan tepat.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-batal" onclick="kembaliModal1()">Kembali</button>
            <a href="#"
               id="linkHapusFinal"
               class="btn-modal-hapus"
               style="text-decoration:none;opacity:0.4;pointer-events:none;"
               onclick="return prosesHapus(event)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
                Hapus Permanen
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
/**
 * Script: Live Search dengan Auto-Focus
 * Fungsi: Pencarian real-time sambil mempertahankan fokus dan posisi cursor
 */
(function() {
    const input = document.getElementById('inputCari');
    if (!input) return;

    // Simpan posisi cursor setelah halaman reload
    const savedPos = sessionStorage.getItem('searchPos');
    if (savedPos !== null) {
        input.focus();
        input.setSelectionRange(parseInt(savedPos), parseInt(savedPos));
        sessionStorage.removeItem('searchPos');
    }

    let timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        const pos = input.selectionStart;
        timer = setTimeout(function() {
            // Simpan posisi cursor sebelum redirect
            sessionStorage.setItem('searchPos', pos);
            const keyword = input.value.trim();
            window.location.href = 'index.php?cari=' + encodeURIComponent(keyword);
        }, 500);
    });
})();
</script>

<script>
/**
 * Script: Modal Konfirmasi Hapus Dua Tahap
 * Tahap 1: Konfirmasi awal
 * Tahap 2: Validasi dengan mengetik nama pegawai
 */

let idHapusAktif  = null;
let namaHapusAktif = '';

// Membuka modal tahap 1
function bukaModal(id, nama) {
    idHapusAktif   = id;
    namaHapusAktif = nama;
    document.getElementById('modalNama1').textContent = nama;
    document.getElementById('modalHapus1').classList.add('show');
}

// Menutup semua modal dan reset
function tutupModal() {
    document.getElementById('modalHapus1').classList.remove('show');
    document.getElementById('modalHapus2').classList.remove('show');
    resetModal2();
    idHapusAktif   = null;
    namaHapusAktif = '';
}

// Reset state modal tahap 2
function resetModal2() {
    document.getElementById('inputKonfirmasi').value = '';
    document.getElementById('inputKonfirmasi').style.borderColor = '#e2e8f0';
    document.getElementById('pesanValidasi').style.display = 'none';
    const link = document.getElementById('linkHapusFinal');
    link.style.opacity       = '0.4';
    link.style.pointerEvents = 'none';
}

// Lanjut ke modal tahap 2
function lanjutKonfirmasi() {
    document.getElementById('modalNama2Target').textContent = namaHapusAktif;
    document.getElementById('linkHapusFinal').href = 'pages/hapus.php?id=' + idHapusAktif;
    document.getElementById('modalHapus1').classList.remove('show');
    document.getElementById('modalHapus2').classList.add('show');

    // Fokus ke input setelah modal terbuka
    setTimeout(function() {
        document.getElementById('inputKonfirmasi').focus();
    }, 100);
}

// Kembali ke modal tahap 1
function kembaliModal1() {
    document.getElementById('modalHapus2').classList.remove('show');
    resetModal2();
    document.getElementById('modalHapus1').classList.add('show');
}

// Proses hapus setelah klik tombol Hapus Permanen
function prosesHapus(e) {
    const input = document.getElementById('inputKonfirmasi').value.trim();
    if (input !== namaHapusAktif) {
        e.preventDefault();
        document.getElementById('pesanValidasi').style.display = 'block';
        document.getElementById('inputKonfirmasi').style.borderColor = '#dc2626';
        return false;
    }
    // Nama cocok, biarkan link href berjalan
    return true;
}

// Live check: aktifkan tombol jika nama sudah sesuai
document.getElementById('inputKonfirmasi').addEventListener('input', function() {
    const input = this.value.trim();
    const link  = document.getElementById('linkHapusFinal');
    const pesan = document.getElementById('pesanValidasi');

    if (input === namaHapusAktif) {
        // Nama cocok: aktifkan tombol
        link.style.opacity       = '1';
        link.style.pointerEvents = 'auto';
        this.style.borderColor   = '#00AB21';
        pesan.style.display      = 'none';
    } else {
        // Belum cocok: nonaktifkan tombol
        link.style.opacity       = '0.4';
        link.style.pointerEvents = 'none';
        this.style.borderColor   = '#e2e8f0';
    }
});

// Tutup modal jika klik overlay
document.getElementById('modalHapus1').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});
document.getElementById('modalHapus2').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});
</script>

<script>
/**
 * Script: Filter Kolom Tabel
 * Fungsi: Dropdown filter modern per kolom,
 *         langsung redirect tanpa tombol tambahan
 */

// Buka/tutup dropdown spesifik, tutup yang lain
function toggleDropdown(id) {
    const semua = ['ddJK', 'ddPend', 'ddUsia'];
    semua.forEach(function(did) {
        const el = document.getElementById(did);
        if (!el) return;
        if (did === id) {
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        } else {
            el.style.display = 'none';
        }
    });
}

// Terapkan filter dan redirect langsung
function terapkanFilter(key, value) {
    const params = new URLSearchParams(window.location.search);
    if (value === '') {
        params.delete(key);
    } else {
        params.set(key, value);
    }
    params.delete('hal');
    window.location.href = 'index.php?' + params.toString();
}

// Tutup semua dropdown jika klik di luar area dropdown
document.addEventListener('click', function(e) {
    const semua = ['ddJK', 'ddPend', 'ddUsia'];
    // Cek apakah klik berasal dari dalam dropdown atau tombol filter
    const dalamDropdown = semua.some(function(id) {
        const el = document.getElementById(id);
        return el && el.contains(e.target);
    });
    const tombolFilter = e.target.closest('button[onclick^="toggleDropdown"]');
    if (!dalamDropdown && !tombolFilter) {
        semua.forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }
});
</script>
</body>
</html>