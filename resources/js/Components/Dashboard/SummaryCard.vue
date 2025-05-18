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
.summary-card {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--box-shadow);
  height: 100%;
  transition: var(--transition);
  border: 1px solid rgba(0,0,0,0.02);
  display: flex;
  flex-direction: column;
}

.card-content {
  display: flex;
  align-items: flex-start;
}

.summary-card:hover {
  transform: translateY(-7px);
  box-shadow: 0 15px 20px rgba(0,0,0,0.1);
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  font-size: 1.5rem;
  transition: var(--transition);
}

.summary-card:hover .card-icon {
  transform: scale(1.1);
}

.card-title {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.card-value {
  font-size: 1.75rem;
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
  background-color: rgba(0,0,0,0.05);
}

.verification-progress span {
  font-weight: 600;
  color: var(--success-color);
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
  font-size: 0.875rem;
}

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
</style>