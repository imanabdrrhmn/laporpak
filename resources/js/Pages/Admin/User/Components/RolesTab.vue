<template>
  <div class="row">
    <!-- Role List Desktop (Tabel) -->
    <div class="col-12 col-md-7 mb-4 d-none d-md-block">
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

    <!-- Role List Mobile (Card/List) -->
    <div class="col-12 d-md-none">
      <div v-if="roles.length" class="list-group">
        <div v-for="role in roles" :key="role" class="list-group-item d-flex justify-content-between align-items-center flex-column flex-sm-row">
          <div class="d-flex align-items-center mb-2 mb-sm-0 w-100">
            <div class="role-icon me-3">
              <i class="bi bi-shield-fill"></i>
            </div>
            <div>
              <div class="fw-medium">{{ role }}</div>
              <small class="text-muted">Users: <span class="users-count-badge">{{ getUserCountByRole(role) }}</span></small>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state text-center py-5">
        <div class="empty-icon mb-3">
          <i class="bi bi-shield-slash"></i>
        </div>
        <h5>No roles available</h5>
        <p class="text-muted">Create your first role to get started</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  users: Array,
  roles: Array,
  csrf: String
})

function getUserCountByRole(roleName) {
  return props.users.filter(user => 
    user.roles.length && user.roles === roleName
  ).length
}

</script>

<style scoped>
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

.users-count-badge {
  background-color: rgba(13, 202, 240, 0.15);
  color: #0dcaf0;
  font-weight: 500;
  padding: 0.4rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
}

/* List group item responsive */
.list-group-item {
  border-radius: 0.5rem;
  margin-bottom: 0.75rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  padding: 1rem 1.25rem;
}

.role-card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding: 1rem 1.5rem;
}

.custom-role-table thead th {
  background-color: #f8f9fa;
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

.empty-state {
  padding: 2rem;
  text-align: center;
  color: #6c757d;
}

.empty-icon {
  font-size: 2.5rem;
  opacity: 0.5;
}
</style>
