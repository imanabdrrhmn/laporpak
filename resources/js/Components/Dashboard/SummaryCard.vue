<template>
  <div class="summary-card">
    <div class="card-content">
      <div class="card-icon" :style="{ background: iconBg, color: iconColor }">
        <i :class="'bi ' + icon"></i>
      </div>
      <div>
        <h6 class="card-title">{{ title }}</h6>
        <h3 class="card-value">{{ value }}</h3>
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  icon: String,
  value: [String, Number],
  iconBg: String,
  iconColor: String
});
</script>

<style scoped>
:root {
  --card-bg: #ffffff;
  --border-radius: 12px;
  --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  --text-color: #2c3e50;
  --text-muted: #6c757d;
  --transition: all 0.3s ease;
}

.summary-card {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: clamp(1rem, 3vw, 1.5rem);
  box-shadow: var(--box-shadow);
  border: 1px solid rgba(0, 0, 0, 0.02);
  transition: var(--transition);
  width: 100%; /* Mengganti nilai tetap dengan 100% */
  max-width: 100%; /* Membatasi lebar maksimum */
}

.card-content {
  display: flex;
  align-items: flex-start;
}

.summary-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.card-icon {
  width: clamp(40px, 10vw, 50px);
  height: clamp(40px, 10vw, 50px);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: clamp(0.75rem, 2vw, 1rem);
  font-size: clamp(1.25rem, 3vw, 1.5rem);
  transition: var(--transition);
}

.summary-card:hover .card-icon {
  transform: scale(1.05);
}

.card-title {
  font-size: clamp(0.75rem, 2vw, 0.9rem);
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.card-value {
  font-size: clamp(1.5rem, 4vw, 1.75rem);
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: var(--text-color);
}

.verification-badges {
  margin-top: 0.75rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.verification-progress {
  margin-bottom: 0.75rem;
}

.progress-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.progress-container .progress {
  flex: 1;
  height: 6px;
  border-radius: 3px;
  background-color: rgba(0, 0, 0, 0.05);
}

.verification-progress span {
  font-weight: 600;
  color: #28a745;
}

.reports-stats {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: clamp(0.75rem, 2vw, 0.875rem);
}

/* Responsive Design */
@media (max-width: 768px) {
  .summary-card {
    padding: 1rem;
  }

  .card-content {
    flex-direction: column;
    text-align: center;
  }

  .card-icon {
    margin-right: 0;
    margin-bottom: 1rem;
    margin-left: auto;
    margin-right: auto;
  }

  .card-value {
    font-size: 1.5rem;
  }
}

@media (max-width: 576px) {
  .summary-card {
    padding: 0.75rem;
  }

  .card-icon {
    width: 40px;
    height: 40px;
    font-size: 1.25rem;
  }

  .card-title {
    font-size: 0.75rem;
  }

  .card-value {
    font-size: 1.25rem;
  }
}
</style>