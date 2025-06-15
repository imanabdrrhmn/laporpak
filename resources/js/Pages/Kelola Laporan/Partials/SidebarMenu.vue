<template>
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-primary text-white py-3">
      <h5 class="mb-0 fw-bold text-center">Menu Laporan</h5>
    </div>
    <div class="card-body p-0">
      <div class="nav flex-column nav-pills">
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center text-dark sidebar-btn"
          :class="{
            'active bg-primary-soft text-primary': selectedTab === 'penipuan',
          }"
          @click="$emit('update:selectedTab', 'penipuan')"
        >
          <i class="fas fa-file-alt me-2 me-md-3"></i>
          <span class="fw-medium menu-text">Penipuan</span>
          <i class="fas fa-chevron-right ms-auto chevron-icon"></i>
        </button>
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center text-dark sidebar-btn"
          :class="{
            'active bg-primary-soft text-primary': selectedTab === 'infrastruktur',
          }"
          @click="$emit('update:selectedTab', 'infrastruktur')"
        >
          <i class="fas fa-building me-2 me-md-3"></i>
          <span class="fw-medium menu-text">Infrastruktur</span>
          <i class="fas fa-chevron-right ms-auto chevron-icon"></i>
        </button>
        <button
          class="nav-link border-0 rounded-0 py-3 d-flex align-items-center text-dark sidebar-btn"
          :class="{
            'active bg-primary-soft text-primary': selectedTab === 'verifikasi',
          }"
          @click="$emit('update:selectedTab', 'verifikasi')"
        >
          <i class="fas fa-check-circle me-2 me-md-3"></i>
          <span class="fw-medium menu-text">Verifikasi</span>
          <i class="fas fa-chevron-right ms-auto chevron-icon"></i>
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

<style scoped>
.bg-primary-soft {
  background-color: rgba(13, 110, 253, 0.1) !important;
  width: 100%;
}

.nav-link {
  width: 100%;
}

.nav-pills .nav-link.active {
  width: 100%;
  margin: 0;
}

@media (min-width: 780px) and (max-width: 1000px) {
  .sidebar-btn {
    padding: 0.75rem 0.5rem !important;
    width: 100%;
  }

  .menu-text {
    font-size: 0.9rem;
  }

  .chevron-icon {
    font-size: 0.8rem;
  }

  .card-header h5 {
    font-size: 1rem;
  }
}
</style>
