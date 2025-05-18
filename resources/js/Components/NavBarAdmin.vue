<template>
  <NavBar>
    <template #userDropdownItems>
      <li><hr class="dropdown-divider" /></li>
      <div class="admin-dropdown-section">
        <li class="nav-item">
          <a class="dropdown-item admin-item" href="/admin/dashboard">
            <i class="bi bi-grid-fill me-2"></i>
            Dashboard Admin
          </a>
        </li>
        <li class="nav-item">
          <a class="dropdown-item admin-item" href="/admin/pelaporan">
            <i class="bi bi-file-earmark-excel-fill me-2"></i>
            Manajemen Pelaporan
          </a>
        </li>
        <li class="nav-item">
          <a class="dropdown-item admin-item" href="/admin/top-ups">
            <i class="bi bi-wallet-fill me-2"></i>
            Manajemen Top-up
          </a>
        </li>
        <li class="nav-item">
          <a class="dropdown-item admin-item" href="/admin/users">
            <i class="bi bi-people-fill me-2"></i>
            Manajemen Pengguna
          </a>
        </li>
        <!-- Feedback & Tentang Kami Section -->
        
      </div>
    </template>
  </NavBar>
</template>

<script>
import NavBar from '@/Components/NavBar.vue';

export default {
  components: {
    NavBar,
  },
};
</script>

<style scoped>
.admin-dropdown-section {
  background-color: #f8f9fa;
  padding: 0.5rem 0;
  margin: -0.5rem 0;
}

.admin-item {
  padding: 0.8rem 1.2rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  color: #555;
  font-weight: 500;
  border-left: 3px solid transparent;
}

.admin-item:hover {
  color: #0d6efd;
  background-color: rgba(13, 110, 253, 0.08);
  border-left: 3px solid #0d6efd;
  transform: translateX(2px);
}

.admin-item .bi {
  font-size: 16px;
  margin-right: 10px;
  transition: transform 0.2s ease;
}

.admin-item:hover .bi {
  transform: translateX(2px);
}

.admin-item.active {
  color: #0d6efd;
  font-weight: 600;
  background-color: rgba(13, 110, 253, 0.08);
  border-left: 3px solid #0d6efd;
}
</style>