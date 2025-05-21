<template>
  <!-- Main Layout Container -->
  <div class="admin-layout">
    <!-- Top Navbar -->
    <div class="top-navbar">
      <div class="navbar-brand">
        <img src="/images/logo-Navbar.svg" alt="LaporPak Logo" class="navbar-logo">
      </div>
      <div class="navbar-actions">
        <div class="user-info">
          <span class="user-greeting">Selamat datang, Admin</span>
        </div>
        <div class="navbar-icons">
          <i class="bi bi-bell"></i>
          <i class="bi bi-gear"></i>
        </div>
      </div>
    </div>
    
    <!-- Sidebar Component -->
    <div class="sidebar">
      <div class="sidebar-section">
        <div class="section-title">MAIN MENU</div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/dashboard">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard Admin</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/pelaporan">
              <i class="bi bi-file-earmark-excel-fill"></i>
              <span>Manajemen Pelaporan</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/top-ups">
              <i class="bi bi-wallet-fill"></i>
              <span>Manajemen Top-up</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/users">
              <i class="bi bi-people-fill"></i>
              <span>Manajemen Pengguna</span>
            </a>
          </li>
        </ul>
      </div>
      
      <div class="sidebar-section">
        <div class="section-title">LAINNYA</div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/feedback">
              <i class="bi bi-chat-left-text-fill"></i>
              <span>Feedback Pengguna</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link admin-item" href="/admin/about">
              <i class="bi bi-info-circle-fill"></i>
              <span>Tentang Aplikasi</span>
            </a>
          </li>
        </ul>
      </div>
      
      <div class="bottom-menu">
        <a class="nav-link admin-item danger-item" href="/logout" @click.prevent="logout">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
      </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      // Submit a POST request to the logout endpoint
      this.$inertia.post('/logout');
    }
  }
};
</script>

<style scoped>
/* Main Layout Styles */
.admin-layout {
  display: flex;
  min-height: 100vh;
  position: fixed;
   z-index: 50
}

/* Top Navbar Styles */
.top-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background-color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
  z-index: 1000;
}

.navbar-brand {
  display: flex;
  align-items: center;
}

.navbar-logo {
  height: 36px;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.user-greeting {
  font-size: 0.9rem;
  font-weight: 500;
  color: #555;
}

.navbar-icons {
  display: flex;
  gap: 1rem;
}

.navbar-icons i {
  font-size: 1.2rem;
  color: #555;
  cursor: pointer;
  transition: color 0.2s ease;
}

.navbar-icons i:hover {
  color: #0d6efd;
}

/* Sidebar Styles */
.sidebar {
  width: 260px;
  height: 100vh;
  background-color: #0d6efd; /* Changed to blue */
  padding-top: 75px;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  border-right: 1px solid rgba(255, 255, 255, 0.1); /* Updated for better contrast */
  z-index: 900;
}

.sidebar-section {
  margin-bottom: 1.5rem;
  padding: 0 0.8rem;
}

.section-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7); /* Changed to semi-transparent white */
  padding: 0.5rem 1rem;
  letter-spacing: 0.5px;
}

.nav {
  padding: 0;
  margin: 0;
}

.nav-item {
  list-style: none;
  margin-bottom: 0.3rem;
}

.admin-item {
  display: flex;
  align-items: center;
  padding: 0.8rem 1rem;
  color: #ffffff; /* Changed to white */
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border-radius: 6px;
}

.admin-item i {
  font-size: 1.1rem;
  margin-right: 12px;
  transition: transform 0.2s ease;
  color: #ffffff; /* Changed to white */
}

.admin-item span {
  font-size: 0.95rem;
  color: #ffffff; /* Changed to white */
}

.admin-item:hover {
  color: #ffffff; /* Keeping text white on hover */
  background-color: rgba(255, 255, 255, 0.2); /* Lighter blue for hover */
  transform: translateX(2px);
}

.admin-item:hover i {
  transform: translateX(2px);
  color: #ffffff; /* Keeping icon white on hover */
}

.admin-item.active {
  color: #ffffff; /* Changed to white */
  font-weight: 600;
  background-color: rgba(255, 255, 255, 0.25); /* Highlighted background for active items */
}

.bottom-menu {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.2); /* Lighter border for contrast */
}

.danger-item {
  color: #dc3545 !important;
  background-color: transparent !important;
}

.danger-item i {
  color: #dc3545 !important;
}

.danger-item span {
  color: #dc3545 !important;
}

.danger-item:hover {
  color: #ffffff !important;
  background-color: #dc3545 !important;
}

.danger-item:hover i,
.danger-item:hover span {
  color: #ffffff !important;
}

/* Main Content Area */
.main-content {
  flex: 1;
  margin-left: 260px;
  padding: 75px 1.5rem 1.5rem;
  background-color: #f5f7fa;
  min-height: 100vh;
}
</style>