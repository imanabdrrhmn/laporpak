<template>
  <div class="admin-layout">
    <!-- Top Navbar -->
    <div class="top-navbar">
      <div class="navbar-brand">
        <img src="/images/logo-Navbar.svg" alt="LaporPak Logo" class="navbar-logo">
      </div>

      <div class="navbar-right">
        <div class="user-info d-none d-md-block">
          <span class="user-greeting">Selamat datang, {{ user.name }}</span>
        </div>
        <button class="hamburger-btn d-md-none" @click="toggleSidebar">
          <i class="bi bi-list"></i>
        </button>
        <div class="navbar-icons d-none d-md-flex">
          <img v-if="user.avatar_url" :src="user.avatar_url" alt="User Avatar" class="avatar-image">
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-flex" ref="sidebar">
      <div class="sidebar-section">
        <div class="section-title">MAIN MENU</div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <Link class="nav-link admin-item" :class="{ 'active-item': $page.url === '/admin/dashboard' }" href="/admin/dashboard">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard Admin</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link class="nav-link admin-item" :class="{ 'active-item': $page.url === '/admin/pelaporan' }" href="/admin/pelaporan">
              <i class="bi bi-file-earmark-excel-fill"></i>
              <span>Manajemen Pelaporan</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link class="nav-link admin-item" :class="{ 'active-item': $page.url === '/admin/top-ups' }" href="/admin/top-ups">
              <i class ="bi bi-wallet-fill"></i>
              <span>Manajemen Top-up</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link class="nav-link admin-item" :class="{ 'active-item': $page.url === '/admin/users' }" href="/admin/users">
              <i class="bi bi-people-fill"></i>
              <span>Manajemen Pengguna</span>
            </Link>
          </li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="section-title">LAINNYA</div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link admin-item" href="/dashboard" target="_blank">
              <i class="bi bi-arrow-up-right-circle-fill"></i>
              <span>Halaman User</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="bottom-menu">
        <a class="nav-link admin-item danger-item" href="#" @click.prevent="logout">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)
const user = computed(() => page.props.auth?.user || {})
const isAdmin = computed(() => page.props.auth?.isAdmin)

// Sidebar toggle logic
const sidebar = ref(null)
const toggleSidebar = () => {
  sidebar.value.classList.toggle('d-none')
}

// Logout logic
const form = useForm({})
const logout = () => {
  form.post('/logout')
}
</script>

<style scoped>
/* Existing styles remain the same, adding avatar styling */
.avatar-image {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ffffff;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
}

/* Rest of your existing styles remain unchanged */
.admin-layout {
  display: flex;
  min-height: 100vh;
  position: fixed;
  z-index: 50;
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

.navbar-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;
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
  position: fixed;
  top: 0;
  left: 0;
  padding-top: 75px;
  background-color: #0d6efd;
  display: flex;
  flex-direction: column;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  overflow-y: auto;
  z-index: 900;
  &::-webkit-scrollbar {
    display: none;
  }
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.sidebar-section {
  margin-bottom: 1.5rem;
  padding: 0 0.8rem;
}

.hamburger-btn {
  background-color: #0d6efd;
}

.section-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
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
  color: #ffffff;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  border-radius: 6px;
}

.admin-item i {
  font-size: 1.1rem;
  margin-right: 12px;
  transition: transform 0.2s ease;
  color: #ffffff;
}

.admin-item span {
  font-size: 0.95rem;
  color: #ffffff;
}

.admin-item:hover {
  color: #ffffff;
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateX(2px);
}

.admin-item:hover i {
  transform: translateX(2px);
  color: #ffffff;
}

.admin-item.active-dashboard {
  color: #ffffff;
  font-weight: 600;
  background-color: rgba(255, 255, 255, 0.25);
}

.active-item {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateX(5px);
  font-weight: 600;
  position: relative;
}

.active-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background-color: #ffffff;
  border-radius: 0 4px 4px 0;
}

.admin-item:not(.active-item):hover {
  background-color: rgba(255, 255, 255, 0.1);
  transform: translateX(3px);
}

.bottom-menu {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.danger-item {
  color: #ffffff !important;
  background-color: transparent !important;
}

.danger-item i {
  color: #ffffff !important;
}

.danger-item span {
  color: #ffffff !important;
}

.danger-item:hover {
  color: #ffffff !important;
  background-color: #dc3545 !important;
}

/* Main Content Area */
.main-content {
  display: flex;
}

@media (max-width: 767.98px) {
  .navbar-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }

  .user-info,
  .navbar-icons {
    display: none !important;
  }
}
</style>