<script setup>
import Navbar from '@/Components/NavBar.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'
import { ref, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const showRegister = ref(false)
const showLoginModal = ref(false)

</script>

<template>
  <div>
    <Navbar @trigger-login="showLoginModal = true" />
    <LoginModal v-model:visible="showLoginModal"
      @open-register="() => { showLoginModal = false; showRegister = true }"
    />
    <RegisterModal v-model:visible="showRegister" 
    @switch-to-login="() => {showRegister = false; showLoginModal = true}"
    />
  </div>
  
  <slot />
</template>
