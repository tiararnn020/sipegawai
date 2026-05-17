<?php
/**
 * File: config/PegawaiClass.php
 * Fungsi: Class Pegawai yang mengimplementasikan konsep
 *         Pemrograman Berorientasi Objek (OOP)
 * Konsep OOP yang digunakan:
 *   - Encapsulation: data dan method terkumpul dalam satu class
 *   - Constructor: inisialisasi koneksi database
 *   - Method: fungsi-fungsi yang bekerja pada data pegawai
 * Author: [Tiara]
 * Tanggal: [17-05-2026]
 */

class Pegawai {

    /**
     * Property: koneksi database
     * Akses: private (hanya bisa diakses dari dalam class)
     */
    private $conn;

    /**
     * Constructor: dipanggil otomatis saat objek dibuat
     * Fungsi: Menginisialisasi koneksi database
     * Parameter: $conn - koneksi MySQLi yang sudah dibuat
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Method: ambilSemua()
     * Fungsi: Mengambil semua data pegawai dari database
     * Parameter: $limit (int) - jumlah data per halaman
     *            $offset (int) - posisi awal data
     * Return: mysqli_result - hasil query
     * Algoritma: Eksekusi SELECT dengan LIMIT dan OFFSET untuk pagination
     */
    public function ambilSemua($limit = 50, $offset = 0) {
        $query = "SELECT * FROM pegawai ORDER BY id ASC
                  LIMIT $limit OFFSET $offset";
        return mysqli_query($this->conn, $query);
    }

    /**
     * Method: ambilBerdasarkanId()
     * Fungsi: Mengambil satu data pegawai berdasarkan ID
     * Parameter: $id (int) - ID pegawai yang dicari
     * Return: array - data pegawai, atau null jika tidak ditemukan
     * Algoritma: SELECT dengan kondisi WHERE id = $id
     */
    public function ambilBerdasarkanId($id) {
        $id     = (int) $id;
        $query  = "SELECT * FROM pegawai WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Method: hitungTotal()
     * Fungsi: Menghitung total jumlah pegawai di database
     * Parameter: $kondisi (string) - kondisi WHERE opsional
     * Return: int - jumlah total pegawai
     */
    public function hitungTotal($kondisi = '') {
        $where = $kondisi ? "WHERE $kondisi" : '';
        $query = "SELECT COUNT(*) as total FROM pegawai $where";
        $result = mysqli_query($this->conn, $query);
        return (int) mysqli_fetch_assoc($result)['total'];
    }

    /**
     * Method: tambah()
     * Fungsi: Menyimpan data pegawai baru ke database
     * Parameter: $data (array) - array asosiatif berisi data pegawai
     * Return: bool - true jika berhasil, false jika gagal
     * Algoritma: Sanitasi input lalu eksekusi INSERT INTO
     */
    public function tambah($data) {
        // Sanitasi input untuk mencegah SQL injection
        $nama                = mysqli_real_escape_string($this->conn, $data['nama']);
        $jenis_kelamin       = mysqli_real_escape_string($this->conn, $data['jenis_kelamin']);
        $pendidikan_terakhir = mysqli_real_escape_string($this->conn, $data['pendidikan_terakhir']);
        $usia                = (int) $data['usia'];
        $tanggal_bergabung   = mysqli_real_escape_string($this->conn, $data['tanggal_bergabung']);
        $jabatan             = mysqli_real_escape_string($this->conn, $data['jabatan']);

        $query = "INSERT INTO pegawai
                    (nama, jenis_kelamin, pendidikan_terakhir,
                     usia, tanggal_bergabung, jabatan)
                  VALUES
                    ('$nama', '$jenis_kelamin', '$pendidikan_terakhir',
                     '$usia', '$tanggal_bergabung', '$jabatan')";

        return mysqli_query($this->conn, $query);
    }

    /**
     * Method: ubah()
     * Fungsi: Memperbarui data pegawai yang sudah ada
     * Parameter: $id (int) - ID pegawai yang diubah
     *            $data (array) - data baru yang akan disimpan
     * Return: bool - true jika berhasil, false jika gagal
     * Algoritma: Sanitasi input lalu eksekusi UPDATE SET WHERE id
     */
    public function ubah($id, $data) {
        $id                  = (int) $id;
        $nama                = mysqli_real_escape_string($this->conn, $data['nama']);
        $jenis_kelamin       = mysqli_real_escape_string($this->conn, $data['jenis_kelamin']);
        $pendidikan_terakhir = mysqli_real_escape_string($this->conn, $data['pendidikan_terakhir']);
        $usia                = (int) $data['usia'];
        $tanggal_bergabung   = mysqli_real_escape_string($this->conn, $data['tanggal_bergabung']);
        $jabatan             = mysqli_real_escape_string($this->conn, $data['jabatan']);

        $query = "UPDATE pegawai SET
                    nama                = '$nama',
                    jenis_kelamin       = '$jenis_kelamin',
                    pendidikan_terakhir = '$pendidikan_terakhir',
                    usia                = '$usia',
                    tanggal_bergabung   = '$tanggal_bergabung',
                    jabatan             = '$jabatan'
                  WHERE id = $id";

        return mysqli_query($this->conn, $query);
    }

    /**
     * Method: hapus()
     * Fungsi: Menghapus data pegawai dari database berdasarkan ID
     * Parameter: $id (int) - ID pegawai yang akan dihapus
     * Return: bool - true jika berhasil, false jika gagal
     * Algoritma: Validasi ID lalu eksekusi DELETE WHERE id
     */
    public function hapus($id) {
        $id    = (int) $id;
        $query = "DELETE FROM pegawai WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }

    /**
     * Method: getInisial()
     * Fungsi: Mengambil 2 huruf inisial dari nama pegawai
     * Parameter: $nama (string) - nama lengkap pegawai
     * Return: string - 2 huruf inisial kapital
     * Algoritma: Pecah nama per spasi, ambil huruf pertama
     *            dari 2 kata pertama
     */
    public static function getInisial($nama) {
        $kata = explode(' ', trim($nama));
        if (count($kata) >= 2) {
            return strtoupper($kata[0][0] . $kata[1][0]);
        }
        return strtoupper(substr($kata[0], 0, 2));
    }

    /**
     * Method: getAvatarColor()
     * Fungsi: Menghasilkan warna avatar berdasarkan huruf pertama nama
     * Parameter: $nama (string) - nama pegawai
     * Return: string - kode warna hex
     * Algoritma: Gunakan nilai ASCII huruf pertama sebagai indeks array warna
     */
    public static function getAvatarColor($nama) {
        // Array struktur data warna untuk avatar
        $warna = ['#0000FF','#7c3aed','#db2777','#059669','#d97706','#dc2626'];
        $index = ord(strtoupper($nama[0])) % count($warna);
        return $warna[$index];
    }
}
