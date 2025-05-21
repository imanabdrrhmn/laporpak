<template>
  <AppLayout>
    <Head title="Manajemen Pengguna" />
    <div class="container py-5 overflow-hidden-container">
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

      <!-- Success Alert with improved animation -->
      <div v-if="$page.props.flash.success" class="alert custom-alert shadow-sm border-0 animate__animated animate__fadeInDown">
        <div class="d-flex align-items-center">
          <div class="alert-icon-container me-3">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <div>{{ $page.props.flash.success }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
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
              <!-- Search & Filter Row with improved styling -->
              <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                  <div class="search-container">
                    <div class="input-group">
                      <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search text-primary"></i>
                      </span>
                      <input 
                        type="text" 
                        v-model="searchQuery" 
                        class="form-control form-control-lg border-start-0" 
                        placeholder="Search users..."
                      >
                    </div>
                  </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                  <div class="custom-select-container">
                    <select v-model="filterRole" class="form-select form-select-lg custom-select">
                      <option value="">All Roles</option>
                      <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Users Table with enhanced styling -->
              <div class="table-responsive custom-table-container">
                <table class="table custom-table align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Change Role</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, index) in filteredUsers" :key="user.id" class="align-middle">
                      <td>{{ index + 1 }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <img 
                            :src="user.avatar_url" 
                            alt="Avatar" 
                            class="user-avatar rounded-circle me-2" 
                            style="width: 40px; height: 40px; object-fit: cover;"
                          >

                          <span class="fw-medium">{{ user.name }}</span>
                        </div>
                      </td>
                      <td>{{ user.email }}</td>
                      <td>
                        <span v-if="user.roles.length" class="badge role-badge text-primary">
                          {{ user.roles}}
                        </span>
                        <span v-else class="badge no-role-badge">
                          No Role
                        </span>
                      </td>
                      <td>
                        <form :action="route('admin.users.assignRole', user.id)" method="POST">
                          <input type="hidden" name="_token" :value="csrf" />
                          <select
                            name="role"
                            class="form-select form-select-sm custom-role-select"
                            @change="(e) => submitForm(e, user.name)"
                          >
                            <option disabled selected>Select role</option>
                            <option
                              v-for="role in roles"
                              :key="role"
                              :value="role"
                              :selected="user.roles === role"
                            >
                              {{ role }}
                            </option>
                          </select>
                        </form>
                      </td>
                      <td class="text-center">
                        <form :action="route('admin.users.delete', user.id)" method="POST">
                          <input type="hidden" name="_method" value="DELETE" />
                          <input type="hidden" name="_token" :value="csrf" />
                          <button
                            class="btn btn-delete"
                            @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
                          >
                            <i class="bi bi-trash me-1"></i> Delete
                          </button>
                        </form>
                      </td>
                    </tr>
                    <tr v-if="filteredUsers.length === 0">
                      <td colspan="6" class="text-center py-5">
                        <div class="empty-state">
                          <div class="empty-icon mb-3">
                            <i class="bi bi-search"></i>
                          </div>
                          <h5>No users found</h5>
                          <p class="text-muted">Try adjusting your search or filter criteria</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Roles Tab -->
            <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
              <div class="row">
                <!-- Form Add New Role with improved styling -->
                <div class="col-md-5 mb-4">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header role-card-header">
                      <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Add New Role
                      </h5>
                    </div>
                    <div class="card-body p-4">
                      <form :action="route('admin.roles.store')" method="POST" @submit.prevent="submitNewRole">
                        <input type="hidden" name="_token" :value="csrf" />
                        <div class="mb-4">
                          <label for="newRoleName" class="form-label fw-medium">Role Name</label>
                          <input
                            type="text"
                            id="newRoleName"
                            v-model="newRoleName"
                            class="form-control form-control-lg"
                            placeholder="Enter role name"
                            required
                          />
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">
                          <i class="bi bi-plus-lg me-2"></i>Add Role
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
                
                <!-- Role List with improved styling -->
                <div class="col-md-7 mb-4">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header role-card-header">
                      <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Role List
                      </h5>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table custom-role-table mb-0">
                          <thead>
                            <tr>
                              <th>Role Name</th>
                              <th>Users</th>
                              <th class="text-end">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="role in roles" :key="role">
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="role-icon me-2">
                                    <i class="bi bi-shield-fill"></i>
                                  </div>
                                  <span class="fw-medium">{{ role }}</span>
                                </div>
                              </td>
                              <td>
                                <span class="badge users-count-badge">
                                  {{ getUserCountByRole(role) }}
                                </span>
                              </td>
                              <td class="text-end">
                                <form
                                  :action="route('admin.roles.destroy', role)"
                                  method="POST"
                                  @submit.prevent="confirmDeleteRole(role, $event.target)"
                                >
                                  <input type="hidden" name="_method" value="DELETE" />
                                  <input type="hidden" name="_token" :value="csrf" />
                                  <button class="btn btn-role-delete" type="submit">
                                    <i class="bi bi-trash"></i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                            <tr v-if="roles.length === 0">
                              <td colspan="3" class="text-center py-5">
                                <div class="empty-state">
                                  <div class="empty-icon mb-3">
                                    <i class="bi bi-shield-slash"></i>
                                  </div>
                                  <h5>No roles available</h5>
                                  <p class="text-muted">Create your first role to get started</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import Swal from 'sweetalert2'
import { ref, computed } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue';

const { props } = usePage()
const users = props.users
const roles = props.roles


const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const newRoleName = ref('')
const searchQuery = ref('')
const filterRole = ref('')

// Filtered users based on search and role filter
const filteredUsers = computed(() => {
  return users.filter(user => {
    // Filter by search query
    const matchesSearch = searchQuery.value === '' || 
      user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    // Filter by role
    const matchesRole = filterRole.value === '' || 
      (user.roles.length && user.roles[0].name === filterRole.value)
    
    return matchesSearch && matchesRole
  })
})

// Count users with a specific role
function getUserCountByRole(roleName) {
  return users.filter(user => 
    user.roles.length && user.roles[0].name === roleName
  ).length
}

function submitForm(e, userName) {
  e.preventDefault()
  const form = e.target.form

  Swal.fire({
    title: `Change role for ${userName}?`,
    text: 'This change will be saved immediately!',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, change it!',
    cancelButtonText: 'Cancel',
    customClass: {
      confirmButton: 'btn btn-primary',
      cancelButton: 'btn btn-secondary'
    },
    buttonsStyling: false
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit()
    }
  })
}

function confirmDelete(userName, form) {
  Swal.fire({
    title: `Delete user ${userName}?`,
    text: 'This action cannot be undone!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete!',
    cancelButtonText: 'Cancel',
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

function confirmDeleteRole(role, form) {
  Swal.fire({
    title: `Delete role "${role}"?`,
    text: 'This will affect all users with this role!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete!',
    cancelButtonText: 'Cancel',
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

function submitNewRole() {
  if (!newRoleName.value.trim()) {
    Swal.fire({
      title: 'Error',
      text: 'Role name cannot be empty',
      icon: 'error',
      customClass: {
        confirmButton: 'btn btn-primary'
      },
      buttonsStyling: false
    })
    return
  }
  
  const data = {
    name: newRoleName.value.trim(),
  }
  
  window.axios.post(route('admin.roles.store'), data)
    .then(() => {
      Swal.fire({
        title: 'Success',
        text: 'New role has been created successfully',
        icon: 'success',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      }).then(() => {
        window.location.reload()
      })
    })
    .catch(err => {
      Swal.fire({
        title: 'Failed',
        text: 'Failed to create new role',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      })
      console.error(err)
    })
}
</script>

<style>
.overflow-hidden-container {
  height: 100vh;
  top: 0;

  bottom: 0;
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