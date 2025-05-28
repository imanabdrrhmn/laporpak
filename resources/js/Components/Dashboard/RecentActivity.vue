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
          <svg width="48" height="48" viewBox="0 0 64 64" fill="none">
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
  padding: clamp(0.75rem, 2vw, 1rem);
  width: 100%; /* Mengganti nilai tetap dengan 100% */
  max-width: 100%; /* Membatasi lebar maksimum */
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: clamp(0.75rem, 2vw, 1.25rem) clamp(1rem, 3vw, 1.5rem);
  background: #ffffff;
}

.card-title {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.lihat-semua {
  color: #4285f4;
  text-decoration: none;
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  font-weight: 500;
  transition: color 0.2s ease;
}

.lihat-semua:hover {
  color: #3367d6;
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
  padding: clamp(0.75rem, 2vw, 1rem) clamp(1rem, 3vw, 1.5rem);
  transition: background-color 0.2s ease;
}

.aktivitas-item:hover {
  background-color: #fafafa;
}

.activity-icon {
  margin-right: clamp(0.5rem, 2vw, 1rem);
  position: relative;
  z-index: 2;
}

.icon-circle {
  width: clamp(32px, 8vw, 40px);
  height: clamp(32px, 8vw, 40px);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: clamp(12px, 3vw, 16px);
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
  font-size: clamp(0.875rem, 2.5vw, 1rem);
  font-weight: 500;
  color: #333333;
  margin-bottom: 0.25rem;
}

.activity-time {
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  color: #999999;
}

.activity-border {
  position: absolute;
  left: clamp(32px, 8vw, 40px);
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
  padding: clamp(2rem, 5vw, 3rem) clamp(1rem, 3vw, 1.5rem);
}

.empty-icon svg {
  width: clamp(32px, 8vw, 48px);
  height: clamp(32px, 8vw, 48px);
  opacity: 0.6;
}

.empty-text {
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  color: #999999;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .aktivitas-card {
    padding: 0.75rem;
  }

  .card-header {
    padding: 0.75rem 1rem;
  }

  .aktivitas-item {
    padding: 0.75rem 1rem;
  }

  .icon-circle {
    width: 36px;
    height: 36px;
    font-size: 14px;
  }

  .activity-border {
    left: 36px;
  }
}

@media (max-width: 576px) {
  .aktivitas-item {
    padding: 0.5rem 0.75rem;
  }

  .card-header {
    padding: 0.5rem 0.75rem;
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