<template>
  <AppLayout>
    <Head title="User Management" />
    <div class="container py-6">
      <!-- Page Header with Card styling -->
      <div class="card shadow-sm mb-6 border-0">
        <div class="card-body bg-light rounded-lg">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1 fw-bold text-primary">User Management</h2>
              <p class="text-muted mb-0">Manage users and roles in your application</p>
            </div>
            <div class="badge bg-primary px-3 py-2">
              Total Users: {{ users.length }}
            </div>
          </div>
        </div>
      </div>

      <!-- Success Alert with animation -->
      <div v-if="$page.props.flash.success" class="alert alert-success shadow-sm border-0 animate__animated animate__fadeIn">
        <div class="d-flex align-items-center">
          <i class="bi bi-check-circle-fill me-2"></i>
          {{ $page.props.flash.success }}
        </div>
      </div>

      <!-- User and Role Management in Tabs -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white p-0 border-0">
          <ul class="nav nav-tabs" id="managementTabs" role="tablist">
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
              <!-- Search & Filter Row -->
              <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                      <i class="bi bi-search"></i>
                    </span>
                    <input type="text" v-model="searchQuery" class="form-control bg-light border-start-0" placeholder="Search users...">
                  </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                  <select v-model="filterRole" class="form-select d-inline-block w-auto me-2">
                    <option value="">All Roles</option>
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                  </select>
                </div>
              </div>

              <!-- Users Table with enhanced styling -->
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th class="rounded-start">#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Change Role</th>
                      <th class="rounded-end text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, index) in filteredUsers" :key="user.id" class="align-middle">
                      <td>{{ index + 1 }}</td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            {{ user.name.charAt(0).toUpperCase() }}
                          </div>
                          <span class="fw-medium">{{ user.name }}</span>
                        </div>
                      </td>
                      <td>{{ user.email }}</td>
                      <td>
                        <span v-if="user.roles.length" class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">
                          {{ user.roles[0].name }}
                        </span>
                        <span v-else class="badge bg-soft-secondary text-secondary rounded-pill px-3 py-2">
                          No Role
                        </span>
                      </td>
                      <td>
                        <form :action="route('admin.users.assignRole', user.id)" method="POST">
                          <input type="hidden" name="_token" :value="csrf" />
                          <select
                            name="role"
                            class="form-select form-select-sm border-primary"
                            @change="(e) => submitForm(e, user.name)"
                          >
                            <option disabled selected>Select role</option>
                            <option
                              v-for="role in roles"
                              :key="role"
                              :value="role"
                              :selected="user.roles[0]?.name === role"
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
                            class="btn btn-danger btn-sm rounded-pill px-3"
                            @click.prevent="confirmDelete(user.name, $event.target.closest('form'))"
                          >
                            <i class="bi bi-trash me-1"></i> Delete
                          </button>
                        </form>
                      </td>
                    </tr>
                    <tr v-if="filteredUsers.length === 0">
                      <td colspan="6" class="text-center py-4">
                        <div class="alert alert-info mb-0">
                          <i class="bi bi-info-circle me-2"></i>
                          No users found.
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
                <!-- Form Add New Role -->
                <div class="col-md-5 mb-4">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                      <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Add New Role
                      </h5>
                    </div>
                    <div class="card-body">
                      <form :action="route('admin.roles.store')" method="POST" @submit.prevent="submitNewRole">
                        <input type="hidden" name="_token" :value="csrf" />
                        <div class="mb-3">
                          <label for="newRoleName" class="form-label">Role Name</label>
                          <input
                            type="text"
                            id="newRoleName"
                            v-model="newRoleName"
                            class="form-control"
                            placeholder="Enter role name"
                            required
                          />
                        </div>
                        <button class="btn btn-primary w-100" type="submit">
                          <i class="bi bi-plus-lg me-2"></i>Add Role
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
                
                <!-- Role List -->
                <div class="col-md-7 mb-4">
                  <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                      <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Role List
                      </h5>
                    </div>
                    <div class="card-body p-0">
                      <div class="table-responsive">
                        <table class="table mb-0">
                          <thead class="table-light">
                            <tr>
                              <th>Role Name</th>
                              <th>Users</th>
                              <th class="text-end">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="role in roles" :key="role">
                              <td>
                                <span class="fw-medium">{{ role }}</span>
                              </td>
                              <td>
                                <span class="badge bg-soft-info text-info rounded-pill">
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
                                  <button class="btn btn-outline-danger btn-sm" type="submit">
                                    <i class="bi bi-trash"></i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                            <tr v-if="roles.length === 0">
                              <td colspan="3" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                  <i class="bi bi-info-circle me-2"></i>
                                  No roles have been created yet.
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
import AppLayout from '@/Layouts/AppLayout.vue'

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
  
  window.axios.post(route('roles.store'), data)
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

.bg-soft-primary {
  background-color: rgba(13, 110, 253, 0.15);
}

.bg-soft-secondary {
  background-color: rgba(108, 117, 125, 0.15);
}

.bg-soft-info {
  background-color: rgba(13, 202, 240, 0.15);
}

.py-6 {
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.mb-6 {
  margin-bottom: 4rem;
}

/* Add Bootstrap Icons */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

/* Add animate.css */
@import url("https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css");
</style>