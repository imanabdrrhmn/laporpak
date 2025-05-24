<template>
  <AppLayout>
    <Head title="Manajemen Pengguna" />
    <div class="wrapper">
      <div class="container py-5">
        <!-- Modern Page Header with gradient background -->
        <div class="card border-0 shadow-lg mb-5 overflow-hidden">
          <div class="card-body position-relative p-0">
            <div class="-mt-4 gradient-header p-4">
              <div class="d-flex justify-content-between align-items-center position-relative z-10">
                <div>
                  <h2 class="mb-1 fw-bold text-white">Manajemen Pengguna</h2>
                  <p class="text-white-50 mb-0">Kelola pengguna dan peran dalam aplikasi Anda</p>
                </div>
                <div class="badge bg-white text-primary fw-bold px-3 py-2 fs-6 shadow-sm">
                  Total Users: {{ users.length }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Alert -->
        <div v-if="flashMessage" class="alert custom-alert shadow-sm border-0 animate__animated animate__fadeInDown">
          <div class="d-flex align-items-center">
            <div class="alert-icon-container me-3">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>{{ flashMessage }}</div>
            <button type="button" class="btn-close ms-auto" @click="flashMessage = ''" aria-label="Close"></button>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="flashError" class="alert custom-alert shadow-sm border-0 animate__animated animate__fadeInDown alert-error">
          <div class="d-flex align-items-center">
            <div class="alert-icon-container me-3 bg-danger">
              <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>{{ flashError }}</div>
            <button type="button" class="btn-close ms-auto" @click="flashError = ''" aria-label="Close"></button>
          </div>
        </div>

        <!-- User and Role Management in Tabs -->
        <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
          <div class="card-header bg-white p-0 border-0">
            <ul class="nav nav-pills nav-tabs-modern" id="managementTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-3" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">
                  <i class="bi bi-people-fill me-2"></i>Users
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-3" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="false">
                  <i class="bi bi-shield-lock-fill me-2"></i>Roles
                </button>
              </li>
            </ul>
          </div>
          
          <div class="card-body p-4">
            <div class="tab-content" id="managementTabsContent">
              <!-- Users Tab -->
              <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                <UsersTab 
                  :users="users" 
                  :roles="roles" 
                  :csrf="csrf" 
                  @open-permission-modal="openPermissionModal"
                />
              </div>
              <!-- Roles Tab -->
              <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                <RolesTab 
                  :users="users" 
                  :roles="roles" 
                  :csrf="csrf" 
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Permission Modal -->
    <PermissionModal 
      v-if="showModal" 
      :user="selectedUser" 
      :all-permissions="allPermissions" 
      :selected-permissions="selectedPermissions"
      @update:selected-permissions="selectedPermissions = $event"
      @close="showModal = false"
      @submit="submitPermissions"
    />
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import UsersTab from './Components/UsersTab.vue'
import RolesTab from './Components/RolesTab.vue'
import PermissionModal from './Components/PermissionModal.vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const { props } = usePage()
const users = props.users
const roles = props.roles

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const showModal = ref(false)
const selectedUser = ref({})
const selectedPermissions = ref([])
const allPermissions = ref([])
const flashMessage = ref(props.flash?.success || '')
const flashError = ref(props.flash?.error || '')

// Auto-dismiss flash messages after 5 seconds
watch(() => props.flash?.success, (newMessage) => {
  if (newMessage) {
    flashMessage.value = newMessage
    setTimeout(() => {
      flashMessage.value = ''
    }, 5000)
  }
})

watch(() => props.flash?.error, (newError) => {
  if (newError) {
    flashError.value = newError
    setTimeout(() => {
      flashError.value = ''
    }, 5000)
  }
})

function openPermissionModal(user) {
  axios.get(`/admin/users/${user.id}/permissions`).then((res) => {
    selectedUser.value = res.data.user
    allPermissions.value = res.data.allPermissions
    selectedPermissions.value = res.data.user.permissions
    showModal.value = true
  }).catch((error) => {
    console.error('Error fetching permissions:', error)
    flashError.value = 'Gagal memuat izin pengguna. Silakan coba lagi.'
  })
}

function submitPermissions({ userId, permissions }) {
  axios
    .patch(`/admin/users/${userId}/permissions`, {
      permissions,
      _token: csrf
    })
    .then((response) => {
      // Tidak menutup modal otomatis
      // Hapus SweetAlert sukses, gunakan animasi checkmark di PermissionModal.vue
      // Perbarui permissions pengguna secara lokal
      const user = users.find(u => u.id === userId)
      if (user) {
        user.permissions = permissions
      }
    })
    .catch((error) => {
      console.error('Error updating permissions:', error)
      Swal.fire({
        title: 'Gagal',
        text: error.response?.data?.message || 'Gagal memperbarui izin. Silakan coba lagi.',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      })
    })
}
</script>

<style>
.wrapper {
  min-height: 100vh;
  background-color: #f8f9fc;
  margin-top: -1rem;
}

.gradient-header {
  padding-top: 24px;
  background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  position: relative;
  overflow: hidden;
  border-radius: 0.5rem;
}

.gradient-header h2 {
  font-size: 1.8rem;
}

.gradient-header::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 60%);
  z-index: 1;
}

/* Custom Alert Styling */
.custom-alert {
  background-color: #d1e7dd;
  border-left: 4px solid #198754;
  color: #0f5132;
  border-radius: 0.5rem;
}

.alert-error {
  background-color: #fee2e2;
  border-left: 4px solid #dc2626;
  color: #991b1b;
}

.alert-icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #198754;
  color: white;
}

.alert-error .alert-icon-container {
  background-color: #dc2626;
}

/* Modern Nav Tabs */
.nav-tabs-modern {
  border-bottom: 1px solid #e9ecef;
  gap: 0.5rem;
  padding: 0.5rem 1rem 0;
}

.nav-tabs-modern .nav-link {
  border: none;
  border-radius: 0.5rem 0.5rem 0 0;
  color: #6c757d;
  font-weight: 500;
  transition: all 0.2s ease;
}

.nav-tabs-modern .nav-link:hover {
  color: #4e73df;
  background-color: rgba(78, 115, 223, 0.05);
}

.nav-tabs-modern .nav-link.active {
  color: #4e73df;
  background-color: transparent;
  border-bottom: 3px solid #4e73df;
}

/* Search Container */
.search-container .input-group {
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  border-radius: 0.5rem;
  overflow: hidden;
}

.search-container .form-control {
  border-color: #e9ecef;
  transition: all 0.2s ease;
}

.search-container .form-control:focus {
  box-shadow: none;
  border-color: #4e73df;
}

.search-container .input-group-text {
  border-color: #e9ecef;
}

/* Custom Select */
.custom-select-container {
  display: inline-block;
  position: relative;
}

.custom-select {
  padding-right: 2.5rem;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234e73df' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 0.85rem;
  border-color: #e9ecef;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.custom-select:focus {
  border-color: #4e73df;
  box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
}

/* Custom Table */
.custom-table-container {
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.custom-table {
  margin-bottom: 0;
}

.custom-table thead {
  background-color: #f8f9fa;
}

.custom-table thead th {
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #e9ecef;
  padding: 1rem;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.custom-table tbody td {
  padding: 1rem;
  border-color: #f8f9fa;
  vertical-align: middle;
}

.custom-table tbody tr {
  transition: all 0.2s ease;
}

.custom-table tbody tr:hover {
  background-color: rgba(78, 115, 223, 0.05);
}

/* User Avatar */
.user-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.2rem;
  box-shadow: 0 2px 10px rgba(78, 115, 223, 0.25);
}

/* Role Badges */
.role-badge {
  background-color: rgba(78, 115, 223, 0.15);
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
}

.no-role-badge {
  background-color: rgba(108, 117, 125, 0.15);
  color: #6c757d;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
}

/* Custom Role Select */
.custom-role-select {
  border-color: #4e73df;
  font-weight: 500;
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.custom-role-select:focus {
  box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
}

/* Delete Button */
.btn-delete {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
  border: none;
  border-radius: 1rem;
  padding: 0.375rem 1rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-delete:hover {
  background-color: #dc3545;
  color: white;
}

/* Empty State */
.empty-state {
  padding: 2rem;
  text-align: center;
}

.empty-icon {
  font-size: 2.5rem;
  color: #6c757d;
  opacity: 0.5;
}

/* Role Card Header */
.role-card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding: 1rem 1.5rem;
}

/* Role Icon */
.role-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: rgba(78, 115, 223, 0.15);
  color: #4e73df;
}

/* Users Count Badge */
.users-count-badge {
  background-color: rgba(13, 202, 240, 0.15);
  color: #0dcaf0;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
}

/* Role Delete Button */
.btn-role-delete {
  background-color: transparent;
  color: #dc3545;
  border: 1px solid rgba(220, 53, 69, 0.25);
  border-radius: 0.375rem;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-role-delete:hover {
  background-color: rgba(220, 53, 69, 0.1);
}

/* Custom Role Table */
.custom-role-table thead {
  background-color: #f8f9fa;
}

.custom-role-table thead th {
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #e9ecef;
  padding: 1rem 1.5rem;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.custom-role-table tbody td {
  padding: 1rem 1.5rem;
  border-color: #f8f9fa;
}

/* Animation Extras */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}
</style>