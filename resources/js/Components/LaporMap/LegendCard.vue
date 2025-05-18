<template>
  <div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Legenda Peta</h5>
    </div>
    <div class="card-body">
      <div v-for="(item, index) in legendItems" :key="index" class="legend-item mb-3">
        <div class="d-flex align-items-center">
          <div class="legend-marker" :style="{ backgroundColor: item.color }">
            <i class="bi bi-geo-alt-fill text-white"></i>
          </div>
          <div class="ms-3">
            <p class="mb-0 fw-bold">{{ item.label }}</p>
            <small class="text-muted">{{ item.description }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  legendItems: Array
});
</script>

<style scoped>
.legend-marker {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 14px;
}

@media (max-width: 768px) {
  .legend-marker {
    width: 20px;
    height: 20px;
    font-size: 12px;
  }
}

@media (max-width: 320px) {
  .legend-marker {
    width: 18px;
    height: 18px;
    font-size: 10px;
  }
}
</style>