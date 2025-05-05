<script setup>
import { usePage, Head} from '@inertiajs/vue3'

import Beranda from '@/Components/HeroSectionBeranda.vue'
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'

import '@/assets/responsive.css'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
page.layout = AppLayout

</script>

<template>  
  <Head title="Beranda" />
  <AppLayout>
    <div class="main-container">
      <Beranda />
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
