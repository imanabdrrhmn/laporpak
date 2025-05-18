<template>
  <div class="card main-card">
    <div class="card-header">
      <h5 class="fw-bold mb-0">Aksi Cepat</h5>
    </div>
    <div class="card-body">
      <div class="quick-actions-grid">
        <a
          v-for="(aksi, index) in aksiCepat"
          :key="index"
          :href="aksi.url"
          :class="`btn btn-${aksi.variant} btn-action`"
        >
          <span class="action-icon">{{ aksi.icon }}</span>
          <span class="action-label">{{ aksi.label }}</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  aksiCepat: Array
});
</script>

<style scoped>
.main-card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  height: 100%;
  transition: var(--transition);
  background-color: var(--card-bg);
}

.main-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.main-card .card-header {
  background: var(--card-bg);
  border-bottom: 1px solid rgba(0,0,0,0.05);
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
}

.main-card .card-header h5 {
  color: var(--text-color);
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.btn-action {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 1rem 0.5rem;
  border-radius: 8px;
  text-align: center;
  white-space: normal;
  transition: var(--transition);
  text-decoration: none;
}

.btn-action:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.action-icon {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  display: block;
}

.action-label {
  font-size: 0.8rem;
}

@media (max-width: 992px) {
  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>