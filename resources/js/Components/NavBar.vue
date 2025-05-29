<template>
  <div class="navbar-wrapper">
    <!-- Top Navigation Bar -->
    <div class="shadow-sm navbar-full">
      <div class="container-fluid px-0">
        <div class="d-flex align-items-center justify-content-between py-2 px-3">
          <Link href="/" class="d-flex align-items-center text-decoration-none ms-5">
            <img src="/logo.svg" alt="LaporPak Logo" class="logo-img me-2" />
            <span class="fw-bold text-primary fs-5 text-lp">LaporPak</span>
          </Link>

          <!-- Mobile Toggle Button -->
          <button
            class="navbar-toggler d-lg-none"
            type="button"
            @click="mobileNavOpen = !mobileNavOpen"
            aria-label="Toggle navigation"
          >
            <i class="bi" :class="mobileNavOpen ? 'bi-x-lg' : 'bi-list'"></i>
          </button>

          <!-- Desktop Login Button or User Profile -->
          <div class="d-none d-lg-block me-5">
            <!-- Show login button when not logged in -->
            <button
              v-if="!isLoggedIn"
              class="btn btn-primary rounded-2 px-5 py-2 login-btn"
              @click="$emit('trigger-login')"
            >
              Masuk
            </button>

            <!-- Show user profile when logged in -->
            <div v-else class="dropdown user-dropdown">
              <button
                class="btn dropdown-toggle user-dropdown-btn d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <div class="avatar-wrapper me-2">
                  <img v-if="user.avatar_url" :src="user.avatar_url" alt="User Avatar" class="user-avatar" />
                  <div v-else class="user-avatar-placeholder">
                    {{ getUserInitials() }}
                  </div>
                </div>
                <span class="user-name me-1">Hi, {{ user.name }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-user-info px-3 py-2">
                  <div class="d-flex align-items-center">
                    <div class="avatar-wrapper me-2">
                      <img v-if="user.avatar_url" :src="user.avatar_url" alt="User Avatar" class="user-avatar" />
                      <div v-else class="user-avatar-placeholder">
                        {{ getUserInitials() }}
                      </div>
                    </div>
                    <div>
                      <div class="fw-bold">{{ user.name }}</div>
                      <div class="small text-muted">{{ user.email }}</div>
                    </div>
                  </div>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item" href="/dashboard">
                    <i class="bi bi-file-bar-graph-fill me-2"></i>
                    Dashboard
                  </a>
                </li>
    
                <li>
                  <a class="dropdown-item" href="/profile">
                    <i class="bi bi-person-fill me-2"></i>
                    Profil Saya
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/laporan-saya">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>
                    Laporan Saya
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/top-ups">
                    <i class="bi bi-wallet-fill me-2"></i>
                    Credit
                  </a>
                </li>
                 <li v-if="isAdmin">
                  <a class="dropdown-item" href="/admin/dashboard">
                    <i class="bi bi-shield-fill me-2"></i>
                    Admin Panel
                  </a>
                </li>
                <!-- Slot for additional dropdown items -->
                <li>
                  <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Keluar
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Menu - Desktop -->
      <div class="border-top border-bottom d-none d-lg-block">
        <div class="container-fluid px-0">
          <ul class="nav nav-fill w-100">
            <li class="nav-item">
              <Link
                href="/"
                class="nav-link py-3"
                :class="{ active: $page.url === '/' }"
              >
                <i class="bi bi-house-fill me-2"></i>
                <span>Beranda</span>
              </Link>
            </li>

            <!-- Dropdown Layanan -->
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle py-3"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="bi bi-collection-fill me-2"></i>
                <span>Layanan</span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <Link
                    href="/pelaporan"
                    class="dropdown-item"
                    :class="{ active: $page.url === '/pelaporan' }"
                  >
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Pelaporan
                  </Link>
                </li>
                <li>
                  <Link
                    href="/verifikasi"
                    class="dropdown-item"
                    :class="{ active: $page.url === '/verifikasi' }"
                  >
                    <i class="bi bi-check-square-fill me-2"></i>
                    Verifikasi
                  </Link>
                </li>
                <li>
                  <Link
                    href="/LaporMap"
                    class="dropdown-item"
                    :class="{ active: $page.url === '/LaporMap' }"
                  >
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    Lapor Map
                  </Link>
                </li>
                <li>
                  <Link
                    href="/CariLaporan"
                    class="dropdown-item"
                    :class="{ active: $page.url === '/CariLaporan' }"
                  >
                    <i class="bi bi-search me-2"></i>
                    Cari Laporan
                  </Link>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <Link
                href="/feedback"
                class="nav-link py-3"
                :class="{ 'active-feedback': $page.url === '/feedback' }"
              >
                <i class="bi bi-chat-dots-fill me-2"></i>
                <span>Feedback</span>
              </Link>
            </li>
            <li class="nav-item">
              <Link
                href="/tentang-kami"
                class="nav-link py-3"
                :class="{ 'active-tentang-kami': $page.url === '/tentang-kami' }"
              >
                <i class="bi bi-people-fill me-2"></i>
                <span>Tentang Kami</span>
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Overlay for Mobile -->
    <div
      class="sidebar-overlay d-lg-none"
      :class="{ show: mobileNavOpen }"
      @click="closeMobileNav"
    ></div>

    <!-- Mobile Sidebar Navigation -->
    <div class="sidebar-nav d-lg-none" :class="{ open: mobileNavOpen }">
      <div class="sidebar-content">
        <!-- Header for Mobile Menu -->
        <div
          class="d-flex align-items-center justify-content-between px-4 pb-3 mb-2 border-bottom"
        >
          <Link
            href="/"
            class="d-flex align-items-center text-decoration-none"
            @click="closeMobileNav"
          >
            <img
              src="/logo.svg"
              alt="LaporPak Logo"
              class="logo-img-small me-2"
              style="height: 32px; width: auto"
            />
            <span class="fw-bold text-primary fs-5 text-lp">LaporPak</span>
          </Link>
          <button
            class="btn-close"
            type="button"
            @click="closeMobileNav"
            aria-label="Close menu"
          ></button>
        </div>

        <!-- Mobile Navigation Structure -->
        <ul class="nav flex-column mobile-nav">
          <!-- Beranda -->
          <li class="nav-item">
            <Link href="/" class="nav-link" @click="closeMobileNav">
              <i class="bi bi-house-fill me-2"></i>
              <span>Beranda</span>
            </Link>
          </li>

          <!-- Layanan Dropdown -->
          <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center"
               @click.prevent="toggleLayananMobile = !toggleLayananMobile">
              <div>
                <i class="bi bi-collection-fill me-2"></i>
                <span>Layanan</span>
              </div>
              <i class="bi" :class="toggleLayananMobile ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </a>
            <div class="collapse" :class="{ show: toggleLayananMobile }">
              <div class="nav flex-column submenu">
                <Link href="/pelaporan" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  Pelaporan
                </Link>
                <Link href="/verifikasi" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-check-square-fill me-2"></i>
                  Verifikasi
                </Link>
                <Link href="/LaporMap" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-geo-alt-fill me-2"></i>
                  Lapor Map
                </Link>
                <Link href="/CariLaporan" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-search me-2"></i>
                  Cari Laporan
                </Link>
              </div>
            </div>
          </li>

          <!-- Profile Dropdown -->
          <li v-if="isLoggedIn" class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center"
               @click.prevent="toggleProfileMobile = !toggleProfileMobile">
              <div>
                <i class="bi bi-person-fill me-2"></i>
                <span>Profil</span>
              </div>
              <i class="bi" :class="toggleProfileMobile ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </a>
            <div class="collapse" :class="{ show: toggleProfileMobile }">
              <div class="nav flex-column submenu">
                <Link href="/dashboard" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-speedometer2 me-2"></i>
                  Dashboard
                </Link>
                <Link href="/profile" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-person-fill me-2"></i>
                  Profil Saya
                </Link>
                <Link href="/laporan-saya" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-file-earmark-text-fill me-2"></i>
                  Laporan Saya
                </Link>
                <Link href="/top-ups" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-wallet-fill me-2"></i>
                  Credit
                </Link>
              </div>
            </div>
          </li>

          <!-- Feedback -->
          <li class="nav-item">
            <Link href="/feedback" class="nav-link" @click="closeMobileNav">
              <i class="bi bi-chat-dots-fill me-2"></i>
              <span>Feedback</span>
            </Link>
          </li>

          <!-- Tentang Kami -->
          <li class="nav-item">
            <Link href="/tentang-kami" class="nav-link" @click="closeMobileNav">
              <i class="bi bi-people-fill me-2"></i>
              <span>Tentang Kami</span>
            </Link>
          </li>
          <li class="nav-item" v-if="isAdmin">
            <Link href="/admin/dashboard" class="nav-link" @click="closeMobileNav">
              <i class="bi bi-shield-fill me-2"></i>
                Admin Panel
            </Link>
          </li>
          <!-- Logout and Login Buttons -->
          <li v-if="isLoggedIn" class="nav-item mt-3">
            <a class="nav-link text-danger" href="#" @click.prevent="logout">
              <i class="bi bi-box-arrow-right me-2"></i>
              <span>Keluar</span>
            </a>
          </li>

          <li v-else class="nav-item mt-4 mb-2 d-flex justify-content-center">
            <button class="btn btn-primary rounded-pill px-5" 
                    @click="$emit('trigger-login'); closeMobileNav()">
              Masuk
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

export default {
  name: 'NavbarLaporPak',
  components: {
    Link,
  },
  emits: ['trigger-login'],
  setup() {
    const page = usePage();
    const mobileNavOpen = ref(false);
    const toggleLayananMobile = ref(false);
    const toggleProfileMobile = ref(false);

    const isLoggedIn = computed(() => !!page.props.auth?.user);
    const user = computed(() => page.props.auth?.user || {});
  const isAdmin = computed(() => page.props.auth?.isAdmin)
    const getUserInitials = () => {
      const name = user.value.name || '';
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
    };

    const logout = () => {
      window.axios.post('/logout').then(() => {
        window.location.href = '/';
      });
    };

    const closeMobileNav = () => {
      mobileNavOpen.value = false;
    };

    return {
      mobileNavOpen,
      toggleLayananMobile,
      toggleProfileMobile,
      isLoggedIn,
      user,
      getUserInitials,
      logout,
      closeMobileNav,
      page,
      isAdmin,
    };
  },
};
</script>

<style scoped>
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

.text-lp {
  color: #008cdb !important;
}

.navbar-wrapper {
  position: relative;
  z-index: 1030;
}

.navbar-full {
  width: 100%;
  margin-left: 0;
  position: relative;
  background-color: white;
  overflow: visible !important;
}

.container-fluid {
  overflow: visible !important;
}

.logo-img {
  height: 40px !important;
  width: auto !important;
}

.navbar-toggler {
  border: none;
  background: transparent;
  font-size: 1.5rem;
  color: #2563eb;
  padding: 0.25rem 0.5rem;
  cursor: pointer;
}

/* User Profile Styles */
.user-dropdown {
  position: relative;
}

.login-btn {
  min-width: 150px;
}

.user-dropdown-btn {
  background: linear-gradient(135deg, #f8f9fa, #e9ecef); /* Gradient background */
  border: 1px solid #dee2e6;
  color: #333;
  padding: 0.6rem 1.2rem;
  border-radius: 50px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  min-width: 160px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  transform: scale(1);
}

.user-dropdown-btn:hover {
  background: linear-gradient(135deg, #e9ecef, #dee2e6);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: scale(1.02); /* Slight scale on hover */
}

.avatar-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #008cdb; /* Added border for definition */
}

.user-avatar-placeholder {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: #008cdb;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 14px;
  border: 2px solid #006bb3; /* Matching border for placeholder */
}

.user-name {
  font-weight: 500;
  max-width: 90px; /* Adjusted for "Hi" */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #333;
}

.dropdown-user-info {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  padding: 1rem !important; /* Increased padding */
}

/* Fix for long email in dropdown */
.small.text-muted {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #6c757d;
}

/* User dropdown menu styles */
.user-dropdown .dropdown-menu {
  padding: 0.75rem 0;
  width: 280px; /* Slightly wider */
  right: 0;
  left: auto;
  z-index: 1050;
  margin-top: 0 !important;
  transform: translateY(0);
  border: none;
  border-radius: 12px; /* Softer corners */
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); /* Pronounced shadow */
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.user-dropdown .dropdown-menu.show {
  opacity: 1;
  transform: translateY(4px); /* Slight slide-in effect */
}

.user-dropdown .dropdown-item {
  padding: 0.75rem 1.25rem;
  display: flex;
  align-items: center;
  font-weight: 500;
  color: #444;
  transition: all 0.2s ease;
}

.user-dropdown .dropdown-item:hover {
  background-color: #e9ecef;
  color: #0d6efd;
  transform: translateX(4px); /* Slight slide on hover */
}

.user-dropdown .dropdown-item .bi {
  font-size: 18px;
  margin-right: 12px;
  transition: transform 0.2s ease;
}

.user-dropdown .dropdown-item:hover .bi {
  transform: translateX(2px);
}

.dropdown-divider {
  margin: 0.5rem 0;
  border-color: #dee2e6;
}

/* Desktop Navigation */
@media (min-width: 992px) {
  .nav-item {
    border-right: 1px solid #dee2e6;
    flex: 1;
  }

  .nav-item:first-child {
    border-left: 1px solid #dee2e6;
  }

  .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    color: #666;
    transition: all 0.3s;
  }

  .dropdown-menu {
    min-width: 100%;
    border-radius: 0 0 8px 8px;
    border: none;
    margin-top: 0;
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    padding: 10px 0;
    opacity: 0;
    display: block;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }

  .dropdown:hover .dropdown-menu {
    opacity: 1;
    pointer-events: auto;
  }

  .dropdown-item {
    padding: 0.8rem 1.2rem;
    display: flex;
    align-items: center;
    color: #555;
    font-weight: 500;
    border-left: 3px solid transparent;
  }

  .dropdown-item .bi {
    font-size: 16px;
    margin-right: 10px;
    transition: transform 0.2s ease;
  }

  .dropdown-item:hover .bi {
    transform: translateX(2px);
  }

  .dropdown-item.active {
    color: #0d6efd;
    font-weight: 600;
    background-color: rgba(13, 110, 253, 0.08);
    border-left: 3px solid #0d6efd;
  }

  .nav-link:hover,
  .dropdown:hover .nav-link {
    color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.03);
  }

  .dropdown-toggle::after {
    margin-left: 8px;
    vertical-align: 0.15em;
    transition: transform 0.2s ease;
  }

  .dropdown:hover .dropdown-toggle::after {
    transform: rotate(180deg);
  }
}

/* Mobile Navigation */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1045;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
}

.sidebar-overlay.show {
  opacity: 1;
  visibility: visible;
}

.sidebar-nav {
  position: fixed;
  top: 0;
  right: -280px;
  width: 280px;
  height: 100%;
  background-color: white;
  z-index: 1050;
  transition: transform 0.3s ease-in-out;
  box-shadow: -4px 0 15px rgba(0, 0, 0, 0.15);
  overflow-y: auto;
  padding-top: 10px;
}

.sidebar-nav.open {
  transform: translateX(-280px);
}

.sidebar-nav .nav-item {
  margin: 5px 0;
}

.sidebar-nav .nav-link {
  padding: 12px 20px;
  font-weight: 500;
  font-size: 15px;
  border-radius: 8px;
  margin: 0 8px;
}

.nav-flex-column.ms-4 .nav-link {
  padding: 10px 15px 10px 2.5rem;
  font-size: 14px;
}

@media (max-width: 991px) {
  .sidebar-nav .nav-link {
    color: #666;
    display: flex;
    align-items: center;
  }

  .sidebar-nav .collapse .nav-link {
    color: #666;
  }

  .sidebar-nav .bi {
    font-size: 18px;
    margin-right: 12px;
  }

  .sidebar-content {
    padding: 15px 0;
  }
}

@media (max-width: 991px) {
  .nav-link {
    transition: all 0.25s ease-in-out;
    border-left: 4px solid transparent;
  }

  .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.05);
    border-left-color: rgba(13, 110, 253, 0.3);
    color: #0d6efd !important;
  }

  .nav-link.active,
  .nav-link.active-CariLaporan,
  .nav-link.active-verifikasi,
  .nav-link.active-pelaporan,
  .nav-link.active-LaporMap,
  .nav-link.active-feedback,
  .nav-link.active-tentang-kami,
  .nav-link.active-credit {
    color: #0d6efd !important;
    font-weight: 600;
    background-color: rgba(13, 110, 253, 0.1);
    border-left: 4px solid #0d6efd;
    transform: translateX(3px);
  }

  .collapse {
    transition: all 0.3s ease-out;
  }

  .collapse:not(.show) {
    max-height: 0;
    overflow: hidden;
  }

  .collapse.show {
    max-height: 500px;
  }
}

@media (min-width: 992px) {
  .nav-link {
    transition: all 0.2s ease-in-out;
    border-bottom: 3px solid transparent;
  }

  .nav-link:hover {
    color: #0d6efd !important;
    border-bottom-color: rgba(13, 110, 253, 0.3);
  }

  .nav-link.active,
  .nav-link.active-Beranda,
  .nav-link.active-CariLaporan,
  .nav-link.active-verifikasi,
  .nav-link.active-pelaporan,
  .nav-link.active-LaporMap,
  .nav-link.active-feedback,
  .nav-link.active-tentang-kami,
  .nav-link.active-credit {
    color: #0d6efd !important;
    font-weight: bold;
    border-bottom: 3px solid #0d6efd;
  }

  .dropdown-item {
    transition: all 0.2s ease-in-out;
  }

  .dropdown-item:hover {
    color: #0d6efd;
    transform: translateX(3px);
  }

  .dropdown-item.active {
    color: #0d6efd !important;
    font-weight: bold;
    background-color: #f8f9fa;
  }
}

.nav-flex-column.ms-4 .nav-link {
  padding-left: 2.5rem;
  font-size: 14px;
  border-left: 2px solid transparent;
  margin: 2px 8px;
  border-radius: 6px;
}

.nav-flex-column.ms-4 .nav-link.active {
  border-left: 2px solid #0d6efd;
  background-color: rgba(13, 110, 253, 0.08);
}

.sidebar-nav .btn-primary {
  padding: 10px 24px;
  font-weight: 500;
  box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25);
  transition: all 0.2s ease;
}

.sidebar-nav .btn-primary:active {
  transform: translateY(1px);
  box-shadow: 0 1px 3px rgba(13, 110, 253, 0.2);
}

.mobile-nav .nav-link {
  padding: 15px 20px;
  font-weight: 600;
  font-size: 14px;
  color: #333;
  transition: all 0.2s ease;
}

.mobile-nav .submenu {
  background-color: #f8f9fa;
  margin: 0;
  padding: 5px 0;
}

.mobile-nav .submenu .nav-link {
  padding: 12px 20px 12px 48px;
  font-weight: normal;
  font-size: 13px;
  color: #666;
}

.mobile-nav .collapse {
  transition: all 0.2s ease-out;
}

.mobile-nav .collapse:not(.show) {
  display: none;
}

.mobile-nav .nav-link i.bi-chevron-down,
.mobile-nav .nav-link i.bi-chevron-up {
  transition: transform 0.3s ease;
}

.mobile-nav .nav-item {
  border-bottom: 1px solid #eee;
}

.mobile-nav .nav-item:last-child {
  border-bottom: none;
}
</style>