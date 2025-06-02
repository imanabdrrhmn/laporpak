<template>
  <div class="stat-card">
    <div class="card h-100 border-0 shadow-sm custom-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="text-content">
          <div class="text-muted mb-1 small-text">{{ title }}</div>
          <h3 class="mb-0 count-text">{{ count }}</h3>
          <div v-if="totalOverall !== undefined && totalOverall !== null" class="total-overall-text text-muted">
            Total Keseluruhan: {{ totalOverall }}
          </div>
          <div :class="['small', 'd-flex', 'align-items-center', 'gap-1', percentage >= 0 ? 'text-success' : 'text-danger']">
            <i :class="['bi', percentage >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short']"></i>
            <span class="percentage-text">{{ Math.abs(percentage) }}%</span>
          </div>
        </div>
        <div :class="['icon-box', bgClassFromParent || bgClass]">
          <i :class="['bi', icon, 'text-white']"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: { type: String, required: true },
    count: { type: [String, Number], required: true },
    totalOverall: { type: [String, Number], default: null },
    percentage: { type: Number, required: true, default: 0 },
    periodLabel: { type: String, default: 'vs Periode Sebelumnya' },
    icon: { type: String, required: true },
    bgClass: { type: String, required: false },
    bgClassFromParent: { type: String, required: false },
  },
};
</script>

<style scoped>
.stat-card {
  width: 100%;
  min-width: 0;
}

.custom-card {
  border-radius: 12px;
  background: #fff;
  width: 100%;
  min-height: 140px;
  display: flex;
  flex-direction: column;
}

.card-body {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem;
  flex-grow: 1;
  flex-wrap: nowrap;
}

.text-content {
  flex: 1 1 auto;
  padding-right: 1rem;
  min-width: 0;
}

.count-text {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.1rem;
  white-space: nowrap;
  color: #2c3e50;
}

.small-text {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #7f8c8d;
  margin-bottom: 0.3rem;
}

.total-overall-text {
  font-size: 0.75rem;
  color: #95a5a6;
  margin-bottom: 0.3rem;
}

.percentage-text {
  font-size: 0.85rem;
  font-weight: 500;
}

.text-success .bi-arrow-up-short, .text-success .percentage-text {
  color: #28a745 !important;
}

.text-danger .bi-arrow-down-short, .text-danger .percentage-text {
  color: #dc3545 !important;
}

.icon-box {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

.icon-box i {
  font-size: 28px;
}

.bg-primary { background-color: #4e73df; }
.bg-success { background-color: #1cc88a; }
.bg-info { background-color: #36b9cc; }
.bg-secondary { background-color: #858796; }

.bg-primary-soft { background-color: rgba(59, 130, 246, 0.1); color: #2563eb !important; }
.bg-primary-soft i { color: #2563eb !important; }

.bg-success-soft { background-color: rgba(16, 185, 129, 0.1); color: #059669 !important; }
.bg-success-soft i { color: #059669 !important; }

.bg-info-soft { background-color: rgba(56, 189, 248, 0.1); color: #0ea5e9 !important; }
.bg-info-soft i { color: #0ea5e9 !important; }

.bg-secondary-soft { background-color: rgba(107, 114, 128, 0.1); color: #4b5563 !important; }
.bg-secondary-soft i { color: #4b5563 !important; }

@media (max-width: 576px) {
  .card-body {
    padding: 1rem 0.75rem;
    align-items: flex-start;
  }
  .text-content {
    padding-right: 0.5rem;
  }
  .count-text {
    font-size: 1.5rem;
  }
  .icon-box {
    width: 48px;
    height: 48px;
  }
  .icon-box i {
    font-size: 24px;
  }
}
</style>
