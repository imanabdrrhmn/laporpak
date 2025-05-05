<script setup>
import Navbar from '@/Components/NavBar.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ForgotPassword from '@/Components/modals/ResetPasswordModal.vue'

const page = usePage()

const showRegister = ref(false)
const showLoginModal = ref(false)
const showForgotPassword = ref(false)

</script>

<template>
  <div>
    <Navbar @trigger-login="showLoginModal = true" />
    <LoginModal v-model:visible="showLoginModal"
      @open-register="() => { showLoginModal = false; showRegister = true }"
      @open-reset-password="() => { showLoginModal = false; showForgotPassword = true }"   
    />
    <RegisterModal v-model:visible="showRegister" 
    @switch-to-login="() => {showRegister = false; showLoginModal = true}"
    />
    <ForgotPassword 
      v-model:visible="showForgotPassword"
      :status="page.props.status"
      @update:visible="val => showForgotPassword = val"
      @triggerLogin="() =>  { showLoginModal = true; showForgotPassword = false }"
    />
  </div>
  <slot />
</template>
