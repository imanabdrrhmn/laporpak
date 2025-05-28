<template>
  <div class="aksi-cepat-container">
    <div class="quick-actions-wrapper" :class="wrapperVariant">
      <h5 class="section-title">{{ title || 'Aksi Cepat' }}</h5>
      <div class="quick-actions-grid" :class="gridLayout">
        <div
          v-for="(aksi, index) in displayActions"
          :key="index"
          :class="getActionClasses(aksi)"
          @click="handleAction(aksi)"
          :disabled="aksi.disabled"
          :title="aksi.tooltip"
          role="button"
          tabindex="0"
          @keydown.enter="handleAction(aksi)"
        >
          <div class="action-icon" :class="aksi.iconVariant">
            <i :class="aksi.iconClass"></i>
            <span v-if="aksi.badge && props.showBadges" class="action-badge">{{ aksi.badge }}</span>
          </div>
          <span class="action-label">{{ aksi.label }}</span>
          <span v-if="aksi.description" class="action-description">{{ aksi.description }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  title: { type: String, default: 'Aksi Cepat' },
  customActions: { type: Array, default: () => [] },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'modern', 'minimal', 'colorful'].includes(value)
  },
  layout: {
    type: String,
    default: 'grid',
    validator: (value) => ['grid', 'horizontal', 'vertical'].includes(value)
  },
  itemSize: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  maxItems: { type: Number, default: null },
  showBadges: { type: Boolean, default: false },
  animationStyle: {
    type: String,
    default: 'hover',
    validator: (value) => ['hover', 'pulse', 'bounce', 'none'].includes(value)
  }
})

const emit = defineEmits(['action-click', 'action-hover'])

const defaultActions = ref([
  {
    label: 'Membuat Laporan',
    iconClass: 'bi bi-pencil-square',
    active: false,
    url: '/pelaporan',
    color: '#28a745',
    iconVariant: 'success',
    description: 'Buat laporan baru',
    tooltip: 'Klik untuk membuat laporan baru',
  },
  {
    label: 'Cari Laporan',
    iconClass: 'bi bi-search',
    active: false,
    url: '/CariLaporan',
    color: '#17a2b8',
    iconVariant: 'info',
    description: 'Temukan laporan',
    tooltip: 'Cari laporan yang sudah ada'
  },
  {
    label: 'Laporan Saya',
    iconClass: 'bi bi-file-text',
    active: false,
    url: '/laporan-saya',
    color: '#6f42c1',
    iconVariant: 'purple',
    description: 'Daftar laporan Anda',
    tooltip: 'Lihat semua laporan Anda'
  },
  {
    label: 'Tambah Credit',
    iconClass: 'bi bi-credit-card',
    active: false,
    url: '/top-ups',
    color: '#fd7e14',
    iconVariant: 'warning',
    description: 'Top up credit',
    tooltip: 'Tambahkan credit ke akun Anda',
  },
  {
    label: 'Verifikasi Data',
    iconClass: 'bi bi-check-circle',
    active: false,
    url: '/verifikasi',
    color: '#dc3545',
    iconVariant: 'danger',
    description: 'Verifikasi informasi',
    tooltip: 'Verifikasi data akun Anda',
    disabled: false
  }
])

const displayActions = computed(() => {
  const actions = props.customActions.length > 0 ? props.customActions : defaultActions.value
  return props.maxItems ? actions.slice(0, props.maxItems) : actions
})

const wrapperVariant = computed(() => `wrapper-${props.variant}`)
const gridLayout = computed(() => `layout-${props.layout} size-${props.itemSize}`)

const getActionClasses = (aksi) => {
  return [
    'action-item',
    `variant-${props.variant}`,
    `animation-${props.animationStyle}`,
    {
      'active': aksi.active,
      'disabled': aksi.disabled,
      [`color-${aksi.iconVariant}`]: aksi.iconVariant
    }
  ]
}

const handleAction = (aksi) => {
  if (aksi.disabled) return
  emit('action-click', aksi)
  if (aksi.url) {
    if (aksi.newTab) {
      window.open(aksi.url, '_blank')
    } else {
      window.location.href = aksi.url
    }
  }
  if (aksi.callback && typeof aksi.callback === 'function') {
    aksi.callback(aksi)
  }
}
</script>

<style scoped>
.aksi-cepat-container {
  padding: clamp(1rem, 3vw, 1.5rem);
  margin-bottom: 2rem;
  width: 100%;
}

.section-title {
  font-size: clamp(1.25rem, 3vw, 1.5rem);
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1.5rem;
  padding-left: 0.5rem;
}

.quick-actions-wrapper {
  background: #ffffff;
  border-radius: 12px;
  padding: clamp(1rem, 3vw, 1.5rem);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #e9ecef;
  transition: all 0.3s ease;
  width: 100%; /* Mengganti nilai tetap dengan 100% */
  max-width: 100%; /* Membatasi lebar maksimum */
}

.wrapper-modern {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.wrapper-minimal {
  background: transparent;
  border: none;
  box-shadow: none;
  padding: 1rem;
}

.wrapper-colorful {
  background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
  border: none;
}

.quick-actions-grid {
  display: grid;
  gap: clamp(0.5rem, 2vw, 1rem);
}

.layout-grid {
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
}

.layout-horizontal {
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  grid-auto-flow: column;
  overflow-x: auto;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

.layout-vertical {
  grid-template-columns: 1fr;
  max-width: 300px;
  margin: 0 auto;
}

.size-small .action-item {
  min-height: 80px;
  padding: clamp(0.5rem, 2vw, 1rem) 0.75rem;
}

.size-medium .action-item {
  min-height: 100px;
  padding: clamp(1rem, 2.5vw, 1.5rem) 1rem;
}

.size-large .action-item {
  min-height: 140px;
  padding: clamp(1.5rem, 3vw, 2rem) 1.5rem;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #f8f9fa;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  color: #6c757d;
  position: relative;
  overflow: hidden;
}

.action-item:hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
  left: 100%;
}

.variant-default { background: #f8f9fa; }
.variant-modern {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
}
.variant-minimal { background: transparent; border: 1px solid #dee2e6; }
.variant-colorful { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); }

.action-item.active {
  background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%);
  color: white;
  border-color: #1976d2;
  box-shadow: 0 4px 15px rgba(66, 133, 244, 0.3);
}

.color-success { --item-color: #28a745; }
.color-info { --item-color: #17a2b8; }
.color-warning { --item-color: #ffc107; }
.color-danger { --item-color: #dc3545; }
.color-purple { --item-color: #6f42c1; }

.action-item[class*="color-"]:not(.active) {
  border-left: 4px solid var(--item-color);
}

.action-item[class*="color-"]:not(.active):hover {
  background: var(--item-color);
  color: white;
}

.animation-hover:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.animation-pulse {
  animation: pulse 2s infinite;
}

.animation-bounce:hover {
  animation: bounce 0.6s;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
  40%, 43% { transform: translateY(-8px); }
  70% { transform: translateY(-4px); }
  90% { transform: translateY(-2px); }
}

.action-item.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.action-icon {
  margin-bottom: 0.75rem;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-icon i {
  font-size: clamp(1.5rem, 4vw, 2rem);
  transition: all 0.3s ease;
}

.action-item:hover .action-icon i {
  transform: scale(1.1);
}

.action-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.action-label {
  font-size: clamp(0.75rem, 2vw, 0.875rem);
  font-weight: 500;
  text-align: center;
  line-height: 1.3;
  word-wrap: break-word;
  margin-bottom: 0.25rem;
}

.action-description {
  font-size: clamp(0.65rem, 1.5vw, 0.75rem);
  opacity: 0.7;
  text-align: center;
  line-height: 1.2;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .layout-grid {
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  }
}

@media (max-width: 768px) {
  .quick-actions-wrapper {
    padding: 1rem;
  }

  .layout-grid {
    grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
  }

  .size-medium .action-item {
    min-height: 90px;
    padding: 1rem 0.75rem;
  }

  .action-icon i {
    font-size: 1.5rem;
  }

  .action-label {
    font-size: 0.75rem;
  }
}

@media (max-width: 576px) {
  .quick-actions-wrapper {
    padding: 0.75rem;
  }

  .layout-grid {
    grid-template-columns: 1fr;
  }

  .action-item {
    flex-direction: row;
    text-align: left;
    justify-content: flex-start;
    min-height: auto;
    padding: 0.75rem;
  }

  .action-icon {
    margin-bottom: 0;
    margin-right: 1rem;
  }

  .action-icon i {
    font-size: 1.25rem;
  }

  .action-label {
    font-size: 0.7rem;
  }
}
</style>