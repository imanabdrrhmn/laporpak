<template>
  <Teleport to="body">
    <Transition
      name="modal"
      enter-active-class="modal-enter-active"
      leave-active-class="modal-leave-active"
      enter-from-class="modal-enter-from"
      leave-to-class="modal-leave-to"
    >
      <div 
        v-if="show" 
        class="modal-overlay"
        @click.self="handleBackdropClick"
        @keydown.esc="handleEscapeKey"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="titleId"
      >
        <!-- Modal container -->
        <div 
          class="modal-dialog"
          role="document"
          @click.stop
        >
          <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
              <!-- Close button -->
              <button 
                type="button" 
                class="close-button" 
                @click="handleClose" 
                aria-label="Tutup modal"
                tabindex="0"
              >
                <span aria-hidden="true">&times;</span>
              </button>

              <!-- Success icon -->
              <div class="success-icon">
                <svg 
                  xmlns="http://www.w3.org/2000/svg" 
                  width="40" 
                  height="40" 
                  fill="currentColor" 
                  viewBox="0 0 16 16"
                  aria-hidden="true"
                >
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
              </div>

              <!-- Title -->
              <h4 :id="titleId" class="modal-title">
                Laporan Berhasil Terkirim!
              </h4>

              <!-- Description -->
              <p class="modal-description">
                Anda dapat memantau status tindak lanjut laporan melalui halaman riwayat
              </p>

              <!-- Action buttons -->
              <div class="button-group">
                <button 
                  @click="handleNavigate"
                  class="btn btn-primary"
                  type="button"
                >
                  <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                  </svg>
                  Lihat Riwayat Laporan
                </button>
                <button 
                  @click="handleFeedbackNavigate"
                  class="btn btn-secondary"
                  type="button"
                >
                  <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                  </svg>
                  Berikan Ulasan
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'

// Props dengan validasi yang lebih ketat
const props = defineProps({
  show: {
    type: Boolean,
    default: false,
    required: true
  },
  redirectPath: {
    type: String,
    default: '/laporan-saya',
    validator: (value) => typeof value === 'string' && value.length > 0
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close'])

// Reactive refs
const titleId = computed(() => `modal-title-${Math.random().toString(36).substr(2, 9)}`)

// Event handlers dengan debouncing untuk prevent spam clicking
let isNavigating = ref(false)

const handleClose = () => {
  if (!isNavigating.value) {
    emit('close')
  }
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && !isNavigating.value) {
    handleClose()
  }
}

const handleEscapeKey = () => {
  if (!isNavigating.value) {
    handleClose()
  }
}

// Navigation dengan loading state
const handleNavigate = async () => {
  if (isNavigating.value) return
  
  isNavigating.value = true
  try {
    await router.visit(props.redirectPath)
  } finally {
    isNavigating.value = false
  }
}

const handleFeedbackNavigate = async () => {
  if (isNavigating.value) return
  
  isNavigating.value = true
  try {
    await router.visit('/feedback')
  } finally {
    isNavigating.value = false
  }
}

// Focus management untuk accessibility
const focusManagement = () => {
  if (props.show) {
    nextTick(() => {
      // Focus pada modal untuk screen readers
      const modal = document.querySelector('.modal-overlay')
      if (modal) {
        modal.focus()
      }
    })
  }
}

// Lifecycle hooks
onMounted(() => {
  focusManagement()
})

// Watch show prop untuk body scroll lock
const handleBodyScroll = (lock) => {
  if (lock) {
    document.body.style.overflow = 'hidden'
    document.body.style.paddingRight = getScrollbarWidth() + 'px'
  } else {
    document.body.style.overflow = ''
    document.body.style.paddingRight = ''
  }
}

// Utility untuk menghitung scrollbar width
const getScrollbarWidth = () => {
  const scrollDiv = document.createElement('div')
  scrollDiv.style.cssText = 'width: 100px; height: 100px; overflow: scroll; position: absolute; top: -9999px;'
  document.body.appendChild(scrollDiv)
  const scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
  document.body.removeChild(scrollDiv)
  return scrollbarWidth
}

// Watch show prop
import { watch } from 'vue'
watch(() => props.show, (newVal) => {
  handleBodyScroll(newVal)
  if (newVal) {
    focusManagement()
  }
}, { immediate: true })

// Cleanup saat component unmount
onUnmounted(() => {
  handleBodyScroll(false)
})
</script>

<style scoped>
/* CSS Variables untuk konsistensi */
.modal-overlay {
  --modal-z-index: 1050;
  --modal-backdrop: rgba(0, 0, 0, 0.5);
  --modal-border-radius: 1rem;
  --modal-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  --success-color: #0d6efd;
  --success-bg: #0d6efd;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --transition-fast: 0.15s ease;
  --transition-medium: 0.3s ease;
  
  position: fixed;
  inset: 0;
  z-index: var(--modal-z-index);
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--modal-backdrop);
  backdrop-filter: blur(2px);
  padding: 1rem;
}

.modal-dialog {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}

.modal-content {
  position: relative;
  background: #ffffff;
  border: none;
  border-radius: var(--modal-border-radius);
  box-shadow: var(--modal-shadow);
  will-change: transform;
}

.modal-body {
  padding: 3rem 2rem 2rem;
  text-align: center;
  position: relative;
}

/* Close button optimization */
.close-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 2rem;
  height: 2rem;
  background: none;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all var(--transition-fast);
  z-index: 10;
}

.close-button:hover,
.close-button:focus {
  color: var(--text-primary);
  transform: scale(1.1);
  outline-offset: 2px;
}

/* Success icon dengan GPU acceleration */
.success-icon {
  width: 5rem;
  height: 5rem;
  background: var(--success-bg);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  color: white;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
  transform: translateZ(0); /* GPU layer */
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: var(--text-primary);
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.modal-description {
  color: var(--text-secondary);
  margin-bottom: 2rem;
  line-height: 1.5;
}

/* Button optimization */
.button-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all var(--transition-fast);
  text-decoration: none;
  transform: translateZ(0); /* GPU layer */
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  background: #f8fafc;
  color: var(--text-secondary);
  border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
  background: #f1f5f9;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

/* Animations dengan will-change untuk performa */
.modal-enter-active,
.modal-leave-active {
  transition: all var(--transition-medium);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.95) translateY(20px);
}

.modal-content {
  will-change: transform;
  transition: transform var(--transition-medium);
}

/* Success icon animation dengan optimisasi */
.success-icon {
  animation: successBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes successBounce {
  0% {
    opacity: 0;
    transform: scale(0.3) translateZ(0);
  }
  50% {
    opacity: 1;
    transform: scale(1.05) translateZ(0);
  }
  70% {
    transform: scale(0.9) translateZ(0);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateZ(0);
  }
}

/* Responsive dengan container queries jika didukung */
@container (max-width: 640px) {
  .modal-dialog {
    max-width: 320px;
  }
  
  .modal-body {
    padding: 2rem 1.5rem 1.5rem;
  }
}

/* Fallback untuk browser yang tidak support container queries */
@media (max-width: 640px) {
  .modal-dialog {
    max-width: 320px;
    margin: 0.5rem;
  }
  
  .modal-body {
    padding: 2rem 1.5rem 1.5rem;
  }
  
  .success-icon {
    width: 4rem;
    height: 4rem;
  }
  
  .modal-title {
    font-size: 1.25rem;
  }
}

/* Reduce motion untuk accessibility */
@media (prefers-reduced-motion: reduce) {
  .modal-enter-active,
  .modal-leave-active,
  .btn,
  .close-button,
  .success-icon {
    transition: none;
    animation: none;
  }
}

/* Tetap putih untuk semua mode */
.modal-content {
  background: #ffffff !important;
  color: #1f2937 !important;
}

.modal-title {
  color: #1f2937 !important;
}

.modal-description {
  color: #6b7280 !important;
}
</style>