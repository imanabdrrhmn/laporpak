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
        <div class="stat-item solved">
          <span class="stat-value">{{ reportStats.solved }}</span>
          <span class="stat-label">Solved</span>
        </div>
        <div class="stat-item unpublished">
          <span class="stat-value">{{ reportStats.unpublished }}</span>
          <span class="stat-label">Unpublished</span>
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
      published: 0,
      solved: 0,
      unpublished: 0,
    }),
  },
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
  gap: 16px; /* dikurangi sedikit supaya lebih rapih */
  flex-wrap: wrap;
  justify-content: center; /* supaya rapi di mobile */
}

.stat-item {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 12px 16px; 
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  min-width: 120px;
  width: 120px; 
  text-align: center;
  box-sizing: border-box;
}

.stat-value {
  font-size: 1.4rem; 
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.85rem; 
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
}

@media (max-width: 768px) {
  .stats-bar {
    gap: 10px;
    justify-content: center;
  }

  .stat-item {
    min-width: 90px;
    width: 90px;
    padding: 8px 10px;
  }

  .stat-value {
    font-size: 1rem;
  }

  .stat-label {
    font-size: 0.65rem;
  }
}

.stat-item.pending { background-color: rgba(255, 193, 7, 0.2); }
.stat-item.approved { background-color: rgba(40, 167, 69, 0.2); }
.stat-item.rejected { background-color: rgba(220, 53, 69, 0.2); }
.stat-item.published { background-color: rgba(13, 110, 253, 0.2); }
.stat-item.solved { background-color: rgba(23, 162, 184, 0.2); }
.stat-item.unpublished { background-color: rgba(108, 117, 125, 0.2); } 

</style>
