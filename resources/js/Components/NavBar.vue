<template>
  <div class="shadow-sm navbar-full">
    <!-- Logo & Login Button -->
    <div class="container-fluid px-0">
      <div class="d-flex align-items-center justify-content-between py-2 px-3">
        <Link href="/" class="d-flex align-items-center text-decoration-none">
          <img src="/logo-Navbar.svg" alt="LaporPak Logo" height="25" class="me-2 logo" />
          <!-- <span class="fw-bold fs-5 d-none d-sm-inline text-logo">LaporPak</span> -->
        </Link>

        <!-- Mobile Toggle Button -->
        <button
          class="navbar-toggler d-md-none"
          type="button"
          @click="mobileNavOpen = !mobileNavOpen"
          aria-label="Toggle navigation"
        >
          <i class="bi" :class="mobileNavOpen ? 'bi-x-lg' : 'bi-list'"></i>
        </button>

        <button
          class="btn btn-primary rounded-2 px-3 px-md-5 py-1 shadow d-none d-md-block"
          @click="$emit('trigger-login')"
        >
          Masuk
        </button>
      </div>
    </div>

    <!-- Navigation Menu - Desktop -->
    <div class="border-top border-bottom d-none d-md-block">
      <div class="container-fluid px-0">
        <ul class="nav nav-fill w-100">
          <li class="nav-item" v-for="link in navLinks" :key="link.href">
            <Link :href="link.href" class="nav-link py-3" :class="{ 'router-link-exact-active': isActive(link.href) }">
              <i :class="`bi ${link.icon} me-2`"></i>
              {{ link.label }}
            </Link>
          </li>
        </ul>
      </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-nav d-md-none" :class="{ 'show': mobileNavOpen }">
      <div class="container-fluid px-0">
        <ul class="nav flex-column">
          <li class="nav-item" v-for="link in navLinks" :key="link.href">
            <Link
              :href="link.href"
              class="nav-link py-3"
              @click="mobileNavOpen = false"
              :class="{ 'router-link-exact-active': isActive(link.href) }"
            >
              <i :class="`bi ${link.icon} me-2`"></i>
              {{ link.label }}
            </Link>
          </li>
        </ul>
      </div>
      <div class="d-flex justify-content-center py-3">
          <button
            class="btn btn-primary rounded-2 px-5 shadow"
            @click="$emit('trigger-login')"
          >
            Masuk
          </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const mobileNavOpen = ref(false)

router.on('navigate', () => {
  mobileNavOpen.value = false
})

const page = usePage()
const currentPath = computed(() => page.url)

const isActive = (path) => currentPath.value === path

const navLinks = [
  { label: 'Beranda', href: '/beranda', icon: 'bi-house-fill' },
  { label: 'Pelaporan', href: '/pelaporan', icon: 'bi-exclamation-triangle-fill' },
  { label: 'Verifikasi', href: '/verifikasi', icon: 'bi-check-square-fill' },
  { label: 'Lapor Map', href: '/lapor-map', icon: 'bi-geo-alt-fill' },
  { label: 'Tentang Kami', href: '/tentang-kami', icon: 'bi-people-fill' }
]
</script>

<style scoped>
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

.logo {
  height: 30px;
}

.navbar-full {
  width: 100%;
  margin-left: 0;
  position: relative;
  z-index: 1030;
}

/* Nav Links */
.nav-link {
  color: #666;
  transition: all 0.3s;
  padding-left: 1rem;
  padding-right: 1rem;
}

.nav-link:hover {
  color: #0d6efd;
  background-color: #f8f9fa;
}

/* Active nav item */
.nav-link.router-link-exact-active {
  color: #0d6efd;
  font-weight: bold;
  background-color: #f8f9fa;
  border-bottom: 3px solid #0d6efd;
}

.nav-item {
  text-align: center;
}

/* Desktop Nav */
@media (min-width: 768px) {
  .nav-item {
    border-right: 1px solid #dee2e6;
  }

  .nav-item:first-child {
    border-left: 1px solid #dee2e6;
  }
}

@media (max-width: 992px) {
  .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 0.5rem;
  }
}

/* Mobile Nav */
.navbar-toggler {
  border: none;
  background: transparent;
  font-size: 1.5rem;
  color: #0d6efd;
  padding: 0.25rem 0.5rem;
}

.mobile-nav {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background-color: white;
  border-bottom: 1px solid #dee2e6;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transform: translateY(-100%);
  max-height: 0;
  overflow: hidden;
  transition: all 0.3s ease;
  opacity: 0;
  z-index: 1020;
}

.mobile-nav.show {
  transform: translateY(0);
  max-height: 100vh;
  opacity: 1;
}

.mobile-nav .nav-item {
  border-bottom: 1px solid #dee2e6;
}

.mobile-nav .nav-link {
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
}

/* Small screens */
@media (max-width: 576px) {
  .mobile-nav .nav-link {
    font-size: 1rem;
  }
}
</style>
