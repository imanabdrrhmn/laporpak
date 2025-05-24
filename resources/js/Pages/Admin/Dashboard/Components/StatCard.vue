<template>
  <div class="col-md-3 col-6 mb-3">
    <div class="card h-100 border-0 shadow-sm custom-card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div class="text-content">
          <div class="text-muted mb-1">{{ title }}</div>
          <h3 class="mb-0">{{ count }}</h3>
          <div class="text-success small">
            <i class="bi bi-arrow-up-short"></i>
            {{ percentage }}% dari bulan {{ isCurrency ? 'ini' : 'lalu' }}
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
    title: {
      type: String,
      required: true,
    },
    count: {
      type: [String, Number],
      required: true,
    },
    percentage: {
      type: Number,
      required: true,
    },
    icon: {
      type: String,
      required: true,
    },
    bgClass: {
      type: String,
      required: true,
    },
    isCurrency: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped>
.icon-box {
  width: 70px;
  height: 70px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.icon-box i {
  font-size: 32px;
}

.custom-card {
  padding: 0.5rem;
}

.card-body {
  padding: 1.25rem;
}

.text-content {
  flex: 1;
  padding-right: 1.5rem;
}
</style>