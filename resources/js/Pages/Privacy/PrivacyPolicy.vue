<template>
  <AppLayout>
    <Head title="Kebijakan Privasi" />

    <div class="policy-container">
      <div class="content-wrapper">
        <h1>Kebijakan Privasi untuk laporpak.com</h1>
        <p class="effective-date">Tanggal Efektif: 19 Juni 2025</p>

        <p>Terima kasih telah menggunakan layanan kami di laporpak.com (“kami”, “milik kami”). Kami berkomitmen untuk melindungi privasi informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat Anda menggunakan layanan kami, termasuk saat berinteraksi melalui API WhatsApp dan Google OAuth.</p>

        <h2>1. Informasi yang Kami Kumpulkan</h2>
        <p>Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami dan melalui layanan pihak ketiga yang Anda gunakan untuk berinteraksi dengan layanan kami.</p>

        <h3>Saat Login dengan Google (Google OAuth)</h3>
        <p>Saat Anda memilih untuk mendaftar atau login ke layanan kami menggunakan akun Google, kami akan meminta izin untuk mengakses informasi dasar dari profil Google Anda. Informasi ini mencakup:</p>
        <ul>
          <li>Nama Lengkap Anda</li>
          <li>Alamat Email</li>
          <li>URL Foto Profil</li>
        </ul>

        <h3>Saat Menggunakan Verifikasi via WhatsApp</h3>
        <p>Saat Anda berinteraksi dengan layanan kami untuk verifikasi nomor telepon melalui WhatsApp, kami akan memproses informasi berikut:</p>
        <ul>
          <li>Nomor Telepon WhatsApp Anda (sebagai pengirim)</li>
          <li>Nama Profil WhatsApp Anda</li>
          <li>Isi Pesan (khususnya token pemicu verifikasi)</li>
        </ul>

        <h2>2. Bagaimana Kami Menggunakan Informasi Anda</h2>
        <p>Informasi yang kami kumpulkan digunakan secara spesifik untuk tujuan berikut:</p>
        <ul>
          <li><strong>Data dari Google OAuth:</strong> Digunakan untuk membuat dan mengelola akun Anda di layanan kami, mempersonalisasi pengalaman Anda (misalnya, menyapa Anda dengan nama), dan untuk mengirimkan notifikasi penting terkait akun Anda.</li>
          <li><strong>Data dari WhatsApp:</strong> Digunakan <strong>HANYA</strong> untuk satu tujuan, yaitu mengirimkan kode verifikasi (OTP) sekali pakai ke nomor Anda sebagai balasan atas permintaan Anda. Kami tidak menggunakan nomor telepon Anda untuk tujuan marketing, promosi, atau membagikannya kepada pihak lain. Isi percakapan tidak kami simpan setelah proses verifikasi selesai.</li>
        </ul>

        <h2>3. Berbagi Informasi</h2>
        <p>Kami <strong>tidak menjual, menyewakan, atau membagikan</strong> informasi pribadi Anda kepada pihak ketiga untuk tujuan marketing mereka. Informasi Anda hanya digunakan secara internal untuk menjalankan fungsionalitas inti dari laporpak.com.</p>

        <h2>4. Keamanan Data</h2>
        <p>Kami menerapkan langkah-langkah keamanan yang wajar untuk melindungi informasi Anda dari akses, pengubahan, pengungkapan, atau penghancuran yang tidak sah. Ini termasuk penggunaan enkripsi (HTTPS) dan praktik keamanan standar industri lainnya.</p>

        <h2>5. Penyimpanan dan Penghapusan Data</h2>
        <p>Kami menyimpan informasi akun Anda selama Anda masih memiliki akun aktif di layanan kami. Data interaksi WhatsApp seperti token pemicu verifikasi disimpan sementara dan akan dihapus secara berkala setelah kedaluwarsa.</p>
        <p>Anda berhak untuk meminta penghapusan akun dan semua data pribadi terkait. Untuk melakukannya, silakan kirim email ke <a :href="'mailto:' + contactEmail">{{ contactEmail }}</a> dengan subjek "Permintaan Penghapusan Data".</p>

        <h2>6. Hak-Hak Anda</h2>
        <p>Anda memiliki hak untuk mengakses, memperbaiki, atau menghapus informasi pribadi Anda yang kami simpan. Silakan hubungi kami melalui informasi kontak di bawah untuk mengajukan permintaan tersebut.</p>

        <h2>7. Perubahan pada Kebijakan Privasi Ini</h2>
        <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Versi terbaru akan selalu tersedia di halaman ini, dan kami akan memberitahukan Anda jika ada perubahan yang signifikan.</p>

        <h2>8. Hubungi Kami</h2>
        <p>Jika Anda memiliki pertanyaan atau kekhawatiran mengenai Kebijakan Privasi ini, silakan hubungi kami di:</p>
        <p><strong>Email:</strong> <a :href="'mailto:' + contactEmail">{{ contactEmail }}</a></p>
        <p><strong>Website:</strong> <a :href="appUrl" target="_blank" rel="noopener">{{ appUrl }}</a></p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

// Ganti nilai di bawah ini dengan informasi Anda
const contactEmail = 'info@laporpak.com'; 
const appUrl =  'https://laporpak.com';

</script>

<style scoped>
.policy-container {
  padding: 2rem 1rem;
  background-color: #f9fafb;
  color: #374151;
  font-family: sans-serif;
}
.content-wrapper {
  max-width: 800px;
  margin: 0 auto;
  background-color: #ffffff;
  padding: 2rem 2.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  line-height: 1.7;
}
h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
}
h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin-top: 2rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
  padding-bottom: 0.5rem;
}
h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}
p {
  margin-bottom: 1rem;
}
ul {
  list-style-type: disc;
  padding-left: 2rem;
  margin-bottom: 1rem;
}
li {
  margin-bottom: 0.5rem;
}
a {
  color: #3b82f6;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
.effective-date {
  font-style: italic;
  color: #6b7280;
  margin-bottom: 2rem;
}

.effective-date {
  font-style: italic;
  color: #6b7280;
  margin-bottom: 2rem;
}
</style>