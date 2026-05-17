<?php
/**
 * File    : pages/hapus.php
 * Fungsi  : Memproses penghapusan data pegawai berdasarkan ID
 *           Tidak memiliki tampilan UI - hanya memproses DELETE
 *           Menggunakan class Pegawai (OOP) untuk operasi database
 * OOP     : Instansiasi class Pegawai, memanggil method
 *           ambilBerdasarkanId() dan hapus()
 * Author  : [Tiara]
 * Tanggal : [17-05-2026]
 * Versi   : 1.0.0
 */

require_once '../config/db.php';

// Memuat class Pegawai untuk implementasi OOP
require_once '../config/PegawaiClass.php';

// Membuat objek dari class Pegawai (instansiasi)
$pegawai = new Pegawai($conn);

// Mengambil ID dari URL dan validasi
$id = (int) $_GET['id'];

// Jika ID tidak valid, kembali ke halaman utama
if ($id <= 0) {
    header("Location: ../index.php");
    exit();
}

// Mengecek apakah data ada menggunakan method dari class Pegawai
$data_cek = $pegawai->ambilBerdasarkanId($id);

if (!$data_cek) {
    header("Location: ../index.php");
    exit();
}

// Menggunakan method hapus() dari class Pegawai (OOP)
$hasil = $pegawai->hapus($id);

if ($hasil) {
    // Berhasil dihapus, kembali ke index dengan pesan sukses
    header("Location: ../index.php?pesan=hapus");
    exit();
} else {
    // Gagal, kembali ke index tanpa pesan
    header("Location: ../index.php");
    exit();
}
?>