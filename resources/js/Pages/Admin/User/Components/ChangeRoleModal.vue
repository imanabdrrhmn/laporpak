<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="cancel">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah Role Pengguna</h5>
        <button type="button" class="close-button" @click="cancel">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="modal-body">
        <p v-if="user">Ubah role untuk <strong>{{ user.name }}</strong>?</p>
        <div class="form-group">
          <label for="roleSelect" class="form-label">Pilih Role Baru</label>
          <select
            id="roleSelect"
            v-model="selectedRole"
            class="form-select"
            :disabled="isSubmitting"
          >
            <option value="" disabled>Pilih role</option>
            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
          </select>
        </div>
        <div v-if="isSubmitting" class="loading-overlay">
          <div v-if="submitSuccess" class="success-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="3">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
          </div>
          <div v-else class="loading-spinner"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          @click="cancel"
          :disabled="isSubmitting"
        >
          Batal
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="confirm"
          :disabled="!selectedRole || isSubmitting"
        >
          Simpan
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  user: Object,
  roles: Array,
  csrf: String
})

const emit = defineEmits(['close', 'role-changed'])

const selectedRole = ref('')
const isSubmitting = ref(false)
const submitSuccess = ref(false)

function cancel() {
  selectedRole.value = ''
  emit('close')
}

async function confirm() {
  if (!selectedRole.value || !props.user) return

  isSubmitting.value = true
  submitSuccess.value = false

  emit('role-changed', {
    userId: props.user.id,
    selectedRole: selectedRole.value,
    userName: props.user.name
  })

  isSubmitting.value = false
  submitSuccess.value = true
  await new Promise(resolve => setTimeout(resolve, 1000))
  cancel()
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: relative;
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  font-size: 1rem;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  transition: color 0.2s ease;
}

.close-button:hover {
  color: #1f2937;
}

.modal-body {
  padding: 20px;
  position: relative;
}

.form-group {
  margin-bottom: 16px;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  color: #1f2937;
  cursor: pointer;
}

.form-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 20px;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover {
  background: #4b5563;
}

.btn-primary {
  background: #0d6efd;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 8px;
}

.loading-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.success-icon {
  animation: scaleIn 0.3s ease;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes scaleIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>