<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
page.layout = AppLayout;

</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="container py-5">
    <!-- Welcome Header -->
    <div class="mb-4">
      <h2 class="fw-bold">Selamat datang kembali, Iman 👋</h2>
      <p class="text-muted">Dashboard Anda hari ini</p>
    </div>

    <!-- Ringkasan Info -->
    <div class="row g-4 mb-5">
      <!-- Kredit -->
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h6 class="text-muted">Saldo Kredit</h6>
            <h3 class="fw-bold text-success">Rp 125.000</h3>
            <button class="btn btn-outline-primary mt-2">Top Up</button>
          </div>
        </div>
      </div>

      <!-- Verifikasi -->
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h6 class="text-muted">Status Verifikasi</h6>
            <div class="d-flex align-items-center gap-2 mt-2">
              <span class="badge bg-success">Terverifikasi</span>
              <span class="text-muted small">NPWP & Identitas</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Laporan -->
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h6 class="text-muted">Total Laporan</h6>
            <h3 class="fw-bold">5</h3>
            <p class="text-muted small mb-0">2 masih diproses</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Aktivitas & Aksi -->
    <div class="row g-4 mb-5">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h5 class="fw-bold mb-4">Aktivitas Terakhir</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0">
                ✅ Anda melakukan <strong>cek NPWP</strong> - 1 hari lalu
              </li>
              <li class="list-group-item px-0">
                ⚠️ Melaporkan kasus penipuan <strong>#PL1234</strong>
              </li>
              <li class="list-group-item px-0">
                🔍 Cek blacklist terhadap <strong>089xxxxxxx</strong>
              </li>
              <li class="list-group-item px-0">
                🧾 Top up kredit sebesar <strong>Rp 50.000</strong>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Aksi Cepat -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <h5 class="fw-bold mb-4">Aksi Cepat</h5>
            <div class="d-grid gap-2">
              <button class="btn btn-outline-primary">Cek NPWP</button>
              <button class="btn btn-outline-danger">Cek Blacklist</button>
              <button class="btn btn-outline-success">Laporkan Penipuan</button>
              <button class="btn btn-outline-warning">Top Up Kredit</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Testimoni -->
    <div class="mb-4">
      <h5 class="fw-bold mb-3">Feedback Anda</h5>
      <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex align-items-center mb-3">
          <img src="/Default-Profile.png" alt="avatar" class="rounded-circle me-3" width="50" height="50">
          <div>
            <strong>Iman Abdurrohman</strong><br>
            <small class="text-muted">Kategori: Cari Laporan</small>
          </div>
        </div>
        <div class="mb-2">
          <span class="text-warning fs-5">★★★★★</span>
        </div>
        <p class="mb-0">Sistem ini sangat membantu saya menemukan laporan penipuan yang valid. Terima kasih!</p>
      </div>
    </div>
  </div>
    </AppLayout>
</template>

<style scoped>
.card-body {
  padding: 1.5rem;
}

h6 {
  font-size: 0.9rem;
}
</style>