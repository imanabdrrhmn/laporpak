<template>
  <div class="admin-layout">
    <NavBarAdmin/>
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
  flex:2;
  margin-left: 260px; /* Match sidebar width */
  padding-top: 80px;
  background-color: #f8f9fa; ;

  

}
</style>

