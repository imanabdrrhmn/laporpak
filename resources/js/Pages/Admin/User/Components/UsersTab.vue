<template>
  <div>
    <!-- Search & Filter Row (tetap tampil di semua layar) -->
    <div class="row mb-4 align-items-center">
      <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="search-container">
          <div class="custom-search-field">
            <input 
              type="text" 
              v-model="searchQuery" 
              class="search-input" 
              placeholder="Cari pengguna..."
            >
            <i class="bi bi-search search-icon"></i>
            <div class="search-highlight"></div>
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

    <!-- Desktop: tabel (responsive dengan zoom) -->
    <div class="desktop-table-container">
      <div class="table-container">
        <div class="table-responsive">
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
              <tr v-if="filteredUsers.length === 0">
                <td colspan="8" class="text-center py-4 text-gray-500">
                  Tidak ada data pengguna yang ditemukan
                </td>
              </tr>
              <tr v-else v-for="(user, index) in filteredUsers" :key="user.id">
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
                  <button
                    class="btn-icon btn-primary"
                    @click="openChangeRoleModal(user)"
                    title="Ubah Role"
                    :disabled="userStates[user.id]?.isSubmitting"
                  >
                    <i class="bi bi-shield-check"></i>
                  </button>
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
                  <form 
                    :id="'delete-form-' + user.id" 
                    :action="route('admin.users.delete', user.id)" 
                    method="POST"
                  >
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" name="_token" :value="csrf" />
                    <button
                      class="btn-icon btn-danger"
                      type="button"
                      @click="openDeleteModal(user.name, user.id)"
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
    </div>

    <!-- Mobile: card layout -->
    <div class="mobile-card-container">
      <div v-if="filteredUsers.length === 0" class="no-data-card">
        <i class="bi bi-inbox text-gray-400 mb-2"></i>
        <p>Tidak ada data pengguna yang ditemukan</p>
      </div>
      <div v-else v-for="(user, index) in filteredUsers" :key="user.id" class="user-card">
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
          <button 
            class="btn-action btn-primary" 
            @click="openChangeRoleModal(user)"
            :disabled="userStates[user.id]?.isSubmitting"
          >
            <i class="bi bi-shield-check"></i>
            Ubah Role
          </button>
        </div>
        
        <div class="user-actions">
          <button 
            class="btn-action btn-primary" 
            @click="$emit('open-permission-modal', user)"
          >
            <i class="bi bi-pencil-square"></i>
            Edit Izin
          </button>
          <form :action="route('admin.users.delete', user.id)" method="POST" :ref="'form-' + user.id" class="inline-form">
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" :value="csrf" />
            <button 
              class="btn-action btn-danger" 
              type="button"
              @click="openDeleteModal(user.name, user.id)"
            >
              <i class="bi bi-trash"></i>
              Hapus
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmDeleteModal
      :isOpen="showDeleteModal"
      :userName="selectedUserName"
      @close="showDeleteModal = false"
      @confirm="handleDeleteConfirm"
    />

    <!-- Change Role Modal -->
    <ChangeRoleModal
      :isOpen="showChangeRoleModal"
      :user="selectedUser"
      :roles="roles"
      :csrf="csrf"
      @close="closeChangeRoleModal"
      @role-changed="handleRoleChange"
    />
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import axios from 'axios'
import ConfirmDeleteModal from './ConfirmDeleteModal.vue'
import ChangeRoleModal from './ChangeRoleModal.vue'
import Swal from 'sweetalert2';

const props = defineProps({
  users: Array,
  roles: Array,
  csrf: String
})

const searchQuery = ref('')
const filterRole = ref('')
const userStates = reactive({})
const showDeleteModal = ref(false)
const selectedUserName = ref('')
const selectedFormId = ref(null)
const showChangeRoleModal = ref(false)
const selectedUser = ref(null)

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

function openChangeRoleModal(user) {
  selectedUser.value = user
  showChangeRoleModal.value = true
}

function closeChangeRoleModal() {
  showChangeRoleModal.value = false
  selectedUser.value = null
}

async function handleRoleChange({ userId, selectedRole, userName }) {
  if (!selectedRole) return

  const user = props.users.find(u => u.id === userId)
  const originalRole = user?.roles || ''

  if (!userStates[userId]) {
    userStates[userId] = { isSubmitting: false, submitSuccess: false }
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
    if (user) {
      user.roles = originalRole
    }
  } finally {
    userStates[userId].isSubmitting = false
    await new Promise(resolve => setTimeout(resolve, 3000))
    userStates[userId].submitSuccess = false
  }

  closeChangeRoleModal()
}

function openDeleteModal(userName, userId) {
  selectedUserName.value = userName
  selectedFormId.value = userId
  showDeleteModal.value = true
}

function handleDeleteConfirm() {
  if (selectedFormId.value) {
    const formId = `delete-form-${selectedFormId.value}`
    const form = document.getElementById(formId)
    if (form) {
      form.submit()
    }
  }
  showDeleteModal.value = false
  selectedFormId.value = null
  selectedUserName.value = ''
}
</script>

<style scoped>
/* Existing styles remain unchanged */
.search-container {
  max-width: 400px;
  position: relative;
}

.custom-search-field {
  position: relative;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 2px solid #e5e7eb;
}

.custom-search-field:hover {
  border-color: #d1d5db;
}

.custom-search-field:focus-within {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-input {
  width: 100%;
  padding: 12px 45px 12px 20px;
  border: none;
  outline: none;
  font-size: 0.95rem;
  color: #374151;
  background: transparent;
}

.search-input::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

.search-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #3b82f6;
  font-size: 16px;
  pointer-events: none;
  transition: all 0.3s ease;
}

.custom-search-field:focus-within .search-icon {
  color: #2563eb;
  transform: translateY(-50%) scale(1.1);
}

.search-highlight {
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: #3b82f6;
  transition: all 0.3s ease;
  transform: translateX(-50%);
}

.custom-search-field:focus-within .search-highlight {
  width: 100%;
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

.desktop-table-container {
  display: block;
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.user-table th {
  background: #f8fafc;
  color: #374151;
  font-weight: 600;
  padding: 18px;
  text-align: left;
  border-bottom: 2px solid #e5e7eb;
  font-size: 0.875rem;
  white-space: nowrap;
}

.user-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.875rem;
}

.user-table tbody tr:hover {
  background: #f8fafc;
}

.col-no { width: 5%; text-align: center; min-width: 50px; }
.col-name { width: 20%; min-width: 150px; }
.col-email { width: 25%; min-width: 180px; }
.col-phone { width: 12%; min-width: 100px; }
.col-role { width: 10%; min-width: 80px; }
.col-change-role { width: 15%; min-width: 120px; }
.col-permission { width: 7%; text-align: center; min-width: 60px; }
.col-action { width: 6%; text-align: center; min-width: 60px; }

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  background: white;
  flex-shrink: 0;
}

.user-name {
  font-weight: 500;
  color: #1f2937;
  font-size: 0.875rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.email-text {
  font-size: 0.8rem;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 100%;
  cursor: help;
}

@media (max-width: 1200px) {
  .email-text {
    white-space: normal;
    word-break: break-word;
    line-height: 1.3;
  }
}

.badge {
  padding: 4px 8px;
  border-radius: 8px;
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

.btn-icon {
  width: 36px;
  height: 36px;
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
  background: #0d6efd;
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

.mobile-card-container {
  display: none;
}

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

.no-data-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  color: #6b7280;
}

.no-data-card i {
  font-size: 2rem;
  display: block;
}

.no-data-card p {
  margin: 0;
  font-size: 0.875rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes scaleIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

@media (min-width: 1200px) {
  .desktop-table-container { display: block; }
  .mobile-card-container { display: none; }
  
  .user-table th { padding: 16px 12px; font-size: 0.875rem; }
  .user-table td { padding: 14px 12px; font-size: 0.875rem; }
  .user-avatar { width: 40px; height: 40px; }
  .user-name { font-size: 0.875rem; }
  .email-text { font-size: 0.875rem; }
  .btn-icon { width: 36px; height: 36px; font-size: 0.875rem; }
}

@media (max-width: 1199px) and (min-width: 900px) {
  .desktop-table-container { display: block; }
  .mobile-card-container { display: none; }
  
  .user-table th { padding: 14px 8px; font-size: 0.8rem; }
  .user-table td { padding: 12px 8px; font-size: 0.8rem; }
  .user-avatar { width: 36px; height: 36px; }
  .user-name { font-size: 0.8rem; }
  .email-text { font-size: 0.75rem; }
  .btn-icon { width: 32px; height: 32px; font-size: 0.75rem; }
  
  .col-name { width: 18%; }
  .col-email { width: 27%; }
  .col-phone { width: 10%; }
  .col-change-role { width: 17%; }
}

@media (max-width: 899px) and (min-width: 768px) {
  .desktop-table-container { display: block; }
  .mobile-card-container { display: none; }
  
  .user-table th { padding: 12px 6px; font-size: 0.75rem; }
  .user-table td { padding: 10px 6px; font-size: 0.75rem; }
  .user-avatar { width: 32px; height: 32px; }
  .user-name { font-size: 0.75rem; }
  .email-text { font-size: 0.7rem; }
  .btn-icon { width: 28px; height: 28px; font-size: 0.7rem; }
  
  .col-phone { display: none; }
  .col-name { width: 22%; }
  .col-email { width: 30%; }
  .col-role { width: 12%; }
  .col-change-role { width: 18%; }
}

@media (max-width: 767px) {
  .desktop-table-container { display: none; }
  .mobile-card-container { display: block; }
  
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
</style>