<template>
  <div>
  <component :is="layoutComponent">
    <div v-if="$page.props.auth?.needsEmailVerification" class="bg-yellow-100 text-yellow-800 p-3 rounded">
      📧 Anda belum verifikasi email. <a href="/verify-email" class="underline">Klik di sini untuk verifikasi</a>
    </div>
    <div v-if="$page.props.auth?.needsPhoneVerification" class="bg-yellow-100 text-yellow-800 p-3 rounded">
      📱 Anda belum melakukan verifikasi akun. <a href="/verify-phone" class="underline">Klik di sini untuk verifikasi</a>
    </div>
    <slot />
  </component>
  <Footer/>
</div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AdminLayout from './AdminLayout.vue'
import UserLayout from './UserLayout.vue'
import Footer from '@/Components/Footer.vue'

const page = usePage();

const layoutComponent = computed(() =>
  page.props.auth.isAdmin ? AdminLayout : UserLayout
)
</script>
