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
                <th class="col-change-role">Kelola Role</th>
                <th class="col-permission">Izin</th>
                <th class="col-action">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="paginatedUsers.length === 0">
                <td colspan="8" class="text-center py-4 text-gray-500">
                  Tidak ada data pengguna yang ditemukan
                </td>
              </tr>
              <tr v-else v-for="(user, index) in paginatedUsers" :key="user.id">
                <td class="col-no">{{ ((currentPage - 1) * ITEMS_PER_PAGE) + index + 1}}</td>
                <td class="col-name">
                  <div class="user-info">
                    <img :src="user.avatar_url" alt="Avatar" class="user-avatar" />
                    <span class="user-name">{{ user.name }}</span>
                  </div>
                </td>
                <td class="col-email">
                  <div class="email-text" :title="user.email">{{ user.email || '-' }}</div>
                </td>
                <td class="col-phone">
                  <div class="phone-text">{{ user.nomor || '-' }}</div>
                </td>
                <td class="col-role">
                  <span v-if="user.roles.length" class="badge role-badge">{{ user.roles }}</span>
                  <span v-else class="badge no-role-badge">Tidak Ada Role</span>
                </td>
                <td class="col-change-role">
                  <button
                    class="btn-change-role"
                    @click="openChangeRoleModal(user)"
                    title="Kelola Role Pengguna"
                    :disabled="userStates[user.id]?.isSubmitting"
                  >
                    <i class="bi bi-shield-check me-2"></i>
                    <span class="btn-text">Kelola Role</span>
                    <div v-if="userStates[user.id]?.isSubmitting" class="spinner-border spinner-sm ms-2" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
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

        <!-- Pagination -->
        <div class="pagination-container p-4">
          <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
              Menampilkan {{ paginationInfo.from }}-{{ paginationInfo.to }} dari {{ paginationInfo.total }} data
            </div>
            <nav aria-label="Page navigation">
              <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                </li>
                <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                  <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                  <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile & iPad: card layout -->
    <div class="mobile-card-container">
      <div v-if="paginatedUsers.length === 0" class="no-data-card">
        <i class="bi bi-inbox text-gray-400 mb-2"></i>
        <p>Tidak ada data pengguna yang ditemukan</p>
      </div>
      <div v-else v-for="(user, index) in paginatedUsers" :key="user.id" class="user-card">
        <div class="user-header">
          <img :src="user.avatar_url" alt="Avatar" class="user-avatar" />
          <div class="user-details">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-email">{{ user.email }}</div>
            <div class="user-phone">{{ user.nomor || '-' }}</div>
          </div>
        </div>
        
        <div class="user-role-section">
          <strong>Role: </strong>
          <span v-if="user.roles.length" class="badge role-badge">{{ user.roles }}</span>
          <span v-else class="badge no-role-badge">Tidak Ada Role</span>
        </div>
        
        <div class="role-change-section">
          <button 
            class="btn-action-mobile btn-primary-mobile w-100" 
            @click="openChangeRoleModal(user)"
            :disabled="userStates[user.id]?.isSubmitting"
          >
            <i class="bi bi-shield-check me-2"></i>
            <span>Kelola Role Pengguna</span>
            <div v-if="userStates[user.id]?.isSubmitting" class="spinner-border spinner-sm ms-2" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </button>
        </div>
        
        <div class="user-actions">
          <button 
            class="btn-action btn-primary" 
            @click="$emit('open-permission-modal', user)"
          >
            <i class="bi bi-pencil-square me-2"></i>
            Edit Izin
          </button>
          <form :action="route('admin.users.delete', user.id)" method="POST" class="inline-form">
            <input type="hidden" name="_method" value="DELETE" />
            <input type="hidden" name="_token" :value="csrf" />
            <button 
              class="btn-action btn-danger" 
              type="button"
              @click="openDeleteModal(user.name, user.id)"
            >
              <i class="bi bi-trash me-2"></i>
              Hapus
            </button>
          </form>
        </div>
      </div>

      <!-- Pagination for mobile & iPad -->
      <div class="pagination-container p-4 bg-white rounded-lg shadow-sm mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div class="pagination-info">
            Menampilkan {{ paginationInfo.from }}-{{ paginationInfo.to }} dari {{ paginationInfo.total }} data
          </div>
          <nav aria-label="Page navigation">
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">
                  <i class="bi bi-chevron-left"></i>
                </button>
              </li>
              <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                <button class="page-link" @click="goToPage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">
                  <i class="bi bi-chevron-right"></i>
                </button>
              </li>
            </ul>
          </nav>
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
const currentPage = ref(1)
const ITEMS_PER_PAGE = 10; 

const filteredUsers = computed(() => {
  return props.users.filter(user => {
    const query = searchQuery.value.toLowerCase();
    const matchesSearch = searchQuery.value === '' || 
      user.name?.toLowerCase().includes(query) ||
      user.email?.toLowerCase().includes(query) ||
      user.phone?.toLowerCase().includes(query);

    const matchesRole = filterRole.value === '' || 
      (user.roles.length && user.roles === filterRole.value);

    return matchesSearch && matchesRole;
  });
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE;
  const end = start + ITEMS_PER_PAGE;
  return filteredUsers.value.slice(start, Math.min(end, start + 10)); // Ensure max 10 items
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / ITEMS_PER_PAGE))

const paginationInfo = computed(() => ({
  from: filteredUsers.value.length ? (currentPage.value - 1) * ITEMS_PER_PAGE + 1 : 0,
  to: Math.min(currentPage.value * ITEMS_PER_PAGE, filteredUsers.value.length),
  total: filteredUsers.value.length
}))

const displayedPages = computed(() => {
  const maxVisiblePages = 5
  const halfVisible = Math.floor(maxVisiblePages / 2)
  let startPage = Math.max(currentPage.value - halfVisible, 1)
  let endPage = startPage + maxVisiblePages - 1

  if (endPage > totalPages.value) {
    endPage = totalPages.value
    startPage = Math.max(endPage - maxVisiblePages + 1, 1)
  }

  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)
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

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}
</script>

<style scoped>
.search-container {
  max-width: 400px;
  position: relative;
}

.custom-search-field {
  position: relative;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: border-color 0.2s ease;
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
}

/* ...existing styles remain unchanged */
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

/* Updated column widths - made phone column wider */
.col-no { width: 5%; text-align: center; min-width: 50px; }
.col-name { width: 16%; min-width: 150px; }
.col-email { width: 20%; min-width: 180px; }
.col-phone { width: 14%; min-width: 130px; } /* Increased from 10% to 14% */
.col-role { width: 10%; min-width: 80px; }
.col-change-role { width: 16%; min-width: 140px; }
.col-permission { width: 8%; text-align: center; min-width: 60px; }
.col-action { width: 8%; text-align: center; min-width: 60px; }

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

/* New phone text styling */
.phone-text {
  font-size: 0.875rem;
  color: #374151;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: left;
}

/* Enhanced Change Role Button Styles */
.btn-change-role {
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  min-width: 120px;
  position: relative;
  overflow: hidden;
}

.btn-change-role:hover {
  background: #0b5ed7;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-change-role:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(13, 110, 253, 0.2);
}

.btn-change-role:disabled {
  background: #6c757d;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-change-role .btn-text {
  white-space: nowrap;
}

.btn-change-role::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.btn-change-role:hover::before {
  left: 100%;
}

/* Mobile Button Styles */
.btn-primary-mobile {
  background: #0d6efd;
  color: white;
}

.btn-primary-mobile:hover {
  background: #0b5ed7;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
}

.btn-primary-mobile:disabled {
  background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.pagination-container {
  background: #fff;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination {
  gap: 0.25rem;
}

.page-link {
  border: none;
  padding: 0.5rem 0.75rem;
  color: #374151;
  background: transparent;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  transition: all 0.2s ease;
}

.page-link:hover:not(.disabled) {
  background: #f3f4f6;
  color: #1f2937;
}

.page-item.active .page-link {
  background: #2563eb;
  color: white;
}

.page-item.disabled .page-link {
  color: #9ca3af;
  pointer-events: none;
}

@media (max-width: 1200px) {
  .email-text {
    white-space: normal;
    word-break: break-word;
    line-height: 1.3;
  }
  
  .phone-text {
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
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 16px;
  width: 100%;
  overflow: hidden; /* Add overflow control */
}

.user-details {
  flex: 1;
  min-width: 0; /* Add this to enable text truncation */
}

.user-details .user-name {
  font-weight: 600;
  color: #1f2937;
  font-size: 1rem;
  margin-bottom: 4px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-details .user-email,
.user-details .user-phone {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 2px;
  overflow: hidden;
  text-overflow: ellipsis;
  word-wrap: break-word;
  word-break: break-all;
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

.spinner-border {
  width: 1rem;
  height: 1rem;
  border-width: 0.1em;
}

.spinner-sm {
  width: 0.875rem;
  height: 0.875rem;
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
}

@media (max-width: 1199px) {
  .desktop-table-container { display: none; }
  .mobile-card-container { display: block; }
  
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
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
  }

  .user-avatar {
    width: 48px;
    height: 48px;
  }

  .user-details {
    flex: 1;
  }

  .user-name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .user-email, .user-phone {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 2px;
  }

  .user-role-section {
    margin-bottom: 16px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
  }

  .role-change-section {
    margin-bottom: 20px;
  }

  .btn-action-mobile {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .user-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .btn-action {
    padding: 12px;
    width: 100%;
  }

  .inline-form {
    width: 100%;
  }
}

/* Additional iPad-specific refinements */
@media (min-width: 768px) and (max-width: 1199px) {
  .user-card {
    padding: 24px;
  }

  .user-avatar {
    width: 56px;
    height: 56px;
  }

  .user-actions {
    grid-template-columns: repeat(2, 1fr);
  }

  .pagination-container {
    padding: 20px;
  }
}

/* Mobile-specific refinements */
@media (max-width: 767px) {
  .user-card {
    padding: 16px;
  }

  .user-actions {
    grid-template-columns: 1fr;
  }

  .pagination-container {
    padding: 16px;
  }
  
  /* Add these new styles */
  .btn-action-mobile {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;  /* Reduce gap between icon and text */
    width: 100%;
    padding: 12px;
  }

  .btn-action-mobile i {
    margin-right: 4px; /* Add small margin to icon */
    flex-shrink: 0; /* Prevent icon from shrinking */
  }

  .btn-action-mobile span {
    flex-shrink: 0; /* Prevent text from wrapping/shrinking */
    font-size: 0.875rem; /* Slightly reduce font size */
  }
}

/* Add specific iPhone SE handling */
@media (max-width: 375px) {
  .btn-action-mobile {
    gap: 4px; /* Further reduce gap for very small screens */
    padding: 12px 8px; /* Reduce horizontal padding */
  }
  
  .btn-action-mobile span {
    font-size: 0.8rem; /* Further reduce font size */
  }
}
</style>