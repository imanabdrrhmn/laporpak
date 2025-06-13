<template>
  <div class="stat-card">
    <div class="card h-100 border-0 shadow-sm custom-card">
      <div class="card-body">
        <div class="content-wrapper">
          <div class="text-content">
            <div class="text-muted mb-1 small-text">{{ title }}</div>
            <h3 class="mb-0 count-text">{{ count }}</h3>
            <div v-if="totalOverall !== undefined && totalOverall !== null" class="total-overall-text text-muted">
              Total Keseluruhan: {{ totalOverall }}
            </div>
            <div :class="['small', 'd-flex', 'align-items-center', 'gap-1', 'mt-2', percentage >= 0 ? 'text-success' : 'text-danger']">
              <i :class="['bi', percentage >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short']"></i>
              <span class="percentage-text">{{ Math.abs(percentage) }}%</span>
            </div>
          </div>
          <div class="icon-wrapper">
            <div :class="['icon-box', bgClassFromParent || bgClass]">
              <i :class="['bi', icon, 'text-white']"></i>
            </div>
          </div>
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
  height: 100%;
}

.custom-card {
  border-radius: 12px;
  background: #fff;
  width: 100%;
  height: 180px; /* Fixed height untuk konsistensi */
  display: flex;
  flex-direction: column;
}

.card-body {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.content-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  height: 100%;
  gap: 1rem;
}

.text-content {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  overflow: hidden;
}

.icon-wrapper {
  flex-shrink: 0;
  display: flex;
  align-items: flex-start;
}

.count-text {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0.5rem 0;
  color: #2c3e50;
  line-height: 1.2;
  word-break: break-all;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
}

.small-text {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #7f8c8d;
  line-height: 1.2;
  margin: 0;
}

.total-overall-text {
  font-size: 0.75rem;
  color: #95a5a6;
  margin: 0.25rem 0;
  line-height: 1.2;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.percentage-text {
  font-size: 0.85rem;
  font-weight: 500;
}

.text-success .bi-arrow-up-short,
.text-success .percentage-text {
  color: #28a745 !important;
}

.text-danger .bi-arrow-down-short,
.text-danger .percentage-text {
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

/* Background Classes */
.bg-primary { background-color: #4e73df; }
.bg-success { background-color: #1cc88a; }
.bg-info { background-color: #36b9cc; }
.bg-secondary { background-color: #858796; }

.bg-primary-soft { 
  background-color: rgba(59, 130, 246, 0.1); 
  color: #2563eb !important; 
}
.bg-primary-soft i { color: #2563eb !important; }

.bg-success-soft { 
  background-color: rgba(16, 185, 129, 0.1); 
  color: #059669 !important; 
}
.bg-success-soft i { color: #059669 !important; }

.bg-info-soft { 
  background-color: rgba(56, 189, 248, 0.1); 
  color: #0ea5e9 !important; 
}
.bg-info-soft i { color: #0ea5e9 !important; }

.bg-secondary-soft { 
  background-color: rgba(107, 114, 128, 0.1); 
  color: #4b5563 !important; 
}
.bg-secondary-soft i { color: #4b5563 !important; }

/* Responsive Design */
@media (max-width: 768px) {
  .custom-card {
    height: 160px;
  }
  
  .card-body {
    padding: 1.25rem;
  }
  
  .content-wrapper {
    gap: 0.75rem;
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

@media (max-width: 576px) {
  .custom-card {
    height: 140px;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .content-wrapper {
    gap: 0.5rem;
  }
  
  .count-text {
    font-size: 1.25rem;
  }
  
  .small-text {
    font-size: 0.75rem;
  }
  
  .total-overall-text {
    font-size: 0.7rem;
  }
  
  .icon-box {
    width: 40px;
    height: 40px;
  }
  
  .icon-box i {
    font-size: 20px;
  }
}

/* Utility untuk grid konsisten */
.row-cols-auto > * {
  flex: 0 0 auto;
  width: auto;
}

.row-cols-1 > * {
  flex: 0 0 auto;
  width: 100%;
}

.row-cols-2 > * {
  flex: 0 0 auto;
  width: 50%;
}

.row-cols-3 > * {
  flex: 0 0 auto;
  width: 33.333333%;
}

.row-cols-4 > * {
  flex: 0 0 auto;
  width: 25%;
}

@media (min-width: 576px) {
  .row-cols-sm-1 > * { width: 100%; }
  .row-cols-sm-2 > * { width: 50%; }
  .row-cols-sm-3 > * { width: 33.333333%; }
  .row-cols-sm-4 > * { width: 25%; }
}

@media (min-width: 768px) {
  .row-cols-md-1 > * { width: 100%; }
  .row-cols-md-2 > * { width: 50%; }
  .row-cols-md-3 > * { width: 33.333333%; }
  .row-cols-md-4 > * { width: 25%; }
}

@media (min-width: 992px) {
  .row-cols-lg-1 > * { width: 100%; }
  .row-cols-lg-2 > * { width: 50%; }
  .row-cols-lg-3 > * { width: 33.333333%; }
  .row-cols-lg-4 > * { width: 25%; }
}
</style>