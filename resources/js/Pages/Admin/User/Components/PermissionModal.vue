<template>
  <Teleport to="body">
    <div 
      class="modal-overlay"
      @click="handleOverlayClick"
      :class="{ 'modal-overlay--visible': isVisible }"
    >
      <div 
        class="modal-container"
        @click.stop
        :class="{ 'modal-container--visible': isVisible }"
      >
        <!-- Header -->
        <div class="modal-header">
          <div class="modal-header__content">
            <div class="user-avatar">
              <img 
                v-if="user.avatar_url && !showFallback" 
                :src="user.avatar_url" 
                :alt="user.name"
                class="user-avatar__image"
                @error="handleImageError"
              />
              <span v-else class="user-avatar__text">{{ getUserInitials(user.name) }}</span>
            </div>
            <div class="modal-header__info">
              <h2 class="modal-title">Kelola Izin Pengguna</h2>
              <p class="modal-subtitle">{{ user.name }}</p>
            </div>
          </div>
          <button 
            type="button" 
            class="modal-close-btn"
            @click="handleClose"
            @mouseenter="startHoverAnimation"
            @mouseleave="stopHoverAnimation"
            aria-label="Tutup modal"
            :class="{ 'modal-close-btn--rotating': isRotating }"
          >
            <svg 
              class="close-icon"
              :class="{ 'close-icon--rotating': isRotating }"
              width="24" 
              height="24" 
              viewBox="0 0 24 24" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>
          <!-- Permissions Section -->
          <div class="permission-header">
            <h3 class="permission-title">Pilih Izin</h3>
            <div class="permission-actions">
              <button 
                type="button" 
                class="btn btn--text btn--small"
                @click="selectAllPermissions"
              >
                Pilih Semua
              </button>
              <button 
                type="button" 
                class="btn btn--text btn--small"
                @click="clearAllPermissions"
              >
                Hapus Semua
              </button>
            </div>
          </div>
          
          <form @submit.prevent="handleSubmit" class="permission-form">
            <!-- Loading Overlay -->
            <div v-if="isSubmitting" class="loading-overlay">
              <div class="loading-content">
                <div v-if="submitSuccess" class="success-icon">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="3">
                    <polyline points="20,6 9,17 4,12"></polyline>
                  </svg>
                </div>
                <div v-else class="loading-spinner-container">
                  <div class="loading-spinner-large"></div>
                  <div class="loading-spinner-glow"></div>
                </div>
                <h4 class="loading-title">{{ submitSuccess ? 'Berhasil Disimpan!' : 'Menyimpan Perubahan' }}</h4>
                <p class="loading-subtitle">{{ submitSuccess ? 'Izin pengguna telah diperbarui.' : 'Mohon tunggu, sedang memperbarui izin pengguna...' }}</p>
                <div v-if="!submitSuccess" class="loading-progress">
                  <div class="loading-progress-bar"></div>
                </div>
              </div>
            </div>

            <!-- Permissions Grid -->
            <div class="permission-grid" :class="{ 'permission-grid--loading': isSubmitting }">
              <div 
                v-for="perm in allPermissions" 
                :key="perm" 
                class="permission-item"
                :class="{ 'permission-item--selected': selectedPermissions.includes(perm) }"
              >
                <input
                  class="permission-checkbox"
                  type="checkbox"
                  :checked="selectedPermissions.includes(perm)"
                  @change="updatePermissions(perm)"
                  :id="`perm-${perm}`"
                />
                <label class="permission-label" :for="`perm-${perm}`">
                  <div class="permission-checkbox-custom">
                    <svg 
                      v-if="selectedPermissions.includes(perm)"
                      class="permission-checkbox-icon" 
                      width="16" 
                      height="16" 
                      viewBox="0 0 24 24" 
                      fill="none" 
                      stroke="currentColor" 
                      stroke-width="3"
                    >
                      <polyline points="20,6 9,17 4,12"></polyline>
                    </svg>
                  </div>
                  <div class="permission-content">
                    <span class="permission-name">{{ formatPermissionName(perm) }}</span>
                    <span class="permission-description">{{ getPermissionDescription(perm) }}</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Region Section -->
            <div class="region-header" style="margin-top: 1.5rem;">
              <h3 class="region-title">Pilih Region</h3>
              <div class="region-actions">
                <button type="button" class="btn btn--text btn--small" @click="selectAllRegions">Pilih Semua</button>
                <button type="button" class="btn btn--text btn--small" @click="clearAllRegions">Hapus Semua</button>
              </div>
            </div>

            <div class="region-grid" :class="{ 'region-grid--loading': isSubmitting }" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem;">
              <div 
                v-for="region in allRegions"
                :key="region"
                class="region-item"
                :class="{ 'region-item--selected': selectedRegions.includes(region) }"
                style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;"
              >
                <input
                  type="checkbox"
                  :checked="selectedRegions.includes(region)"
                  @change="updateRegions(region)"
                  :id="`region-${region}`"
                  class="region-checkbox"
                  style="display:none;"
                />
                <label 
                  :for="`region-${region}`" 
                  class="region-label"
                  style="user-select:none;"
                >
                  {{ region }}
                </label>
              </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer" style="margin-top: 1.5rem; display:flex; justify-content: space-between; align-items: center;">
              <div class="selected-count">
                {{ selectedPermissions.length }} dari {{ allPermissions.length }} izin dipilih — 
                {{ selectedRegions.length }} dari {{ allRegions.length }} region dipilih
              </div>
              <div class="modal-actions">
                <button 
                  type="button" 
                  class="btn btn--secondary"
                  @click="handleClose"
                >
                  Batal
                </button>
                <button 
                  type="submit" 
                  class="btn btn--primary"
                  :disabled="isSubmitting"
                  :class="{ 'btn--loading': isSubmitting }"
                >
                  <span v-if="isSubmitting" class="btn-spinner"></span>
                  <svg 
                    v-if="isSubmitting" 
                    class="btn-icon btn-icon--loading" 
                    width="16" 
                    height="16" 
                    viewBox="0 0 24 24" 
                    fill="none"
                  >
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.25"/>
                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                  <svg 
                    v-else
                    class="btn-icon" 
                    width="16" 
                    height="16" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2"
                  >
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17,21 17,13 7,13 7,21"/>
                    <polyline points="7,3 7,8 15,8"/>
                  </svg>
                  <span class="btn-text">
                    {{ isSubmitting ? 'Menyimpan Perubahan...' : 'Simpan Perubahan' }}
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>


<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'


const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  allPermissions: {
    type: Array,
    required: true
  },
  selectedPermissions: {
    type: Array,
    default: () => []
  },
  allowedRegions: { type: Array, default: () => [] },
  allRegions: { type: Array, default: () => [] },   

})

const emit = defineEmits(['close', 'submit', 'update:selected-permissions', 'update:allowed-regions'])

const isVisible = ref(false)
const isSubmitting = ref(false)
const showFallback = ref(false)
const submitSuccess = ref(false)
const errorMessage = ref('')
const isRotating = ref(false)


// Animasi saat modal dibuka
onMounted(async () => {
  await nextTick()
  isVisible.value = true
})

// Tangani error gambar avatar
const handleImageError = () => {
  showFallback.value = true
}

// Fungsi pembantu
const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

const formatPermissionName = (permission) => {
  return permission
    .replace(/[_-]/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
}

const getPermissionDescription = (permission) => {
  const descriptions = {
    'read': 'Dapat melihat data',
    'write': 'Dapat mengubah data',
    'delete': 'Dapat menghapus data',
    'admin': 'Akses administrator penuh',
    'user_management': 'Mengelola pengguna',
    'settings': 'Mengatur konfigurasi',
    'reports': 'Melihat laporan',
    'export': 'Mengekspor data',
    'view_reports_penipuan' : 'Melihat Laporan Penipuan',
    'view_reports_infrastruktur' : 'Melihat Laporan Kerusakan Infrastruktur',
    'verify_reports': 'Verifikasi Laporan'
  }
  return descriptions[permission] || 'Akses khusus'
}

// Penanganan event
const updatePermissions = (permission) => {
  const newPermissions = [...props.selectedPermissions]
  const index = newPermissions.indexOf(permission)
  
  if (index === -1) {
    newPermissions.push(permission)
  } else {
    newPermissions.splice(index, 1)
  }
  
  emit('update:selected-permissions', newPermissions)
}

const selectAll = () => {
  emit('update:selected-permissions', [...props.allPermissions])
}

const clearAll = () => {
  emit('update:selected-permissions', [])
}

const handleClose = async () => {
  isRotating.value = true // Mulai animasi rotasi
  isVisible.value = false
  await new Promise(resolve => setTimeout(resolve, 300)) // Sesuaikan durasi animasi tutup
  isRotating.value = false // Reset rotasi
  emit('close')
}

const handleOverlayClick = (event) => {
  if (event.target === event.currentTarget) {
    handleClose()
  }
}

const startHoverAnimation = () => {
  isRotating.value = true
}

const stopHoverAnimation = () => {
  isRotating.value = false
}

const handleSubmit = async () => {
  isSubmitting.value = true
  errorMessage.value = ''
  submitSuccess.value = false

  try {
    // Panggil emit untuk submit izin, termasuk allowed regions
    await emit('submit', {
      userId: props.user.id,
      permissions: props.selectedPermissions,
      allowed_regions: selectedRegions.value, 
    })
    // Tampilkan animasi sukses
    submitSuccess.value = true
    await new Promise(resolve => setTimeout(resolve, 1000)) // Animasi sukses 1 detik
  } catch (error) {
    console.error('Error menyimpan izin:', error)
    errorMessage.value = 'Gagal menyimpan izin. Silakan coba lagi.'
  } finally {
    // Reset status submit
    isSubmitting.value = false
    // Tunggu 3 detik sebelum reset animasi sukses agar pengguna melihatnya
    await new Promise(resolve => setTimeout(resolve, 3000))
    submitSuccess.value = false
  }
}

const selectedRegions = ref([...props.allowedRegions])
watch(() => props.allowedRegions, (newVal) => {
  selectedRegions.value = [...newVal]
})

const updateRegions = (region) => {
  const index = selectedRegions.value.indexOf(region)
  const updated = [...selectedRegions.value]

  if (index === -1) {
    updated.push(region)
  } else {
    updated.splice(index, 1)
  }

  selectedRegions.value = updated
  emit('update:allowed-regions', updated)
}

const selectAllRegions = () => {
  selectedRegions.value = [...props.allRegions]
  emit('update:allowed-regions', selectedRegions.value)
}

const clearAllRegions = () => {
  selectedRegions.value = []
  emit('update:allowed-regions', selectedRegions.value)
}

</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.modal-overlay--visible {
  opacity: 1;
}

/* Modal Container */
.modal-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  transform: scale(0.95) translateY(20px);
  transition: transform 0.3s ease;
}

.modal-container--visible {
  transform: scale(1) translateY(0);
}

/* Header */
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 24px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header__content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-avatar__image {
  width: 100%;
  height: 100%;
  border-radius: 12px;
  object-fit: cover;
}

.user-avatar__text {
  color: white;
  font-weight: 600;
  font-size: 18px;
}

.modal-header__info {
  flex: 1;
}

.modal-title {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  line-height: 1.2;
}

.modal-subtitle {
  margin: 4px 0 0;
  font-size: 14px;
  color: #6b7280;
}

.modal-close-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 8px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.close-icon {
  transition: transform 0.3s ease;
}

.close-icon--rotating {
  transform: rotate(90deg);
}

/* Body */
.modal-body {
  padding: 0 24px 24px;
  max-height: calc(90vh - 140px);
  overflow-y: auto;
}

.error-message {
  background: #fee2e2;
  border-left: 4px solid #dc2626;
  color: #991b1b;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.permission-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  padding-top: 8px;
}

.permission-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.permission-actions {
  display: flex;
  gap: 8px;
}

/* Permission Form */
.permission-form {
  position: relative;
}

/* Loading Overlay */
.loading-overlay {
  position: absolute;
  top: 0;
  left: -24px;
  right: -24px;
  bottom: -24px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 16px;
  animation: fadeIn 0.3s ease;
}

.loading-content {
  text-align: center;
  max-width: 280px;
}

.loading-spinner-container {
  position: relative;
  width: 60px;
  height: 60px;
  margin: 0 auto 20px;
}

.loading-spinner-large {
  width: 60px;
  height: 60px;
  border: 5px solid #e5e7eb;
  border-top: 5px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  position: absolute;
  top: 0;
  left: 0;
}

.loading-spinner-glow {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
  animation: pulse 2s ease-in-out infinite;
  position: absolute;
  top: 0;
  left: 0;
}

.success-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 20px;
  animation: scaleIn 0.3s ease;
}

.loading-title {
  margin: 0 0 8px;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.loading-subtitle {
  margin: 0 0 20px;
  font-size: 14px;
  color: #6b7280;
  line-height: 1.4;
}

.loading-progress {
  width: 100%;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
  margin-top: 16px;
}

.loading-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8, #3b82f6);
  background-size: 200% 100%;
  animation: progressMove 1.5s ease-in-out infinite;
  border-radius: 3px;
}

.permission-grid--loading {
  filter: blur(2px);
  opacity: 0.5;
  pointer-events: none;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes progressMove {
  0% {
    background-position: 0% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.2;
  }
  100% {
    transform: scale(1);
    opacity: 0.5;
  }
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.permission-grid {
  display: grid;
  gap: 12px;
  margin-bottom: 24px;
}

.permission-item {
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  transition: all 0.2s ease;
  overflow: hidden;
}

.permission-item:hover {
  border-color: #d1d5db;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.permission-item--selected {
  border-color: #3b82f6;
  background: #eff6ff;
}

.permission-checkbox {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.permission-label {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.permission-checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.permission-item--selected .permission-checkbox-custom {
  background: #3b82f6;
  border-color: #3b82f6;
}

.permission-checkbox-icon {
  color: white;
}

.permission-content {
  flex: 1;
}

.permission-name {
  display: block;
  font-weight: 500;
  color: #1f2937;
  font-size: 14px;
  line-height: 1.4;
}

.permission-description {
  display: block;
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}

/* Footer */
.modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
  gap: 16px;
}

.selected-count {
  font-size: 14px;
  color: #6b7280;
  flex: 1;
}

.modal-actions {
  display: flex;
  gap: 12px;
}

/* Buttons */
.btn {
  border: none;
  border-radius: 8px;
  font-weight: 500;
  font-size: 14px;
  padding: 10px 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn--primary {
  background: #3b82f6;
  color: white;
}

.btn--primary:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn--secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn--secondary:hover {
  background: #e5e7eb;
}

.btn--text {
  background: none;
  color: #3b82f6;
  padding: 8px 12px;
}

.btn--text:hover {
  background: #eff6ff;
  color: #2563eb;
}

.btn--small {
  font-size: 12px;
  padding: 6px 12px;
}

.btn--loading {
  position: relative;
  pointer-events: none;
}

.btn--loading .btn-text {
  opacity: 0.7;
}

.btn-icon {
  transition: all 0.3s ease;
}

.btn-icon--loading {
  animation: spin 1s linear infinite;
}

.btn-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 4px;
}

/* Responsive */
@media (max-width: 640px) {
  .modal-container {
    width: 95%;
    margin: 20px;
  }
  
  .modal-header,
  .modal-body {
    padding-left: 16px;
    padding-right: 16px;
  }
  
  .modal-footer {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  
  .modal-actions {
    width: 100%;
  }
  
  .btn {
    flex: 1;
  }
  
  .permission-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}

/* Force white background */
.modal-container {
  background: white !important;
  color: #1f2937 !important;
}

.modal-title {
  color: #1f2937 !important;
}

.modal-subtitle {
  color: #6b7280 !important;
}

.permission-item {
  background: white !important;
}

.permission-name {
  color: #1f2937 !important;
}

.permission-description {
  color: #6b7280 !important;
}

.selected-count {
  color: #6b7280 !important;
}
.region-item {
  padding: 0.5rem;
  border: 1px solid #ccc; /* Default border */
  border-radius: 4px;
  cursor: pointer;
  background-color: #fff; /* Explicit default background */
  color: #374151; /* Default text color for the region name */
  transition: border-color 0.2s ease, background-color 0.2s ease, outline 0.2s ease, color 0.2s ease;
  text-align: center; /* Center region name */
  position: relative; /* Good for potential outline rendering consistency */
  display: flex; /* To center label content if needed, or for better control */
  align-items: center;
  justify-content: center;
}

.region-item:hover:not(.region-item--selected) { /* Hover effect only for non-selected items */
  border-color: #999; /* Darker border on hover */
  background-color: #f7f7f7; /* Slight background change on hover */
}

.region-item--selected {
  background-color: #eff6ff; /* Light blue background, consistent with permission-item--selected */
  border-color: #3b82f6;    /* Border color changes to primary theme color */
  color: #1e40af;           /* Darker blue text for better contrast on light blue bg */
  outline: 1px solid #3b82f6; /* Add 1px outline of the same primary color */
  outline-offset: 0px;         /* Outline sits immediately outside the border, making it look like a 2px border */
}

.region-label {
  display: block; /* Make label fill the item (optional, depends on desired click area) */
  /* color is inherited from .region-item by default */
  font-weight: normal; /* Default font weight */
  transition: color 0.2s ease, font-weight 0.2s ease; /* Smooth transition for text changes */
}

.region-item--selected .region-label {
  /* color: #1e40af; /* Text color is already set by .region-item--selected */
  font-weight: 500; /* Make text slightly bolder for selected item */
}

/* Styling for when the region grid is in a loading state */
.region-grid--loading .region-item {
  filter: blur(2px);
  opacity: 0.5;
  pointer-events: none;
  user-select: none; /* Prevent text selection during loading */
}

</style>