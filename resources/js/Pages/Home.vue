<script setup>
import { ref, computed } from 'vue'
import { usePage, Head} from '@inertiajs/vue3'

import UserLayout from '@/Layouts/UserLayout.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import Beranda from '@/Components/HeroSectionBeranda.vue'
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'

import '@/assets/responsive.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
page.layout = AppLayout
const showLogin = ref(false)
const showRegister = ref(false)
</script>

<template>  
  <Head title="Beranda" />
  <AppLayout>
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
