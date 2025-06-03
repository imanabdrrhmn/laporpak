<template>
  <div>
    <!-- Search & Filter Row (tetap tampil di semua layar) -->
    <div class="row mb-4 align-items-center">
      <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="search-container">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0">
              <i class="bi bi-search text-primary"></i>
            </span>
            <input 
              type="text" 
              v-model="searchQuery" 
              class="form-control form-control-lg border-start-0" 
              placeholder="Cari pengguna..."
            >
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 text-md-end">
        <div class="custom-select-container mx-auto mx-md-0">
          <div class="select-wrapper">
            <i class="bi bi-shield-check select-icon"></i>
            <select v-model="filterRole" class="form-select form-select-lg custom-select">
              <option value="">Semua Role</option>
              <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Desktop: tabel (≥1025px) -->
    <div class="d-none d-custom-table-block">
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th class="col-no">No</th>
              <th class="col-name">Nama</th>
              <th class="col-email">Email</th>
              <th class="col-phone">Nomor</th>
              <th class="col-role">Role</th>
              <th class="col-change-role">Ubah Role</th>
              <th class="col-permission">Izin</th>
              <th class="col-action">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in filteredUsers" :key="user.id">
              <td class="col-no">{{ index + 1 }}</td>
              <td class="col-name">
                <div class="user-info">
                  <img :src="user.avatar_url" alt="Avatar" class="user-avatar" />
                  <span class="user-name">{{ user.name }}</span>
                </div>
              </td>
              <td class="col-email">
                <div class="email-text" :title="user.email">{{ user.email }}</div>
              </td>
              <td class="col-phone">{{ user.phone || '-' }}</td>
              <td class="col-role">
                <span v-if="user.roles.length" class="badge role-badge">{{ user.roles }}</span>
                <span v-else class="badge no-role-badge">Tidak Ada Role</span>
              </td>
              <td class="col-change-role">
                <div class="role-select-container">
                  <div 
                    v-if="userStates[user.id]?.isSubmitting" 
                    class="loading-overlay"
                  >
                    <div v-if="userStates[user.id]?.submitSuccess" class="success-icon">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"></polyline>
                      </svg>
                    </div>
                    <div v-else class="loading-spinner"></div>
                  </div>
                  <select
                    class="role-select"
                    @change="(e) => submitRoleChange(e, user.id, user.name)"
                    :disabled="userStates[user.id]?.isSubmitting"
                  >
                    <option disabled selected>Pilih role</option>
                    <option
                      v-for="role in roles"
                      :key="role"
                      :value="role"
                      :selected="user.roles === role"
                    >
                      {{ role }}
                    </option>
                  </select>
                </div>
              </td>
              <td class="col-permission">
                <button
                  class="btn-icon btn-primary"
                  @click="$emit('open-permission-modal', user)"
                  title="Edit Izin"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>
              </td>
              <td class="col-action">
                <form :action="route('admin.users.delete', user.id)" method="POST">
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" :value="csrf" />
                  <button
                    class="btn-icon btn-danger"
                    type="submit"
                    @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
                    title="Hapus Pengguna"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile: card layout (≤1024px) -->
    <div class="d-block d-custom-table-none">
      <div v-for="(user, index) in filteredUsers" :key="user.id" class="user-card">
        <div class="user-header">
          <img :src="user.avatar_url" alt="Avatar" class="user-avatar" />
          <div class="user-details">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-email">{{ user.email }}</div>
            <div class="user-phone">{{ user.phone || '-' }}</div>
          </div>
        </div>
        
        <div class="user-role-section">
          <strong>Role: </strong>
          <span v-if="user.roles.length" class="badge role-badge">{{ user.roles }}</span>
          <span v-else class="badge no-role-badge">Tidak Ada Role</span>
        </div>
        
        <div class="role-change-section">
          <label class="role-label"><strong>Ubah Role</strong></label>
          <select
            class="role-select-mobile"
            @change="(e) => submitRoleChange(e, user.id, user.name)"
            :disabled="userStates[user.id]?.isSubmitting"
          >
            <option disabled selected>Pilih role</option>
            <option v-for="role in roles" :key="role" :value="role" :selected="user.roles === role">{{ role }}</option>
          </select>
        </div>
        
        <div class="user-actions">
          <button 
            class="btn-action btn-primary" 
            @click="$emit('open-permission-modal', user)"
          >
            <i class="bi bi-pencil-square"></i>
            Edit Izin
          </button>
          <form :action="route('admin.users.delete', user.id)" method="POST" class="inline-form">
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" :value="csrf" />
            <button 
              class="btn-action btn-danger" 
              type="submit"
              @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
            >
              <i class="bi bi-trash"></i>
              Hapus
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const props = defineProps({
  users: Array,
  roles: Array,
  csrf: String
})

const searchQuery = ref('')
const filterRole = ref('')
const userStates = reactive({})

const filteredUsers = computed(() => {
  return props.users.filter(user => {
    const matchesSearch = searchQuery.value === '' || 
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (user.phone && user.phone.toLowerCase().includes(searchQuery.value.toLowerCase()))
    
    const matchesRole = filterRole.value === '' || 
      (user.roles.length && user.roles === filterRole.value)
    
    return matchesSearch && matchesRole
  })
})

async function submitRoleChange(e, userId, userName) {
  const selectedRole = e.target.value
  if (!selectedRole) return

  const user = props.users.find(u => u.id === userId)
  const originalRole = user?.roles || ''

  if (!userStates[userId]) {
    userStates[userId] = { isSubmitting: false, submitSuccess: false }
  }

  const result = await Swal.fire({
    title: `Ubah role untuk ${userName}?`,
    text: 'Perubahan ini akan langsung disimpan!',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, ubah!',
    cancelButtonText: 'Batal',
    customClass: {
      confirmButton: 'swal-confirm btn btn-primary',
      cancelButton: 'swal-cancel btn btn-secondary'
    },
    buttonsStyling: false
  })

  if (!result.isConfirmed) {
    e.target.value = originalRole || ''
    return
  }

  userStates[userId].isSubmitting = true
  userStates[userId].submitSuccess = false

  try {
    await axios.post(route('admin.users.assignRole', userId), {
      role: selectedRole,
      _token: props.csrf
    })

    if (user) {
      user.roles = selectedRole
    }

    userStates[userId].submitSuccess = true
    await new Promise(resolve => setTimeout(resolve, 1000))

    Swal.fire({
      title: 'Sukses',
      text: 'Role berhasil diubah!',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false
    })
  } catch (error) {
    console.error('Error mengubah role:', error)
    Swal.fire({
      title: 'Gagal',
      text: 'Gagal mengubah role. Silakan coba lagi.',
      icon: 'error',
      customClass: {
        confirmButton: 'btn btn-primary'
      },
      buttonsStyling: false
    })
    e.target.value = originalRole || ''
  } finally {
    userStates[userId].isSubmitting = false
    await new Promise(resolve => setTimeout(resolve, 3000))
    userStates[userId].submitSuccess = false
  }
}

function confirmDelete(userName, form) {
  Swal.fire({
    title: `Hapus pengguna ${userName}?`,
    text: 'Aksi ini tidak bisa dibatalkan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
    customClass: {
      confirmButton: 'btn btn-danger',
      cancelButton: 'btn btn-secondary'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit()
    }
  })
}
</script>

<style scoped>
/* Base styles */
.search-container {
  max-width: 400px;
}

.custom-select-container {
  max-width: 200px;
  display: inline-block;
}

.select-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

.select-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #3b82f6;
  font-size: 18px;
  pointer-events: none;
  z-index: 2;
}

.custom-select {
  padding-left: 45px !important;
  padding-right: 40px !important;
  background-position: right 14px center !important;
  color: #374151;
  font-weight: 500;
  font-size: 0.9rem;
}

/* Table Container */
.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

/* Table Styles */
.user-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.user-table th {
  background: #f8fafc;
  color: #374151;
  font-weight: 600;
  padding: 16px 12px;
  text-align: left;
  border-bottom: 2px solid #e5e7eb;
  font-size: 0.875rem;
}

.user-table td {
  padding: 14px 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.user-table tbody tr:hover {
  background: #f8fafc;
}

/* Column Widths */
.col-no { width: 60px; text-align: center; }
.col-name { width: 200px; }
.col-email { width: 220px; }
.col-phone { width: 140px; }
.col-role { width: 120px; }
.col-change-role { width: 140px; }
.col-permission { width: 80px; text-align: center; }
.col-action { width: 80px; text-align: center; }

/* User Info */
.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e5e7eb;
  flex-shrink: 0;
}

.user-name {
  font-weight: 500;
  color: #1f2937;
  font-size: 0.875rem;
}

/* Email Text */
.email-text {
  font-size: 0.875rem;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 200px;
  cursor: help;
}

/* Badges */
.badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
}

.role-badge {
  background: #dbeafe;
  color: #1e40af;
}

.no-role-badge {
  background: #f3f4f6;
  color: #6b7280;
}

/* Role Select */
.role-select-container {
  position: relative;
}

.role-select {
  width: 100%;
  padding: 6px 8px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.75rem;
  background: white;
  cursor: pointer;
  color: black;
}

.role-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

/* Loading Overlay */
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
  border-radius: 6px;
  z-index: 10;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.success-icon {
  animation: scaleIn 0.3s ease;
}

/* Buttons */
.btn-icon {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
  transform: translateY(-1px);
}

/* Mobile Styles */
.user-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.user-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.user-details {
  flex: 1;
}

.user-details .user-name {
  font-weight: 600;
  color: #1f2937;
  font-size: 1rem;
  margin-bottom: 4px;
}

.user-email {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 2px;
  word-break: break-all;
}

.user-phone {
  color: #6b7280;
  font-size: 0.875rem;
}

.user-role-section {
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f1f5f9;
}

.role-change-section {
  margin-bottom: 20px;
}

.role-label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.875rem;
}

.role-select-mobile {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.875rem;
  background: white;
  color: black;
}

.user-actions {
  display: flex;
  gap: 12px;
}

.btn-action {
  flex: 1;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.inline-form {
  flex: 1;
}

/* Animations */
@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes scaleIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* Responsive breakpoints */
@media (min-width: 1025px) {
  .d-custom-table-block { display: block !important; }
  .d-custom-table-none { display: none !important; }
}

@media (max-width: 1024px) {
  .d-custom-table-block { display: none !important; }
  .d-custom-table-none { display: block !important; }
}

/* Mobile responsive */
@media (max-width: 576px) {
  .user-card {
    padding: 16px;
  }
  
  .user-header {
    gap: 12px;
  }
  
  .user-avatar {
    width: 32px;
    height: 32px;
  }
  
  .user-actions {
    flex-direction: column;
    gap: 8px;
  }
  
  .btn-action {
    justify-content: center;
  }
  
  .custom-select-container {
    max-width: 100%;
  }
  
  .search-container {
    max-width: 100%;
  }
}

/* SweetAlert custom styles */
.swal-confirm {
  margin-right: 0.5rem !important;
}

.swal-cancel {
  margin-left: 0.5rem !important;
}
</style>