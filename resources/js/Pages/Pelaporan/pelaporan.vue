<template>
  <AppLayout>
    <Head title="Pelaporan" />
    <section class="hero-section py-4">
      <div class="container-fluid px-0">
        <div class="row g-0">
          <HeroContent :selected-service="selectedService" :service-info="serviceInfo" />
          <ReportForm
            ref="reportForm"
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
    <Section 
      :verifiedReports="verifiedReports"
      :totalReports="totalReports"
      :fraudReports="fraudReports"
    />
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
const verifiedReports = page.props.verifiedReports || 0;
const totalReports = page.props.totalReports || 0;
const fraudReports = page.props.fraudReports || 0;

const userIsLoggedIn = computed(() => !!page.props.auth.user);

// Referensi untuk mengakses ReportForm
const reportForm = ref(null);

// Opsi layanan dengan ikon
const services = [
  { label: 'Penipuan', value: 'Penipuan', icon: 'bi bi-shield-exclamation' },
  { label: 'Infrastruktur', value: 'Infrastruktur', icon: 'bi bi-building-gear' }
];

// Kategori untuk setiap jenis layanan
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

// Informasi layanan
const serviceInfo = {
  Penipuan: {
    badge: 'Platform Pelaporan',
    title: 'Pelaporan Penipuan',
    description: 'Laporkan nomor HP, email, atau akun yang terindikasi mencurigakan untuk verifikasi lebih lanjut.',
    icon: 'bi bi-shield-check',
    formTitle: 'Formulir Pelaporan Penipuan',
    descriptionPlaceholder: 'Ceritakan bagaimana kejadian yang mencurigakan terjadi dan modus yang digunakan...',
  },
  Infrastruktur: {
    badge: 'Lapor Infrastruktur',
    title: 'Pelaporan Infrastruktur',
    description: 'Laporkan fasilitas umum dan infrastruktur yang rusak untuk perbaikan yang lebih cepat.',
    icon: 'bi bi-building-gear',
    formTitle: 'Formulir Pelaporan  Infrastruktur',
    descriptionPlaceholder: 'Jelaskan detail kerusakan dan dampaknya terhadap lingkungan...',
  }
};

// Data reaktif
const selectedService = ref('Penipuan');
const formData = reactive({
  category: '',
  description: '',
  evidence: null,
  location: null,
  address: '',
  source: '',
  region: '',
  email: '',
});

// Pelacak error validasi
const validationErrors = reactive({
  category: false,
  description: false,
  source: false,
  location: false,
  region: false,
  email: false,
});

// Validasi form yang ditingkatkan
const isFormValid = computed(() => {
  const baseValid =
    formData.category &&
    formData.category.trim() !== '' &&
    formData.description &&
    formData.description.trim() !== '' &&
    formData.description.length <= 1500 &&
    formData.location !== null &&
    formData.region &&
    formData.region.trim() !== '';

  if (selectedService.value === 'Penipuan') {
    if (formData.category === 'Email') {
      return baseValid && formData.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email);
    }
    return baseValid && formData.source && formData.source.startsWith('+') && formData.source.length >= 9;
  }
  return baseValid;
});

// Hitung kategori berdasarkan layanan yang dipilih
const currentCategories = computed(() =>
  selectedService.value === 'Penipuan' ? fraudCategories : infrastructureCategories
);

// Pantau perubahan layanan untuk mereset nilai form
watch(selectedService, () => {
  formData.category = '';
  formData.description = '';
  formData.source = '';
  formData.email = '';
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });
});

// Handler pemilihan layanan
const selectService = (value) => {
  selectedService.value = value;
  formData.category = '';
};

// Handler upload file
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 5MB.');
      return;
    }
    formData.evidence = file;
  } else {
    formData.evidence = null;
  }
};

// Handler pengiriman form dengan cek login
const handleSubmitReport = () => {
  if (!userIsLoggedIn.value) {
    showLoginModal.value = true;
  } else {
    submitReport();
  }
};

// Handler pengiriman form
const submitReport = () => {
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });

  let hasErrors = false;
  if (!formData.category || formData.category.trim() === '') {
    validationErrors.category = true;
    hasErrors = true;
  }

  if (!formData.description || formData.description.trim() === '') {
    validationErrors.description = true;
    hasErrors = true;
  }

  if (!formData.location) {
    validationErrors.location = true;
    hasErrors = true;
  }

  if (!formData.region || formData.region.trim() === '') {
    validationErrors.region = true;
    hasErrors = true;
  }

  if (selectedService.value === 'Penipuan') {
    if (formData.category === 'Email') {
      if (!formData.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        validationErrors.email = true;
        hasErrors = true;
      }
    } else {
      if (!formData.source || !formData.source.startsWith('+') || formData.source.length < 9) {
        validationErrors.source = true;
        hasErrors = true;
      }
    }
  }

  if (hasErrors) {
    const firstError = document.querySelector('.is-invalid, .border-danger');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return;
  }

  const dataToSubmit = new FormData();
  dataToSubmit.append('category', formData.category);
  dataToSubmit.append('description', formData.description);
  if (formData.evidence) {
    dataToSubmit.append('evidence', formData.evidence);
  }
  dataToSubmit.append('source', formData.source);
  dataToSubmit.append('location[lat]', formData.location.lat);
  dataToSubmit.append('location[lng]', formData.location.lng);
  dataToSubmit.append('address', formData.address);
  dataToSubmit.append('service', selectedService.value);
  dataToSubmit.append('region', formData.region);
  if (formData.email) {
    dataToSubmit.append('email', formData.email);
  }

  Inertia.post('/pelaporan/create', dataToSubmit, {
    onSuccess: () => {
      // Reset formData
      Object.assign(formData, {
        category: '',
        description: '',
        evidence: null,
        location: null,
        address: '',
        source: '',
        region: '',
        email: '',
      });
      // Panggil resetForm dari child component
      if (reportForm.value) {
        reportForm.value.resetForm();
      }
      showSuccessModal.value = true;
    },
    onError: (errors) => {
      console.error('Error pengiriman:', errors);
      alert('Gagal mengirim laporan. Silakan coba lagi.');
    }
  });
};

// Handler geolokasi
const getCurrentLocation = async () => {
  if (navigator.geolocation) {
    formData.address = 'Mendapatkan alamat...';
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        formData.location = userLocation;

        const { fullAddress, region } = await reverseGeocode(userLocation.lat, userLocation.lng);

        formData.address = fullAddress;
        formData.region = region || '';

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
        formData.address = '';
        formData.region = '';
      },
      { enableHighAccuracy: true }
    );
  } else {
    alert("Browser Anda tidak mendukung Geolocation.");
  }
};

// Reverse geocoding menggunakan Nominatim API
async function reverseGeocode(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
    if (!response.ok) {
      throw new Error('Gagal mendapatkan alamat');
    }
    const data = await response.json();

    const fullAddress = data.display_name || 'Alamat tidak ditemukan';
    const region = data.address?.state || data.address?.city || data.address?.county || '';

    return { fullAddress, region };
  } catch (error) {
    console.error('Error saat reverse geocoding:', error);
    return { fullAddress: 'Alamat tidak tersedia', region: '' };
  }
}

// Validasi deskripsi
const validateDescription = () => {
  if (formData.description.length > 1500) {
    formData.description = formData.description.substring(0, 1500);
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