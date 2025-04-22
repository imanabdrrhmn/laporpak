<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <Link href="/" class="navbar-brand fw-bold text-primary d-flex align-items-center">
        <img src="/logo-Navbar.svg" alt="LaporPak Logo" height="28" class="me-2 logo" />
      </Link>

      <div class="ms-auto d-flex align-items-center">
        <!-- Dropdown -->
        <div class="dropdown">
          <button
            class="btn btn-outline-primary dropdown-toggle d-flex align-items-center"
            type="button"
            id="userDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <i class="bi bi-person-circle me-2"></i>
            {{ user.name }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><Link class="dropdown-item" href="/dashboard">Dashboard</Link></li>
            <li><Link class="dropdown-item" href="/profile">Profil</Link></li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <Link class="dropdown-item text-danger" method="post" href="/logout" as="button">
                Logout
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'

const user = usePage().props.auth.user
</script>

<style scoped>

.logo {
  height: 30px;
}

.dropdown-toggle::after {
  margin-left: 0.5rem;
}
</style>
