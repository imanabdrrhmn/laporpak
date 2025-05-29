<template>
  <div class="role-container">
    <div class="role-header">
      <h4>Role List</h4>
    </div>
    
    <!-- Desktop Table -->
    <div class="desktop-view">
      <table class="role-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Users</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in roles" :key="role">
            <td>
              <span class="role-name">{{ role }}</span>
            </td>
            <td>
              <span class="user-count">{{ getUserCountByRole(role) }}</span>
            </td>
          </tr>
          <tr v-if="roles.length === 0">
            <td colspan="2" class="empty-message">
              No roles available
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile List -->
    <div class="mobile-view">
      <div v-if="roles.length" class="role-list">
        <div v-for="role in roles" :key="role" class="role-item">
          <span class="role-name">{{ role }}</span>
          <span class="user-count">{{ getUserCountByRole(role) }} users</span>
        </div>
      </div>
      <div v-else class="empty-message">
        No roles available
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
.role-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden;
}

.role-header {
  background: #f8f9fa;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e9ecef;
}

.role-header h4 {
  margin: 0;
  color: #333;
  font-weight: 600;
}

/* Desktop Table */
.desktop-view {
  display: block;
}

.role-table {
  width: 100%;
  border-collapse: collapse;
}

.role-table th {
  background: #f8f9fa;
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #e9ecef;
}

.role-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f1f1;
}

.role-name {
  font-weight: 500;
  color: #333;
}

.user-count {
  background: #e3f2fd;
  color: #1976d2;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
}

.empty-message {
  text-align: center;
  padding: 2rem;
  color: #666;
  font-style: italic;
}

/* Mobile List */
.mobile-view {
  display: none;
}

.role-list {
  padding: 1rem;
}

.role-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  margin-bottom: 0.5rem;
  background: #f8f9fa;
  border-radius: 6px;
}

/* Responsive */
@media (max-width: 768px) {
  .desktop-view {
    display: none;
  }
  
  .mobile-view {
    display: block;
  }
  
  .role-header {
    padding: 1rem;
  }
  
  .empty-message {
    padding: 2rem 1rem;
  }
}
</style>