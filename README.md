# 📢 Lapor Pak - Sistem Pelaporan & Verifikasi Digital

**Lapor Pak** adalah platform web untuk pelaporan penipuan dan verifikasi informasi berbasis query. Proyek ini memungkinkan pengguna melaporkan kasus penipuan melalui berbagai saluran (email, WhatsApp, SMS), serta melakukan verifikasi data melalui mekanisme top-up dan query berbayar.

---

## 📌 Fitur Utama

### 🔒 User Management
- Registrasi dan login via email, nomor telepon, atau akun Google.
- Sistem autentikasi aman dan mudah digunakan.

### 📝 Sistem Pelaporan
- Pengguna dapat melaporkan kasus penipuan dengan bukti pendukung.
- Laporan diverifikasi secara otomatis dan manual oleh tim internal.
- Hanya laporan yang lolos verifikasi yang dipublikasikan dan dapat dicari.

### 🔎 Sistem Verifikasi
- Pengguna dapat mencari informasi dari laporan yang telah terverifikasi.
- Fitur verifikasi berbasis query (per pencarian) menggunakan saldo/top-up.
- Sistem akan mengembalikan hasil verifikasi kepada pengguna.

### 💰 Sistem Top-Up
- Pengguna dapat menambah saldo minimal Rp100.000.
- Saldo digunakan untuk membayar layanan verifikasi query.

---

## 👥 Jenis Pengguna

- **User Umum**: Melakukan pelaporan dan pencarian data.
- **Admin**: Mengelola sistem dan pengguna.
- **Tim Verifikasi**: Memverifikasi laporan dan memastikan keabsahan data.

---

## 📐 Arsitektur Singkat

- **Frontend**: Vue.js + Inertia.js + Bootstrap
- **Backend**: Laravel PHP
- **Database**: MySQL / PostgreSQL
- **Storage**: Public Storage Laravel (untuk lampiran laporan)
- **Authentication**: Laravel Breeze/Fortify + Sanctum
