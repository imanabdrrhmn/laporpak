<script setup>
import { ref, watch } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import Beranda from '@/Components/HeroSectionBeranda.vue'
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'

import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'
import ForgotPasswordModal from '@/Components/modals/ResetPasswordModal.vue'
import Notification from '@/Components/Notification.vue'

const page = usePage()
const feedbacks = page.props.feedbacks

const showLogin = ref(false)
const showRegister = ref(false)
const showForgotPassword = ref(false)
const status = ref(page.props.flash.status || '')
const error = ref(page.props.flash.error || '')


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
  <Head title="Beranda" />
  <AppLayout>
    <div class="main-container">
      <Beranda @show-login-modal="openLoginModal" />
      <Section />
      <Feedback :feedbacks="feedbacks" />

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
    <Notification v-if="status" type="success" :message="status" />
    <!-- Notification untuk error -->
    <Notification v-if="error" type="error" :message="error" />
    </div>
  </AppLayout>
</template>


<style>
 body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    max-width: 100vw;
    background-color: #f8f9fa;
    font-family: 'poppins', sans-serif;
  }

  .main-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
  }

  #app {
    text-align: left;
    padding: 0;
    max-width: none;
    min-height: 100vh;
    width: 100%;
  }
  </style>
