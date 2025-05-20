<template>
  <div class="sidebar">
    <div class="logo-container">
      <img src="/images/logo-Navbar.svg" alt="LaporPak Logo" class="logo">
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link admin-item" href="/admin/dashboard">
          <i class="bi bi-grid-fill me-2"></i>
          Dashboard Admin
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link admin-item" href="/admin/pelaporan">
          <i class="bi bi-file-earmark-excel-fill me-2"></i>
          Manajemen Pelaporan
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link admin-item" href="/admin/top-ups">
          <i class="bi bi-wallet-fill me-2"></i>
          Manajemen Top-up
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link admin-item" href="/admin/users">
          <i class="bi bi-people-fill me-2"></i>
          Manajemen Pengguna
        </a>
      </li>
      <!-- Feedback & Tentang Kami Section can be added here -->
    </ul>
    <div class="bottom-menu">
      <a class="nav-link admin-item danger-item" href="/logout" @click.prevent="logout">
        <i class="bi bi-box-arrow-right me-2"></i>
        Keluar
      </a>
    </div>
  </div>
</template>

<script>
export default {
  components: {
    // NavBar component removed as it's not needed for sidebar
  },
  methods: {
    logout() {
      // Submit a POST request to the logout endpoint
      this.$inertia.post('/logout');
    }
  }
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  height: 100vh;
  background-color: #f8f9fa;
  padding: 1rem 0;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.nav {
  padding: 0;
  margin: 0;
}

.nav-item {
  list-style: none;
}

.admin-item {
  display: flex;
  align-items: center;
  padding: 0.8rem 1.2rem;
  color: #555;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
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

.logo-container {
  padding: 1rem;
  text-align: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.logo {
  max-width: 150px;
  height: auto;
}

.bottom-menu {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid #dee2e6;
}

.danger-item {
  color: #dc3545 !important;
}

.danger-item:hover {
  color: #dc3545 !important;
  background-color: rgba(220, 53, 69, 0.08) !important;
  border-left: 3px solid #dc3545 !important;
}
</style>