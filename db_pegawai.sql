-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Bulan Mei 2026 pada 07.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pegawai`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `pendidikan_terakhir` enum('SMA','D3','S1','S2','S3') NOT NULL,
  `usia` int(3) NOT NULL,
  `tanggal_bergabung` date DEFAULT NULL,
  `jabatan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pegawai`
--

INSERT INTO `pegawai` (`id`, `nama`, `jenis_kelamin`, `pendidikan_terakhir`, `usia`, `tanggal_bergabung`, `jabatan`, `created_at`) VALUES
(1, 'Budi Santoso', 'Laki-laki', 'S1', 27, '2026-01-15', 'Developer', '2026-05-11 20:25:28'),
(2, 'Siti Rahayu', 'Perempuan', 'S2', 34, '2026-01-22', 'Manager', '2026-05-11 20:25:28'),
(3, 'Andi Prasetyo', 'Laki-laki', 'D3', 25, '2026-02-03', 'Teknisi', '2026-05-11 20:25:28'),
(4, 'Dewi Kartika', 'Perempuan', 'S1', 30, '2026-02-14', 'Designer', '2026-05-11 20:25:28'),
(5, 'Rizky Firmansyah', 'Laki-laki', 'SMA', 22, '2026-02-28', 'Admin', '2026-05-11 20:25:28'),
(6, 'Lina Marlina', 'Perempuan', 'S1', 27, '2026-03-05', 'Analyst', '2026-05-11 20:25:28'),
(7, 'Hendra Gunawan', 'Laki-laki', 'S2', 40, '2026-03-12', 'Direktur', '2026-05-11 20:25:28'),
(8, 'Yuni Astuti', 'Perempuan', 'D3', 26, '2026-03-19', 'HRD', '2026-05-11 20:25:28'),
(9, 'Doni Setiawan', 'Laki-laki', 'S1', 31, '2026-03-25', 'Akuntan', '2026-05-11 20:25:28'),
(10, 'Rini Susanti', 'Perempuan', 'SMA', 23, '2026-04-01', 'Resepsionis', '2026-05-11 20:25:28'),
(12, 'ryusuke', 'Laki-laki', 'S1', 26, '2026-04-10', 'kepala sdm', '2026-05-13 11:49:34'),
(13, 'inoe', 'Perempuan', 'S1', 25, '2026-04-15', 'staff cs', '2026-05-13 11:51:09'),
(14, 'bieja', 'Perempuan', 'S2', 28, '2026-04-21', 'manager', '2026-05-13 15:36:55'),
(16, 'rui', 'Perempuan', 'S1', 24, '2026-05-08', 'staff sdm', '2026-05-13 22:50:27'),
(19, 'jihyun', 'Perempuan', 'D3', 26, NULL, 'banjang', '2026-05-15 06:17:33'),
(20, 'yuki fukushima', 'Perempuan', 'SMA', 36, NULL, 'playmaker', '2026-05-15 08:33:22'),
(22, 'kang sung hyun', 'Laki-laki', 'S1', 43, '2026-05-05', 'manager', '2026-05-15 08:34:57'),
(23, 'ko hee jin', 'Laki-laki', 'S1', 45, '2026-04-05', 'MB', '2026-05-15 08:36:28'),
(25, 'hayumi inoue', 'Perempuan', 'S1', 28, '2026-01-20', 'kpl sdm', '2026-05-15 15:37:50'),
(27, 'kim won ho', 'Laki-laki', 'SMA', 24, '2025-12-03', 'playmaker', '2026-05-16 04:24:11'),
(30, 'Yuki Ishikawa', 'Laki-laki', 'S2', 26, '2026-01-01', 'HR', '2026-05-16 05:10:47'),
(32, 'Yukiko Wada', 'Perempuan', 'S1', 25, '2026-01-01', 'Staff Analyst', '2026-05-17 03:46:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
