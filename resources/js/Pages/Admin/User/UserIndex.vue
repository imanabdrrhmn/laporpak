<template>
  <AppLayout>
    <Head title="Manajemen Pengguna" />
    <div class="wrapper">
      <div class="container py-5">
        <!-- Modern Page Header with gradient background -->
        <div class="card border-0 shadow-lg mb-5 overflow-hidden">
          <div class="card-body position-relative p-0">
            <div class="gradient-header p-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
              <div class="mb-3 mb-md-0">
                <h2 class="mb-1 fw-bold text-white">Manajemen Pengguna</h2>
                <p class="text-white-50 mb-0">Kelola pengguna dan peran dalam aplikasi Anda</p>
              </div>
              <div class="badge bg-white text-primary fw-bold px-3 py-2 fs-6 shadow-sm flex-shrink-0">
                Total Users: {{ users.length }}
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
      :all-regions="allRegions"
      :selected-permissions="selectedPermissions"
      :allowed-regions="selectedUser.allowed_regions"  
      @update:selected-permissions="selectedPermissions = $event"
      @update:allowed-regions="selectedUser.allowed_regions = $event"
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

const allRegions = ref([])
const selectedRegions = ref([])

const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const showModal = ref(false)
const selectedUser = ref({})
const selectedPermissions = ref([])
const allPermissions = ref([])
const flashMessage = ref(props.flash?.success || '')
const flashError = ref(props.flash?.error || '')

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
    allRegions.value = res.data.allRegions
    selectedPermissions.value = res.data.user.permissions
    selectedRegions.value = res.data.user.allowed_regions || []
    showModal.value = true
  }).catch((error) => {
    console.error('Error fetching permissions:', error)
    flashError.value = 'Gagal memuat izin pengguna. Silakan coba lagi.'
  })
}

function submitPermissions({ userId, permissions, allowed_regions }) {
  axios
    .patch(`/admin/users/${userId}/permissions`, {
      permissions,
      allowed_regions,
      _token: csrf
    })
    .then((response) => {
      const user = users.find(u => u.id === userId)
      if (user) {
        user.permissions = permissions,
        user.allowed_regions = allowed_regions
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
  background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  border-radius: 0.5rem;
  position: relative;
  overflow: hidden;
  padding-top: 1.5rem; 
  padding-bottom: 1.5rem;

  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
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
  overflow-x: auto; /* buat scroll muncul kalau perlu */
  border-radius: 0.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}


.custom-table {
  width: 100%;
  table-layout: fixed; 
  margin-bottom: 0;
}


.custom-table th, .custom-table td {
  padding: 0.75rem 0.75rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.custom-table th:nth-child(1), 
.custom-table td:nth-child(1) {
  width: 30px;
}

.custom-table th:nth-child(2), 
.custom-table td:nth-child(2) {
  max-width: 140px;
}

.custom-table th:nth-child(3), 
.custom-table td:nth-child(3) {
  max-width: 180px;
  white-space: nowrap;
}

.custom-table th:nth-child(4), 
.custom-table td:nth-child(4) {
  width: 110px;
}

.custom-table th:nth-child(5),
.custom-table td:nth-child(5) {
  width: 130px;
}

.custom-table th:nth-child(6),
.custom-table td:nth-child(6) {
  width: 80px;
}

.custom-table th:nth-child(7), 
.custom-table td:nth-child(7) {
  width: 70px;
}

.user-name-text, 
.custom-table td:nth-child(3) {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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

/* Margin antar tombol Swal */
.swal-confirm {
  margin-right: 0.6rem !important;
  padding: 0.5rem 1rem !important;
  border-radius: 0.375rem !important;
  font-weight: 600 !important;
  transition: background-color 0.2s ease;
}

.swal-confirm:hover {
  background-color: #2563eb !important; /* Hover warna lebih gelap */
}

.swal-cancel {
  margin-left: 0.6rem !important;
  padding: 0.5rem 1rem !important;
  border-radius: 0.375rem !important;
  font-weight: 600 !important;
  transition: background-color 0.2s ease;
}

.swal-cancel:hover {
  background-color: #6c757d !important; /* Hover warna lebih gelap */
}

/* Optional: Buat tombol Swal lebih lebar agar lebih mudah diklik */
.swal2-actions button {
  min-width: 100px;
}


/* Animation Extras */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}

/* Responsif umum */
@media (max-width: 767.98px) {
  /* Header teks dan badge */
  .gradient-header h2 {
    font-size: 1.4rem;
  }
  .gradient-header p {
    font-size: 0.9rem;
  }
  .badge.bg-white.text-primary {
    font-size: 0.85rem;
    padding: 0.3rem 0.6rem;
    position: absolute;
    top: 1rem;
    right: 1rem;
  }

  /* Tabs nav scroll horizontal */
  .nav-tabs-modern {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .nav-tabs-modern .nav-item {
    flex: 0 0 auto;
  }

  /* Alerts */
  .custom-alert {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
  }

  /* Card body padding */
  .card-body.p-4 {
    padding: 1rem !important;
  }

  /* Table responsive adjustments */
  .custom-table-container {
    box-shadow: none;
  }
  .custom-table thead th,
  .custom-table tbody td {
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
  }

  /* Avatar kecil */
  .user-avatar {
    width: 32px;
    height: 32px;
    font-size: 1rem;
  }

  /* Role badges */
  .role-badge, .no-role-badge {
    font-size: 0.75rem;
    padding: 0.3rem 0.5rem;
  }

  .btn-delete {
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
  }

  .custom-role-select {
    max-width: 120px;
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
  }
}

@media (max-width: 479.98px) {
  .gradient-header h2 {
    font-size: 1.2rem;
  }
  .gradient-header p {
    font-size: 0.8rem;
  }

  .badge.bg-white.text-primary {
    font-size: 0.75rem;
    top: 0.8rem;
    right: 0.8rem;
    padding: 0.25rem 0.5rem;
  }
}

@media (max-width: 575.98px) {
  .search-container .form-control {
    font-size: 0.9rem;
    padding: 0.4rem 0.6rem;
  }
}

@media (max-width: 375px) {
  .search-container .form-control {
    font-size: 0.85rem;
    padding: 0.35rem 0.5rem;
  }
}

@media (min-width: 576px) {
  .search-container .form-control {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
  }
}

@media (min-width: 992px) {
  .search-container .form-control {
    font-size: 1.1rem;
    padding: 0.6rem 1rem;
  }
}

</style>