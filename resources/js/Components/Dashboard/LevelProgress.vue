<template>
  <div class="level-progress-container mb-5 animate__animated animate__fadeIn" style="animation-delay: 0.4s">
    <div class="level-info">
      <div>
        <h6>Program Loyalitas</h6>
        <p class="mb-0">{{ progresLevel }}% menuju level Gold</p>
      </div>
      <button class="btn btn-sm btn-outline-warning">Liat Hadiah</button>
    </div>
    <div class="progress" style="height: 8px;">
      <div class="progress-bar bg-warning" role="progressbar" :style="`width: ${progresLevel}%`" :aria-valuenow="progresLevel" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  progresLevel: Number
});
</script>

<style scoped>
.level-progress-container {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 1.25rem;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}

.level-progress-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.level-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.level-info h6 {
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 0.25rem;
}

.level-info p {
  color: var(--text-muted);
  font-size: 0.875rem;
}
</style>