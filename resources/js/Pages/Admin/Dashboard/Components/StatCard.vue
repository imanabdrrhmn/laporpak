<template>
  <div class="stat-card">
    <div class="card h-100 border-0 shadow-sm custom-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="text-content">
          <div class="text-muted mb-1 small-text">{{ title }}</div>
          <h3 class="mb-0 count-text">{{ count }}</h3>
          <div :class="['small', 'd-flex', 'align-items-center', 'gap-1', isCurrency ? 'text-primary' : 'text-success']">
            <i class="bi bi-arrow-up-short"></i>
            <span class="percentage-text">{{ percentage }}% dari bulan {{ isCurrency ? 'ini' : 'lalu' }}</span>
          </div>
        </div>
        <div :class="['icon-box', bgClass]">
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
    percentage: { type: Number, required: true },
    icon: { type: String, required: true },
    bgClass: { type: String, required: true },
    isCurrency: { type: Boolean, default: false },
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
  padding: 0.5rem;
  width: 100%;
  min-height: 130px;
}

.card-body {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  flex-wrap: nowrap;
}

.text-content {
  flex: 1 1 auto;
  padding-right: 1rem;
  min-width: 0;
}

.count-text {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.2rem;
  white-space: nowrap;
}

.small-text, .percentage-text {
  font-size: 0.875rem;
  word-wrap: break-word;
  white-space: normal;
}

.icon-box {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

@media (max-width: 576px) {


  .card-body {
    padding-right: 0 !important;
  }
}


.icon-box i {
  font-size: 30px;
}

.bg-primary {
  background-color: #4e73df;
}
.bg-success {
  background-color: #1cc88a;
}
.bg-info {
  background-color: #36b9cc;
}
.bg-secondary {
  background-color: #6c757d;
}
</style>
