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
    <div class="d-none d-custom-table-block table-responsive custom-table-container">
      <table class="table custom-table align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 70px;">No</th>
            <th style="min-width: 120px;">Nama</th>
            <th style="min-width: 160px;">Email</th>
            <th style="min-width: 120px;">Nomor</th>
            <th style="width: 100px;">Role</th>
            <th style="width: 130px;">Ubah Role</th>
            <th style="width: 80px;">Izin</th>
            <th class="text-center" style="width: 70px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in filteredUsers" :key="user.id" class="align-middle">
            <td>{{ index + 1 }}</td>
            <td>
              <div class="d-flex align-items-center">
                <img :src="user.avatar_url" alt="Avatar" class="user-avatar rounded-circle me-2" />
                <span class="fw-medium user-name-text">{{ user.name }}</span>
              </div>
            </td>
            <td>{{ user.email }}</td>
            <td>{{ user.phone || '-' }}</td>
            <td>
              <span v-if="user.roles.length" class="badge role-badge text-primary">{{ user.roles }}</span>
              <span v-else class="badge no-role-badge">Tidak Ada Role</span>
            </td>
            <td class="position-relative">
              <div 
                v-if="userStates[user.id]?.isSubmitting" 
                class="loading-overlay-cell"
              >
                <div class="loading-content">
                  <div v-if="userStates[user.id]?.submitSuccess" class="success-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="3">
                      <polyline points="20,6 9,17 4,12"></polyline>
                    </svg>
                  </div>
                  <div v-else class="loading-spinner-container">
                    <div class="loading-spinner-small"></div>
                    <div class="loading-spinner-glow"></div>
                  </div>
                </div>
              </div>
              <select
                name="role"
                class="form-select form-select-sm custom-role-select"
                @change="(e) => submitRoleChange(e, user.id, user.name)"
                :disabled="userStates[user.id]?.isSubmitting"
                style="max-width: 120px;"
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
            </td>
            <td class="text-center">
              <button
                class="btn btn-sm btn-icon btn-primary"
                type="button"
                @click="$emit('open-permission-modal', user)"
                title="Edit Izin"
              >
                <i class="bi bi-pencil-square"></i>
              </button>
            </td>
            <td class="text-center">
              <form :action="route('admin.users.delete', user.id)" method="POST">
                <input type="hidden" name="_method" value="DELETE" />
                <input type="hidden" name="_token" :value="csrf" />
                <button
                  class="btn btn-sm btn-icon btn-danger"
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

    <!-- Mobile: card layout (≤1024px) -->
    <div class="d-block d-custom-table-none">
      <div v-for="(user, index) in filteredUsers" :key="user.id" class="card mb-3 p-3 shadow-sm">
        <div class="d-flex align-items-center mb-2">
          <img :src="user.avatar_url" alt="Avatar" class="user-avatar rounded-circle me-3" />
          <div>
            <div class="fw-semibold">{{ user.name }}</div>
            <div class="text-muted small">{{ user.email }}</div>
            <div class="text-muted small">{{ user.phone || '-' }}</div>
          </div>
        </div>
        <div class="mb-2">
          <strong>Role: </strong>
          <span v-if="user.roles.length" class="badge role-badge text-primary">{{ user.roles }}</span>
          <span v-else class="badge no-role-badge">Tidak Ada Role</span>
        </div>
        <div class="mb-3">
          <label class="form-label mb-1"><strong>Ubah Role</strong></label>
          <select
            class="form-select form-select-sm"
            @change="(e) => submitRoleChange(e, user.id, user.name)"
            :disabled="userStates[user.id]?.isSubmitting"
          >
            <option disabled selected>Pilih role</option>
            <option v-for="role in roles" :key="role" :value="role" :selected="user.roles === role">{{ role }}</option>
          </select>
        </div>
        <div class="action-buttons">
          <button 
            class="btn btn-primary btn-action" 
            @click="$emit('open-permission-modal', user)"
          >
            <i class="bi bi-pencil-square me-1"></i>
            Edit Izin
          </button>
          <form :action="route('admin.users.delete', user.id)" method="POST" class="d-inline">
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" :value="csrf" />
            <button 
              class="btn btn-danger btn-action" 
              type="submit"
              @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
            >
              <i class="bi bi-trash me-1"></i>
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

// State untuk setiap pengguna (loading/success)
const userStates = reactive({})

// Filtered users based on search and role filter
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

  // Store the current role or default to empty string for reset
  const user = props.users.find(u => u.id === userId)
  const originalRole = user?.roles || ''

  // Inisialisasi state untuk pengguna
  if (!userStates[userId]) {
    userStates[userId] = { isSubmitting: false, submitSuccess: false }
  }

  // Tampilkan konfirmasi SweetAlert
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
    // Reset to original role or default placeholder
    e.target.value = originalRole || ''
    return
  }

  // Mulai animasi loading
  userStates[userId].isSubmitting = true
  userStates[userId].submitSuccess = false

  try {
    await axios.post(route('admin.users.assignRole', userId), {
      role: selectedRole,
      _token: props.csrf
    })

    // Perbarui data pengguna secara lokal
    if (user) {
      user.roles = selectedRole
    }

    // Tampilkan animasi sukses
    userStates[userId].submitSuccess = true
    await new Promise(resolve => setTimeout(resolve, 1000))

    // Tampilkan flash message via SweetAlert
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
    // Reset to original role on error
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
.user-avatar {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border: 2px solid #e5e7eb;
}

.search-container {
  max-width: 400px;
}

.custom-select-container {
  max-width: 200px;
  display: inline-block;
}

/* Select wrapper with icon */
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
  font-size: 0.85rem; /* Smaller text for role filter select */
}

/* Table styles */
.custom-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.custom-table th {
  background: #f8fafc;
  color: #1f2937;
  font-weight: 600;
  padding: 16px;
  border-bottom: 2px solid #e5e7eb;
  white-space: nowrap;
}

.custom-table td {
  padding: 16px;
  vertical-align: middle;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

/* Badge styles */
.role-badge {
  background: #dbeafe;
  color: #1e40af;
  font-weight: 500;
  padding: 4px 10px; /* Reduced padding for smaller appearance */
  border-radius: 12px;
  white-space: nowrap;
  font-size: 0.75rem; /* Smaller text for role badge */
}

.no-role-badge {
  background: #f3f4f6;
  color: #6b7280;
  font-weight: 500;
  padding: 4px 10px; /* Reduced padding for smaller appearance */
  border-radius: 12px;
  white-space: nowrap;
  font-size: 0.75rem; /* Smaller text for no-role badge */
}

/* Role select in table */
.custom-role-select {
  font-size: 0.75rem; /* Smaller text for role select dropdown */
  padding: 4px 8px; /* Adjust padding for smaller appearance */
}

/* Button styles */
.btn-primary {
  background: #3b82f6;
  border: none;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.btn-danger {
  background: #e3342f;
  border: none;
  transition: all 0.2s ease;
}

.btn-danger:hover {
  background: #c53030;
  transform: translateY(-1px);
}

/* Action buttons for mobile */
.action-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.btn-action {
  flex: 1;
  min-width: 120px;
  padding: 8px 12px;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

/* Loading animations */
.loading-overlay-cell {
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

.loading-spinner-container {
  position: relative;
  width: 24px;
  height: 24px;
}

.loading-spinner-small {
  width: 24px;
  height: 24px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-spinner-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
  animation: pulse 2s ease-in-out infinite;
}

.success-icon {
  animation: scaleIn 0.3s ease;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 0.5; }
  50% { transform: scale(1.2); opacity: 0.2; }
}

@keyframes scaleIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* SweetAlert custom styles */
.swal-confirm {
  margin-right: 0.5rem !important;
}

.swal-cancel {
  margin-left: 0.5rem !important;
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

/* Mobile styles (≤576px) */
@media (max-width: 576px) {
  .user-avatar {
    width: 32px;
    height: 32px;
  }
  
  .fw-semibold, .fw-medium {
    font-size: 0.95rem;
  }
  
  .text-muted.small {
    font-size: 0.8rem;
  }
  
  .role-badge, .no-role-badge {
    font-size: 0.65rem; /* Smaller text for mobile */
    padding: 3px 6px;
  }
  
  .custom-select, .custom-role-select, .form-select {
    font-size: 0.75rem; /* Smaller text for selects on mobile */
    padding: 6px 8px;
  }
  
  .btn-action {
    min-width: 100px;
    padding: 8px 10px;
    font-size: 0.8rem;
  }
  
  .action-buttons {
    gap: 6px;
  }
  
  .custom-select-container {
    max-width: 100%;
  }
  
  .search-container {
    max-width: 100%;
  }
}

/* Small tablet (577px - 767px) */
@media (min-width: 577px) and (max-width: 767px) {
  .user-avatar {
    width: 36px;
    height: 36px;
  }
  
  .fw-semibold, .fw-medium {
    font-size: 1rem;
  }
  
  .text-muted.small {
    font-size: 0.85rem;
  }
  
  .role-badge, .no-role-badge {
    font-size: 0.7rem; /* Slightly larger than mobile */
    padding: 4px 8px;
  }
  
  .custom-select, .custom-role-select, .form-select {
    font-size: 0.8rem; /* Slightly larger than mobile */
    padding: 6px 8px;
  }
  
  .btn-action {
    min-width: 110px;
    padding: 8px 12px;
    font-size: 0.85rem;
  }
}

/* Large tablet (768px - 1024px) */
@media (min-width: 768px) and (max-width: 1024px) {
  .user-avatar {
    width: 40px;
    height: 40px;
  }
  
  .fw-semibold, .fw-medium {
    font-size: 1.1rem;
  }
  
  .text-muted.small {
    font-size: 0.9rem;
  }
  
  .role-badge, .no-role-badge {
    font-size: 0.75rem; /* Consistent with desktop */
    padding: 4px 10px;
  }
  
  .custom-select, .custom-role-select, .form-select {
    font-size: 0.85rem; /* Consistent with desktop */
    padding: 6px 10px;
  }
  
  .btn-action {
    min-width: 120px;
    padding: 10px 14px;
    font-size: 0.9rem;
  }
}
</style>