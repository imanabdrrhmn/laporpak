<template>
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
</template>

<script setup>
import { ref } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'

const props = defineProps({
  users: Array,
  roles: Array,
  csrf: String
})

const newRoleName = ref('')

// Count users with a specific role
function getUserCountByRole(roleName) {
  return props.users.filter(user => 
    user.roles.length && user.roles === roleName
  ).length
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
  
  axios.post(route('admin.roles.store'), data)
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
</script>
