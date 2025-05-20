<template>
  <div class="admin-layout">
    <NavBarAdmin />
    <div class="main-content">
      <slot />
    </div>
  </div>
</template>

<script setup>
import NavBarAdmin from '@/Components/NavBarAdmin.vue';
</script>

<style scoped>
.admin-layout {
  display: flex;
  overflow: flex;


}


.main-content {
  flex: 1;
  margin-left: 250px; /* Match sidebar width */
  padding: 1rem;
}
</style>
