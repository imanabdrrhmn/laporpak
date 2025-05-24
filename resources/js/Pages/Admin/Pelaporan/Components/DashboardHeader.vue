<!-- resources/js/Components/Pelaporan/DashboardHeader.vue -->
<template>
  <div class="dashboard-header">
    <div class="header-content">
      <h1 class="fw-bold">Manajemen Laporan</h1>
      <div class="stats-bar">
        <div class="stat-item">
          <span class="stat-value">{{ reportStats.total }}</span>
          <span class="stat-label">Total</span>
        </div>
        <div class="stat-item pending">
          <span class="stat-value">{{ reportStats.pending }}</span>
          <span class="stat-label">Pending</span>
        </div>
        <div class="stat-item approved">
          <span class="stat-value">{{ reportStats.approved }}</span>
          <span class="stat-label">Approved</span>
        </div>
        <div class="stat-item rejected">
          <span class="stat-value">{{ reportStats.rejected }}</span>
          <span class="stat-label">Rejected</span>
        </div>
        <div class="stat-item published">
          <span class="stat-value">{{ reportStats.published }}</span>
          <span class="stat-label">Published</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  reportStats: {
    type: Object,
    required: true,
    default: () => ({
      total: 0,
      pending: 0,
      approved: 0,
      rejected: 0,
      published: 0
    })
  }
});
</script>

<style scoped>
.dashboard-header {
  background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  color: white;
}

.header-content h1 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 16px;
}

.stats-bar {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 12px 20px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  min-width: 100px;
  text-align: center;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
}

.stat-item.pending { background-color: rgba(255, 193, 7, 0.2); }
.stat-item.approved { background-color: rgba(40, 167, 69, 0.2); }
.stat-item.rejected { background-color: rgba(220, 53, 69, 0.2); }
.stat-item.published { background-color: rgba(13, 110, 253, 0.2); }

@media (max-width: 768px) {
  .stats-bar {
    gap: 12px;
  }
  
  .stat-item {
    padding: 8px 12px;
    min-width: 80px;
  }
  
  .stat-value {
    font-size: 1.2rem;
  }
}
</style>