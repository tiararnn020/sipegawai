<?php
/**
 * File    : config/db.php
 * Fungsi  : Konfigurasi dan inisialisasi koneksi database MySQL
 *           menggunakan MySQLi prosedural
 * Library : MySQLi (bawaan PHP)
 * Author  : [NAMAMU]
 * Tanggal : [TANGGAL]
 * Versi   : 1.0.0
 */

// Variabel konfigurasi database
$host = 'localhost';    // Alamat server database
$user = 'root';         // Username default XAMPP
$pass = '';             // Password default XAMPP (kosong)
$db   = 'db_pegawai';  // Nama database yang sudah dibuat di phpMyAdmin

// Membuat koneksi ke database menggunakan fungsi prosedural
$conn = mysqli_connect($host, $user, $pass, $db);

// Pengecekan apakah koneksi berhasil
if (!$conn) {
    // Jika gagal, hentikan program dan tampilkan pesan error
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>