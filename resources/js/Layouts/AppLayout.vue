<template>
  <component :is="layoutComponent">
    <slot />
  </component>
  <Footer/>
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
