<template>
  <div class="card border-0 shadow-sm rounded-3 mt-4">
    <div class="card-body">
      <h6 class="text-muted mb-3 fw-bold text-uppercase">Statistik</h6>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-secondary">Total Laporan</span>
        <span class="badge bg-primary rounded-pill">{{ totalCount }}</span>
      </div>
      <div class="progress" style="height: 6px;">
        <div
          class="progress-bar bg-success"
          role="progressbar"
          :style="`width: ${selectedTab === 'penipuan' ? 75 : selectedTab === 'infrastruktur' ? 65 : 60}%`"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  selectedTab: String,
  totalCount: Number,
});
</script>
