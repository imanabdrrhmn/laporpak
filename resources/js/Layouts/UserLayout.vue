<script setup>
import Navbar from '@/Components/NavBar.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ForgotPasswordModal from '@/Components/modals/ResetPasswordModal.vue'

const page = usePage()

const showLogin = ref(false)
const showRegister = ref(false)
const showForgotPassword = ref(false)

watch(
  () => page.props.auth.user,
  (newUser) => {
    if (newUser) {
      showLogin.value = false
      showRegister.value = false
      showForgotPassword.value = false
    }
  },
  { immediate: true }
)

function switchModal(target) {
  showLogin.value = target === 'login'
  showRegister.value = target === 'register'
  showForgotPassword.value = target === 'forgot'
}

function openLoginModal() {
  switchModal('login')
}

watch(
  () => page.props.auth.user,
  (newUser) => {
    if (newUser) switchModal(null)
  },
  { immediate: true }
)
</script>

<template>
  <div>
    <Navbar @trigger-login="openLoginModal()" />
    <LoginModal 
        :visible="showLogin" 
        @update:visible="showLogin = $event"
        @open-register="() => switchModal('register')"
        @open-reset-password="() => switchModal('forgot')"
      />
      <RegisterModal 
        :visible="showRegister" 
        @update:visible="showRegister = $event"
        @open-login="() => switchModal('login')"
      />
      <ForgotPasswordModal 
        :visible="showForgotPassword"
        @update:visible="showForgotPassword = $event"
        @open-login="() => switchModal('login')"
      />
  </div>
  <slot />
</template>
