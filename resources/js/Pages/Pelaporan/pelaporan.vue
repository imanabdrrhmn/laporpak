<template>
  <AppLayout>
    <Head title="Pelaporan" />
    <section class="hero-section py-4">
      <div class="container-fluid px-0">
        <div class="row g-0">
          <HeroContent :selected-service="selectedService" :service-info="serviceInfo" />
          <ReportForm
            :provinces="provinces"
            :selected-service="selectedService"
            :services="services"
            :service-info="serviceInfo"
            :current-categories="currentCategories"
            :form-data="formData"
            :validation-errors="validationErrors"
            :is-form-valid="isFormValid"
            @select-service="selectService"
            @submit-report="handleSubmitReport"
            @file-upload="handleFileUpload"
            @validate-description="validateDescription"
            @get-current-location="getCurrentLocation"
          />
        </div>
      </div>
    </section>
    <Alur />
    <Section />
    <Feedback :feedbacks="feedbacks" />
    <SuccessModal
      :show="showSuccessModal"
      @close="showSuccessModal = false"
    />
    <LoginModal v-model:visible="showLoginModal" :is-from-report="true" />
    <RegisterModal v-model:visible="showRegisterModal" :is-from-report="true" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue';
import { Head, usePage, router as Inertia } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alur from '@/Components/Pelaporan/ReportFlow.vue';
import Section from '@/Components/Section.vue';
import Feedback from '@/Components/Feedback.vue';
import SuccessModal from '@/Components/Pelaporan/SuccessModal.vue';
import LoginModal from '@/Components/modals/LoginModal.vue';
import RegisterModal from '@/Components/modals/RegisterModal.vue';
import HeroContent from '@/Components/Pelaporan/HeroContent.vue';
import ReportForm from '@/Components/Pelaporan/ReportForm.vue';

const page = usePage();
const feedbacks = page.props.feedbacks;
const showSuccessModal = ref(false);
const showLoginModal = ref(false);
const showRegisterModal = ref(false);
const provinces = page.props.provinces;

const userIsLoggedIn = computed(() => !!page.props.auth.user);

// Service options with icons
const services = [
  { label: 'Penipuan', value: 'Penipuan', icon: 'bi bi-shield-exclamation' },
  { label: 'Infrastruktur', value: 'Infrastruktur', icon: 'bi bi-building-gear' }
];

// Categories for each service type
const fraudCategories = [
  { label: 'Nomor Telepon', value: 'Nomor Hp' },
  { label: 'Email', value: 'Email' }
];

const infrastructureCategories = [
  { label: 'Jalan Rusak', value: 'Jalan' },
  { label: 'Lampu Penerangan', value: 'Lampu' },
  { label: 'Saluran Air', value: 'Saluran Air' },
  { label: 'Fasilitas Umum', value: 'Fasilitas Umum' },
  { label: 'Lainnya', value: 'Lainnya' }
];

// Service information
const serviceInfo = {
  Penipuan: {
    badge: 'Platform Pelaporan',
    title: 'Pelaporan Penipuan',
    description: 'Laporkan nomor HP, email, atau akun yang terindikasi mencurigakan untuk verifikasi lebih lanjut.',
    icon: 'bi bi-shield-check',
    formTitle: 'Form Pelaporan',
    descriptionPlaceholder: 'Ceritakan bagaimana kejadian yang mencurigakan terjadi dan modus yang digunakan...',
  },
  Infrastruktur: {
    badge: 'Lapor Infrastruktur',
    title: 'Pelaporan Infrastruktur',
    description: 'Laporkan fasilitas umum dan infrastruktur yang rusak untuk perbaikan yang lebih cepat.',
    icon: 'bi bi-building-gear',
    formTitle: 'Form Infrastruktur',
    descriptionPlaceholder: 'Jelaskan detail kerusakan dan dampaknya terhadap lingkungan...',
  }
};

// Reactive data
const selectedService = ref('Penipuan');
const formData = ref({
  category: '',
  description: '',
  evidence: null,
  location: null,
  address: '',
  source: '',
  region: '',
});

// Validation errors tracker
const validationErrors = reactive({
  category: false,
  description: false,
  source: false,
  location: false
});

// Improved form validation
const isFormValid = computed(() => {
  return formData.value.category &&
         formData.value.category.trim() !== '' &&
         formData.value.description &&
         formData.value.description.trim() !== '' &&
         formData.value.description.length <= 1500 &&
         formData.value.location !== null;
});

// Compute current categories based on selected service
const currentCategories = computed(() =>
  selectedService.value === 'Penipuan' ? fraudCategories : infrastructureCategories
);

// Watch for service changes to reset form values
watch(selectedService, () => {
  formData.value.category = '';
  formData.value.description = '';
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });
});

// Service selection handler
const selectService = (value) => {
  selectedService.value = value;
  formData.value.category = '';
};

// File upload handler
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 5MB.');
      event.target.value = '';
      return;
    }
    formData.value.evidence = file;
  }
};

// Handle form submission with login check
const handleSubmitReport = () => {
  if (!userIsLoggedIn.value) {
    showLoginModal.value = true;
  } else {
    submitReport();
  }
};

// Form submission handler
const submitReport = () => {
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });

  let hasErrors = false;
  if (!formData.value.category || formData.value.category.trim() === '') {
    validationErrors.category = true;
    hasErrors = true;
  }

  if (!formData.value.description || formData.value.description.trim() === '') {
    validationErrors.description = true;
    hasErrors = true;
  }

  if (!formData.value.location) {
    validationErrors.location = true;
    hasErrors = true;
  }

  if (!formData.value.region || formData.value.region.trim() === '') {
  validationErrors.region = true;
  hasErrors = true;
  } else {
    validationErrors.region = false;
  }

  if (hasErrors) {
    const firstError = document.querySelector('.is-invalid, .border-danger');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return;
  }

  const dataToSubmit = new FormData();
  dataToSubmit.append('category', formData.value.category);
  dataToSubmit.append('description', formData.value.description);
  if (formData.value.evidence) {
    dataToSubmit.append('evidence', formData.value.evidence);
  }
  dataToSubmit.append('source', formData.value.source);
  dataToSubmit.append('location[lat]', formData.value.location.lat);
  dataToSubmit.append('location[lng]', formData.value.location.lng);
  dataToSubmit.append('address', formData.value.address);
  dataToSubmit.append('service', selectedService.value);
  dataToSubmit.append('region', formData.value.region || '');

  Inertia.post('/pelaporan/create', dataToSubmit, {
    onSuccess: () => {
      formData.value = {
        category: '',
        description: '',
        evidence: null,
        location: null,
        address: '',
        source: '',
      };
      showSuccessModal.value = true;
    },
    onError: (error) => {
      console.error('Error:', error);
    }
  });
};

// Geolocation handler
const getCurrentLocation = () => {
  if (navigator.geolocation) {
    formData.value.address = 'Mendapatkan alamat...';
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        formData.value.location = userLocation;

        // Ambil alamat dan region sekaligus
        const { fullAddress, region } = await reverseGeocode(userLocation.lat, userLocation.lng);

        formData.value.address = fullAddress;
        formData.value.region = region || '';  // simpan region ke formData

        validationErrors.location = false;
      },
      (error) => {
        let message;
        switch(error.code) {
          case error.PERMISSION_DENIED:
            message = "Akses lokasi ditolak oleh pengguna.";
            break;
          case error.POSITION_UNAVAILABLE:
            message = "Informasi lokasi tidak tersedia.";
            break;
          case error.TIMEOUT:
            message = "Waktu permintaan lokasi habis.";
            break;
          default:
            message = "Terjadi kesalahan saat mendapatkan lokasi.";
        }
        alert(message);
        formData.value.address = '';
        formData.value.region = '';  // kosongkan region jika gagal
      },
      { enableHighAccuracy: true }
    );
  } else {
    alert("Browser Anda tidak mendukung Geolocation.");
  }
};


// Reverse geocoding using Nominatim API
async function reverseGeocode(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
    if (!response.ok) {
      throw new Error('Gagal mendapatkan alamat');
    }
    const data = await response.json();

    const fullAddress = data.display_name || 'Alamat tidak ditemukan';

    // region bisa diambil dari beberapa kemungkinan, contoh:
    const region = data.address?.state || data.address?.city || data.address?.county || null;

    return { fullAddress, region };
  } catch (error) {
    console.error('Error saat reverse geocoding:', error);
    return { fullAddress: 'Alamat tidak tersedia', region: null };
  }
}


// Description validation
const validateDescription = () => {
  if (formData.value.description.length > 1500) {
    formData.value.description = formData.value.description.substring(0, 1500);
    const textarea = document.querySelector('.custom-textarea');
    if (textarea) {
      textarea.classList.add('border-danger');
      setTimeout(() => {
        textarea.classList.remove('border-danger');
      }, 800);
    }
  }
};
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  overflow: hidden;
  position: relative;
  min-height: auto;
  padding-bottom: 2rem;
}

@media (min-width: 992px) {
  .hero-section {
    min-height: 100vh;
  }
}

@media (max-height: 600px) and (orientation: landscape) {
  .hero-section {
    min-height: auto;
    overflow-y: auto;
  }
}

@media (min-width: 1400px) {
  .container-fluid {
    max-width: 1400px;
  }
}
</style>