<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import Beranda from '@/Components/HeroSectionBeranda.vue'
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'
import Footer from '@/Components/Footer.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/RegisterModal.vue'

import '@/assets/responsive.css'

const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)

const showLogin = ref(false)
const showRegister = ref(false)
</script>

<template>
  <component :is="isLoggedIn ? AuthenticatedLayout : GuestLayout">
    <div class="main-container">
      <Beranda @trigger-login="showLogin = true" />
      <LoginModal
        v-if="!isLoggedIn"
        v-model:visible="showLogin"
        @open-register="() => { showLogin = false; showRegister = true }"
      />
      <RegisterModal
        v-if="!isLoggedIn"
        v-model:visible="showRegister"
      />

      <Section />
      <Feedback />
      <Footer />
    </div>
  </component>
</template>

<style>
body {
  margin: 0;
  padding: 0;
  min-height: 100vh;
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
