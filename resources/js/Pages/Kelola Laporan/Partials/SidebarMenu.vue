<template>
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-primary text-white py-3">
      <h5 class="mb-0 fw-bold text-center">Menu Laporan</h5>
    </div>
    <div class="card-body p-0">
      <div class="nav flex-column nav-pills">
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
          :class="{
            'active bg-primary bg-opacity-25 text-primary': selectedTab === 'penipuan',
          }"
          @click="$emit('update:selectedTab', 'penipuan')"
        >
          <i class="fas fa-file-alt me-3"></i>
          <span class="fw-medium">Penipuan</span>
          <i class="fas fa-chevron-right ms-auto"></i>
        </button>
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
          :class="{
            'active bg-primary bg-opacity-25 text-primary': selectedTab === 'infrastruktur',
          }"
          @click="$emit('update:selectedTab', 'infrastruktur')"
        >
          <i class="fas fa-building me-3"></i>
          <span class="fw-medium">Infrastruktur</span>
          <i class="fas fa-chevron-right ms-auto"></i>
        </button>
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
          :class="{
            'active bg-primary bg-opacity-25 text-primary': selectedTab === 'verifikasi',
          }"
          @click="$emit('update:selectedTab', 'verifikasi')"
        >
          <i class="fas fa-check-circle me-3"></i>
          <span class="fw-medium">Verifikasi</span>
          <i class="fas fa-chevron-right ms-auto"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  selectedTab: String,
});
</script>
