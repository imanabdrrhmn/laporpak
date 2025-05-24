<template>
  <div class="admin-layout">
    <NavBarAdmin @toggleSidebar="toggleSidebar" />
    <div
      class="sidebar-overlay"
      v-if="isSidebarOpen"
      @click="closeSidebar"
    ></div>
    <div class="main-content" @click="closeSidebar">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import NavBarAdmin from '@/Components/NavBarAdmin.vue'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
  isSidebarOpen.value = false
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  position: relative;
  min-height: 100vh;
}

.main-content {
  margin-left: 260px;
  padding: 1rem;
  padding-top: 80px;
  background-color: #f8f9fa;
  transition: margin-left 0.3s ease, filter 0.3s ease;
  flex: 1;
}

/* Mobile dan tablet (max 1024px) */
@media (max-width: 767.98px) {
  .main-content {
    margin-left: 0;
    padding-top: 80px;
  }
}

</style>
