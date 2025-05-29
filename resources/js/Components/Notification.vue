<template>
  <transition name="toast-fade" appear>
    <div
      v-if="show"
      class="toast-notification"
      :class="'toast-' + type"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
    >
      <div class="toast-icon">
        <i :class="iconClass"></i>
      </div>
      <div class="toast-content">{{ message }}</div>
      <button
        type="button"
        class="toast-close"
        aria-label="Close"
        @click="$emit('close')"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: Boolean,
  message: String,
  type: {
    type: String,
    default: 'success',
  },
})

const iconClass = computed(() => {
  switch (props.type) {
    case 'success': return 'fas fa-check-circle'
    case 'danger': return 'fas fa-exclamation-circle'
    case 'warning': return 'fas fa-exclamation-triangle'
    default: return 'fas fa-info-circle'
  }
})
</script>

<style scoped>
.toast-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  min-width: 300px;
  max-width: 400px;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  z-index: 1200;
  background-color: #d4edda;
  border-left: 4px solid #28a745;
}

.toast-danger {
  background-color: #f8d7da;
  border-left-color: #dc3545;
}

.toast-icon {
  margin-right: 0.75rem;
  font-size: 1.25rem;
  color: inherit;
}

.toast-content {
  flex-grow: 1;
  font-weight: 500;
}

.toast-close {
  background: none;
  border: none;
  font-size: 0.9rem;
  opacity: 0.5;
  cursor: pointer;
  transition: opacity 0.2s;
}

.toast-close:hover {
  opacity: 1;
}

/* Animations for fade in/out */
.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.3s ease;
}

.toast-fade-enter-to,
.toast-fade-leave-from {
  opacity: 1;
  transform: translateX(0);
}
</style>
