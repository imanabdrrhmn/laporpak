<template>
  <div class="welcome-container">
    <!-- Header Section dengan Background Gradient -->
    <div class="welcome-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="welcome-content">
              <h1 class="welcome-title">Selamat datang kembali, {{ namaUser }}</h1>
              <p class="welcome-subtitle">Mau ngapain hari ini?</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="user-profile-section">
              <!-- User Avatar -->
              <div class="user-avatar">
                <img v-if="user.avatar_url" :src="user.avatar_url" alt="User Avatar" class="user-avatar">
                <i v-else class="bi bi-person"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Saldo Kredit Section -->
    <div class="saldo-section">
      <div class="container-fluid">
        <div class="saldo-card">
          <!-- Saldo Kredit View -->
          <div v-if="activeView === 'saldo'" class="saldo-view">
            <div class="saldo-header">
              <div class="saldo-title-section">
                <h5 class="saldo-title">Saldo Kredit</h5>
                <div class="saldo-icon">
                  <i class="bi bi-wallet"></i>
                </div>
              </div>
            </div>
            
            <div class="saldo-amount">
              <span class="currency">Rp</span>
              <span class="amount">{{ formatCurrency(saldoKredit) }}</span>
            </div>
          </div>

          <!-- Total Laporan View -->
          <div v-if="activeView === 'laporan'" class="laporan-view">
            <div class="saldo-header">
              <div class="saldo-title-section">
                <h3 class="saldo-title">Total Laporan</h3>
                <div class="saldo-icon">
                  <i class="bi bi-graph-up"></i>
                </div>
              </div>
            </div>
            
            <div class="laporan-stats">
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="stat-card total-laporan">
                    <div class="stat-icon">
                      <i class="bi bi-file-text"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-label">Total Laporan</span>
                      <span class="stat-value">{{ totalLaporan }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="stat-card sedang-diproses">
                    <div class="stat-icon">
                      <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-label">Sedang Diproses</span>
                      <span class="stat-value">{{ sedangDiproses }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="stat-card proses-selesai">
                    <div class="stat-icon">
                      <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-label">Proses Selesai</span>
                      <span class="stat-value">{{ prosesSelesai }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="saldo-actions">
            <button class="btn saldo-btn" :class="{ active: activeView === 'saldo' }" @click="showSaldoKredit">
              <i class="bi bi-wallet me-2"></i>
              Saldo Kredit
            </button>
            <button class="btn saldo-btn" :class="{ active: activeView === 'laporan' }" @click="showTotalPelaporan">
              <i class="bi bi-bar-chart me-2"></i>
              Total Pelaporan
            </button>
          </div>
        </div>
      </div>
    </div>
    

          
          <div class="col-md-6 col-lg-4" v-if="levelUser">
            <div class="info-card level-card">
              <div class="card-icon">
                <i class="bi bi-star"></i>
              </div>
              <div class="card-content">
                <span class="card-label">Level</span>
                <span class="card-value">{{ levelUser }}</span>
              </div>
            </div>
          </div>
          
          
          </div>
          
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)
const user = computed(() => page.props.auth?.user || {})
const isAdmin = computed(() => page.props.auth?.isAdmin)

defineProps({
  namaUser: {
    type: String,
    default: 'User'
  },
  levelUser: {
    type: String,
    default: null
  },
  userImage: {
    type: String,
    default: null
  },
  saldoKredit: {
    type: Number,
    default: 50000
  },
  totalLaporan: {
    type: Number,
    default: 10
  },
  sedangDiproses: {
    type: Number,
    default: 0
  },
  prosesSelesai: {
    type: Number,
    default: 0
  }
});

const activeView = ref('saldo')
const currentTime = ref(new Date())

const getCurrentDay = () => {
  return currentTime.value.toLocaleDateString('id-ID', { weekday: 'long' })
}

const getCurrentDate = () => {
  return currentTime.value.toLocaleDateString('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

const getCurrentTime = () => {
  return currentTime.value.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const showSaldoKredit = () => {
  activeView.value = 'saldo'
  console.log('Menampilkan saldo kredit')
}

const showTotalPelaporan = () => {
  activeView.value = 'laporan'
  console.log('Menampilkan total pelaporan')
}

const updateTime = () => {
  currentTime.value = new Date()
}

let timeInterval

onMounted(() => {
  timeInterval = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})
</script>

<style scoped>
.welcome-container {
  margin-bottom: 2rem;
}

.welcome-header {
  background: linear-gradient(135deg, #2563EB 0%, #377CF4 100%);
  color: white;
  padding: 2rem 0;
  border-radius: 1rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
}

.welcome-header::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  opacity: 0.3;
}

.welcome-content {
  position: relative;
  z-index: 2;
}

.welcome-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.welcome-subtitle {
  font-size: 1.1rem;
  margin-bottom: 0;
  opacity: 0.9;
}

.user-profile-section {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  position: relative;
  z-index: 2;
  margin-right: 40px;  
}

.user-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden; 
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Saldo Section Styles */
.saldo-section {
  margin-bottom: 2rem;
  margin-left: -15px;
  width: 100%; /* Add this */
}

.saldo-card {
  background: white;
  border-radius: 1rem;
  margin-left: 24px;
  padding: 16px;
  width: 82rem; /* Increase from 81.5rem to make it wider */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
  margin-bottom: 1.5rem; 
}

.saldo-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.saldo-title-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.saldo-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.saldo-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #4285f4, #1a73e8);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.saldo-amount {
  margin-bottom: 1.5rem; /* Mengurangi margin dari 2rem ke 1.5rem */
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}

.currency {
  font-size: 1.2rem;
  color: #4285f4;
  font-weight: 500;
}

.amount {
  font-size: 3rem;
  font-weight: 700;
  color: #4285f4;
  letter-spacing: -1px;
}

/* Laporan View Styles */
.laporan-view {
  padding: 1rem 0;
}

.laporan-stats {
  margin-bottom: 2rem;
}

.stat-card {
  background: #f8fafc;
  border-radius: 1rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  color: white;
  flex-shrink: 0;
}

.total-laporan .stat-icon {
  background: linear-gradient(135deg, #4285f4, #1a73e8);
}

.sedang-diproses .stat-icon {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.proses-selesai .stat-icon {
  background: linear-gradient(135deg, #10b981, #059669);
}

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
}

.saldo-actions {
  display: flex;
  gap: 1rem;
}

.saldo-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  font-size: 0.9rem;
  border: 2px solid #e5e7eb;
  background: white;
  color: #6b7280;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.saldo-btn:hover {
  border-color: #4285f4;
  color: #4285f4;
  transform: translateY(-1px);
}

.saldo-btn.active {
  background: #4285f4;
  border-color: #4285f4;
  color: white;
}

.saldo-btn.active:hover {
  background: #1a73e8;
  border-color: #1a73e8;
  color: white;
}

.info-cards-section {
  margin-top: -0.5rem;
}

.info-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  color: white;
  flex-shrink: 0;
}

.date-card .card-icon {
  background: linear-gradient(135deg, #4285f4, #1a73e8);
}

.level-card .card-icon {
  background: linear-gradient(135deg, #ff6b35, #f7931e);
}

.time-card .card-icon {
  background: linear-gradient(135deg, #34a853, #137333);
}

.card-content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.card-label {
  font-size: 0.85rem;
  color: #666;
  font-weight: 500;
}

.card-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: #333;
}

.card-date {
  font-size: 0.8rem;
  color: #888;
}

/* Media Queries */
@media (max-width: 768px) {
  .welcome-title {
    font-size: 1.8rem;
  }
  
  .welcome-subtitle {
    font-size: 1rem;
  }
  
  .user-profile-section {
    justify-content: flex-start;
    margin-top: 1rem;
  }
  
  .saldo-card {
    padding: 1.5rem;
  }
  
  .amount {
    font-size: 2.5rem;
  }
  
  .saldo-actions {
    flex-direction: column;
  }
  
  .info-card {
    padding: 1.25rem;
  }
  
  .card-icon {
    width: 45px;
    height: 45px;
  }
}

@media (max-width: 576px) {
  .welcome-header {
    padding: 1.5rem 0;
  }
  
  .welcome-title {
    font-size: 1.5rem;
  }
  
  .saldo-card {
    padding: 1rem;
  }
  
  .amount {
    font-size: 2rem;
  }
  
  .saldo-title {
    font-size: 1.25rem;
  }
  
  .info-card {
    padding: 1rem;
  }
  
  .card-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
}

/* Animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.welcome-header,
.saldo-card,
.info-card {
  animation: fadeIn 0.6s ease-out;
}

.saldo-card {
  animation-delay: 0.1s;
}

.info-card:nth-child(2) {
  animation-delay: 0.2s;
}

.info-card:nth-child(3) {
  animation-delay: 0.3s;
}
</style>