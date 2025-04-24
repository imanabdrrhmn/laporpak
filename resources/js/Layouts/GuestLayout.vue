<script setup>
import Navbar from '@/Components/NavBar.vue'
import LoginModal from '@/Components/LoginModal.vue'
import { ref } from 'vue'
import RegisterModal from '@/Components/RegisterModal.vue'

const showRegister = ref(false)
const showLoginModal = ref(false)
</script>

<template>
  <div>
    <Navbar @trigger-login="showLoginModal = true" />
    <LoginModal v-model:visible="showLoginModal"
    @open-register="() => { showLoginModal = false; showRegister = true }"/>
    <RegisterModal v-model:visible="showRegister" />
  </div>
  <slot />
</template>
