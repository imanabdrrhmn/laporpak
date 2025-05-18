<template>
  <NavBar>
    <template #userDropdownItems>
      <li><hr class="dropdown-divider" /></li>
      <li>
        <a class="dropdown-item" href="/admin/dashboard">
          <i class="bi bi-speedometer2 me-2"></i>
          Dashboard Admin
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="/admin/pelaporan">
          <i class="bi bi-file-earmark-text me-2"></i>
          Manajemen Pelaporan
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="/admin/top-ups">
          <i class="bi bi-credit-card me-2"></i>
          Manajemen Top-up
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="/admin/users">
          <i class="bi bi-people me-2"></i>
          Manajemen Pengguna
        </a>
      </li>
    </template>
  </NavBar>
</template>

<script>
import NavBar from '@/Components/NavBar.vue'

export default {
  components: {
    NavBar
  }
}
</script>
