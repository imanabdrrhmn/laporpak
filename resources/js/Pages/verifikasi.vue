<template>
  <AppLayout>
    <Head title="Verifikasi" />
    <div class="hero-section">
      <div class="container-fluid px-0">
        <div class="row g-0 min-vh-100">
          <HeroContent />
          <VerificationForm :services="services" />
        </div>
      </div>
    </div>
    <Alur />
    <Section />
    <Feedback :feedbacks="feedbacks" />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alur from '@/Components/alurverifikasi.vue';
import Section from '@/Components/Section.vue';
import Feedback from '@/Components/Feedback.vue';
import HeroContent from '@/Components/Verifikasi/HeroContent.vue';
import VerificationForm from '@/Components/Verifikasi/VerificationForm.vue';

const feedbacks = ref([]);
const services = ref([
  { id: 'npwp-v2', name: 'NPWP Check V2', price: 'IDR 7,200', type: 'Success' },
  { id: 'income-tax', name: 'Income Tax Grade', price: 'IDR 10,000', type: 'Success' },
  { id: 'nik-trace', name: 'Digital NIK Trace (NIK to Phone)', price: 'IDR 10,000', type: 'Success' },
  { id: 'phone-trace', name: 'Phone Trace (Phone to NIK)', price: 'IDR 10,000', type: 'Success' },
  { id: 'blacklist-v1', name: 'Blacklist V1', price: 'IDR 45,000', type: 'Success' },
  { id: 'multi-apply', name: 'Multi-Apply Feature Set', price: 'IDR 3,600', type: 'Query' },
  { id: 'multi-loan', name: 'Multi-Loan Feature Set', price: 'IDR 3,600', type: 'Query' },
  { id: 'portrait', name: 'Portrait Feature Set', price: 'IDR 5,600', type: 'Query' },
  { id: 'telco', name: 'Telco Feature Set', price: 'IDR 3,600', type: 'Query' },
  { id: 'sms', name: 'SMS Feature Set', price: 'IDR 4,800', type: 'Success' },
  { id: 'npwp-v1', name: 'NPWP Check V1', price: 'IDR 7,200', type: 'Success' }
]);

onMounted(() => {
  fetchFeedbacks();
});

const fetchFeedbacks = () => {
  const page = usePage();
  feedbacks.value = page.props.feedbacks;
  console.log(feedbacks.value);
};
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  min-height: 100vh;
  overflow: hidden;
  position: relative;
}

@media (max-height: 600px) and (orientation: landscape) {
  .min-vh-100 {
    min-height: auto !important;
  }
  
  .hero-section {
    min-height: auto;
    overflow-y: auto;
  }
}
</style>