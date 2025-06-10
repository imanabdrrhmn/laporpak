<template>
  <div>
    <!-- Flash Messages Container -->
    <div 
      v-if="flashMessage.show" 
      :class="flashMessage.alertClass"
      class="alert alert-dismissible fade show position-fixed"
      style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
      role="alert"
    >
      <div class="d-flex align-items-center">
        <i :class="flashMessage.iconClass" class="me-2"></i>
        <div>
          <strong v-if="flashMessage.title">{{ flashMessage.title }}</strong>
          <div v-if="flashMessage.message">{{ flashMessage.message }}</div>
        </div>
      </div>
      <button 
        type="button" 
        class="btn-close" 
        @click="closeFlash"
        aria-label="Close"
      ></button>
    </div>

    <!-- Demo Buttons untuk Testing -->
    <!-- <div class="container mt-5">
      <h2>Flash Message Demo</h2>
      <div class="row gap-2">
        <button @click="showSuccess" class="btn btn-success col-auto">Success Message</button>
        <button @click="showError" class="btn btn-danger col-auto">Error Message</button>
        <button @click="showWarning" class="btn btn-warning col-auto">Warning Message</button>
        <button @click="showInfo" class="btn btn-info col-auto">Info Message</button>
      </div>
    </div> -->
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Flash Message Composable (inline)
const useFlashMessage = () => {
  const flashMessage = reactive({
    show: false,
    type: '',
    title: '',
    message: '',
    alertClass: '',
    iconClass: '',
    duration: 5000
  })

  const flashTypes = {
    success: {
      alertClass: 'alert-success',
      iconClass: 'bi bi-check-circle-fill text-success',
      title: 'Berhasil!'
    },
    error: {
      alertClass: 'alert-danger', 
      iconClass: 'bi bi-exclamation-triangle-fill text-danger',
      title: 'Error!'
    },
    warning: {
      alertClass: 'alert-warning',
      iconClass: 'bi bi-exclamation-triangle-fill text-warning', 
      title: 'Peringatan!'
    },
    info: {
      alertClass: 'alert-info',
      iconClass: 'bi bi-info-circle-fill text-info',
      title: 'Informasi!'
    },
    status: {
      alertClass: 'alert-primary',
      iconClass: 'bi bi-envelope-check-fill text-primary',
      title: 'Status!'
    }
  }

  let timeoutId = null

  const showFlash = (type, message, title = null, duration = 5000) => {
    const config = flashTypes[type] || flashTypes.info
    
    flashMessage.show = true
    flashMessage.type = type
    flashMessage.message = message
    flashMessage.title = title || config.title
    flashMessage.alertClass = config.alertClass
    flashMessage.iconClass = config.iconClass
    flashMessage.duration = duration

    // Clear existing timeout
    if (timeoutId) {
      clearTimeout(timeoutId)
    }

    // Auto hide after duration
    timeoutId = setTimeout(() => {
      closeFlash()
    }, duration)
  }

  const closeFlash = () => {
    flashMessage.show = false
    if (timeoutId) {
      clearTimeout(timeoutId)
      timeoutId = null
    }
  }

  // Handle Laravel flash messages from backend
  const handleLaravelFlash = (props) => {
    // Handle success message
    if (props.success) {
      showFlash('success', props.message || props.success, 'Berhasil!')
    }
    // Handle error message  
    else if (props.error) {
      showFlash('error', props.error, 'Error!')
    }
    // Handle status message (untuk verification-link-sent)
    else if (props.status) {
      let message = ''
      let title = 'Status!'
      
      if (props.status === 'verification-link-sent') {
        message = 'Link verifikasi telah dikirim ke email Anda!'
        title = 'Email Terkirim!'
      } else {
        message = props.status
      }
      
      showFlash('status', message, title)
    }
    // Handle general message
    else if (props.message) {
      showFlash('info', props.message, 'Informasi!')
    }
  }

  return {
    flashMessage,
    showFlash,
    closeFlash,
    handleLaravelFlash
  }
}

// Use the composable
const { flashMessage, showFlash, closeFlash, handleLaravelFlash } = useFlashMessage()

// Get page props from Inertia
const page = usePage()

// Watch for flash messages from Laravel backend
watch(() => page.props, (newProps) => {
  if (newProps) {
    handleLaravelFlash(newProps)
  }
}, { immediate: true, deep: true })

// Demo functions
const showSuccess = () => {
  showFlash('success', 'Email berhasil diverifikasi! Sekarang Anda dapat mengakses semua fitur.', 'Berhasil!')
}

const showError = () => {
  showFlash('error', 'Token tidak valid atau telah kedaluwarsa.', 'Error!')
}

const showWarning = () => {
  showFlash('warning', 'Harap periksa kembali data yang Anda masukkan.', 'Peringatan!')
}

const showInfo = () => {
  showFlash('info', 'Link verifikasi telah dikirim ke email Anda!', 'Informasi!')
}
</script>

<style scoped>
.alert {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border: none;
  border-radius: 8px;
}

.alert-success {
  background-color: #d1edff;
  border-left: 4px solid #28a745;
}

.alert-danger {
  background-color: #f8d7da;
  border-left: 4px solid #dc3545;
}

.alert-warning {
  background-color: #fff3cd;
  border-left: 4px solid #ffc107;
}

.alert-info {
  background-color: #d1ecf1;
  border-left: 4px solid #17a2b8;
}

.alert-primary {
  background-color: #cce7ff;
  border-left: 4px solid #007bff;
}

.fade.show {
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.btn {
  border-radius: 6px;
  font-weight: 500;
}
</style>