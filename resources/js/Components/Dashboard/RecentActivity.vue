<template>
  <div class="aktivitas-card">
    <div class="card-header">
      <h5 class="card-title">Aktivitas Terakhir</h5>
      <a href="#" class="lihat-semua">Lihat Semua →</a>
    </div>
    <div class="card-body">
      <div class="aktivitas-list">
        <div 
          v-for="(item, index) in aktivitasList" 
          :key="index"
          class="aktivitas-item"
        >
          <div class="activity-icon">
            <div 
              class="icon-circle"
              :class="item.iconClass"
            >
              <i :class="item.icon"></i>
            </div>
          </div>
          <div class="activity-content">
            <div class="activity-title">{{ item.title }}</div>
            <div class="activity-time">{{ item.time }}</div>
          </div>
          <div class="activity-border"></div>
        </div>
      </div>
      
      <div v-if="aktivitasList.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="30" stroke="#e0e0e0" stroke-width="2" stroke-dasharray="4 4"/>
            <path d="M32 20v24M20 32h24" stroke="#e0e0e0" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <p class="empty-text">Tidak ada aktivitas lainnya</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const aktivitasList = ref([
  {
    id: 1,
    title: 'Verifikasi data berhasil',
    time: '2 jam yang lalu',
    icon: '✓',
    iconClass: 'success'
  },
  {
    id: 2,
    title: 'Isi Saldo Rp 50.000',
    time: '2 jam yang lalu',
    icon: '💳',
    iconClass: 'primary'
  }
])

const props = defineProps({
  aktivitas: {
    type: Array,
    default: () => []
  }
})

if (props.aktivitas && props.aktivitas.length > 0) {
  aktivitasList.value = props.aktivitas
}
</script>

<style scoped>
.aktivitas-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #f0f0f0;
  overflow: hidden;
  margin-left: 24px;
  padding: 16px;
  width: 81.5rem;
  
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px 16px 24px;
  background: #ffffff;
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.lihat-semua {
  color: #4285f4;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.2s ease;
}

.lihat-semua:hover {
  color: #3367d6;
  text-decoration: none;
}

.card-body {
  padding: 0;
}

.aktivitas-list {
  position: relative;
}

.aktivitas-item {
  position: relative;
  display: flex;
  align-items: center;
  padding: 16px 24px;
  transition: background-color 0.2s ease;
}

.aktivitas-item:hover {
  background-color: #fafafa;
}

.activity-icon {
  margin-right: 16px;
  position: relative;
  z-index: 2;
}

.icon-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 600;
  color: white;
}

.icon-circle.success {
  background: linear-gradient(135deg, #4CAF50, #45a049);
}

.icon-circle.primary {
  background: linear-gradient(135deg, #2196F3, #1976D2);
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-size: 15px;
  font-weight: 500;
  color: #333333;
  margin-bottom: 4px;
  line-height: 1.4;
}

.activity-time {
  font-size: 13px;
  color: #999999;
  line-height: 1.3;
}

.activity-border {
  position: absolute;
  left: 44px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #f0f0f0;
  z-index: 1;
}

.aktivitas-item:last-child .activity-border {
  display: none;
}

.empty-state {
  text-align: center;
  padding: 60px 24px;
}

.empty-icon {
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-text {
  font-size: 14px;
  color: #999999;
  margin: 0;
  font-weight: 400;
}

/* Responsive */
@media (max-width: 768px) {
  .aktivitas-card {
    border-radius: 8px;
  }
  
  .card-header {
    padding: 16px 20px 12px 20px;
  }
  

  
  .lihat-semua {
    font-size: 13px;
  }
  
  .aktivitas-item {
    padding: 14px 20px;
  }
  
  .activity-icon {
    margin-right: 14px;
  }
  
  .icon-circle {
    width: 36px;
    height: 36px;
    font-size: 14px;
  }
  
  .activity-border {
    left: 38px;
  }
  
  .activity-title {
    font-size: 14px;
  }
  
  .activity-time {
    font-size: 12px;
  }
}

@media (max-width: 480px) {
  .aktivitas-item {
    padding: 12px 16px;
  }
  
  .card-header {
    padding: 14px 16px 10px 16px;
  }
  
  .activity-icon {
    margin-right: 12px;
  }
  
  .icon-circle {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
  
  .activity-border {
    left: 32px;
  }
}
</style>