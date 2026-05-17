/**
 * File: assets/js/script.js
 * Fungsi: Script global aplikasi SiPegawai
 * Fitur: Auto-hide toast notification setelah 3 detik
 * Author: [Tiara]
 * Tanggal: [17-05-2026]
 * Versi: 1.0.0
 */

// Menunggu halaman selesai dimuat sebelum menjalankan script
document.addEventListener('DOMContentLoaded', function () {

    // Auto-hide toast notification setelah 3 detik
    // Mengambil elemen toast dari DOM
    const toast = document.getElementById('toastMsg');
    
    // Jika toast ada di halaman ini, jalankan auto-hide
    if (toast) {
        setTimeout(function () {
            // Animasi fade out sebelum dihilangkan
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity    = '0';
            
            // Setelah animasi selesai, hapus elemen dari DOM
            setTimeout(function () {
                const container = document.getElementById('toastContainer');
                if (container) container.remove();
            }, 500);
        }, 3000); // Tunggu 3 detik sebelum mulai fade out
    }

});