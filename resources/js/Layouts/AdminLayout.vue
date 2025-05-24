<template>
    <NavBarAdmin @toggleSidebar="toggleSidebar" />
    <div class="main-content" @click="closeSidebar">
      <slot />
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

.main-content {
  margin-left: 260px;
  padding-top: 80px; /* space untuk navbar */
  background-color: #f8f9fa;
  transition: margin-left 0.3s ease, filter 0.3s ease;
  flex: 1;
}


@media (max-width: 767.98px) {
  .main-content {
    margin-left: 0;
    padding-top: 80px;
  }
}

</style>
