<template>
  <transition name="fade">
    <div v-if="visible" :class="['notification', type]" @click="close">
      <div class="icon" v-html="icon"></div>
      <div class="message">{{ message }}</div>
      <button class="close-btn" aria-label="Close" @click.stop="close">&times;</button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'success', 
  },
  message: {
    type: String,
    required: true,
  },
  duration: {
    type: Number,
    default: 5000, // ms
  },
})

const visible = ref(true)
let timer = null

const icons = {
  success: `<svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
  error: `<svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
}

const icon = icons[props.type] || ''

function close() {
  visible.value = false
}

onMounted(() => {
  timer = setTimeout(() => {
    close()
  }, props.duration)
})
</script>

<style scoped>
.notification {
  position: fixed;
  top: 1rem;
  left: 50%;
  transform: translateX(-50%);
  min-width: 280px;
  max-width: 90vw;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  font-weight: 600;
  cursor: pointer;
  z-index: 1050;
  user-select: none;
  color: white;
}

.notification.success {
  background-color: #16a34a; /* green-600 */
}

.notification.error {
  background-color: #dc2626; /* red-600 */
}

.icon {
  margin-right: 0.75rem;
  display: flex;
  align-items: center;
}

.icon-svg {
  width: 24px;
  height: 24px;
  stroke: currentColor;
}

.message {
  flex-grow: 1;
  font-size: 1rem;
}

.close-btn {
  background: transparent;
  border: none;
  color: inherit;
  font-size: 1.25rem;
  line-height: 1;
  cursor: pointer;
  padding: 0 0 0 0.5rem;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
