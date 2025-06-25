<template>
  <AppLayout>
    <Head title="Pelaporan" />
    
    <section class="hero-section py-4">
      <div class="hero-overlay"></div>
      <div class="container-fluid px-0">
        <div class="row g-0">
          <HeroContent 
            :selected-service="selectedService" 
            :service-info="serviceInfo" 
          />
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
            @select-service="handleServiceSelection"
            @submit-report="handleReportSubmission"
            @file-upload="handleFileUpload"
            @validate-description="handleDescriptionValidation"
            @get-current-location="handleLocationRequest"
          />
        </div>
      </div>
    </section>

    <Alur />
    <Section 
      :verified-reports="verifiedReports"
      :total-reports="totalReports"
      :fraud-reports="fraudReports"
    />
    <Feedback :feedbacks="feedbacks" />
    
    <!-- Modals -->
    <SuccessModal
      :show="showSuccessModal"
      @close="closeSuccessModal"
    />
    <LoginModal 
      v-model:visible="showLoginModal" 
      :is-from-report="true" 
    />
    <RegisterModal 
      v-model:visible="showRegisterModal" 
      :is-from-report="true" 
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { Head, usePage, router as Inertia } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Alur from '@/Components/Pelaporan/ReportFlow.vue'
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'
import SuccessModal from '@/Components/Pelaporan/SuccessModal.vue'
import LoginModal from '@/Components/modals/LoginModal.vue'
import RegisterModal from '@/Components/modals/RegisterModal.vue'
import HeroContent from '@/Components/Pelaporan/HeroContent.vue'
import ReportForm from '@/Components/Pelaporan/ReportForm.vue'

// Constants
const VALIDATION_CONSTANTS = {
  MAX_DESCRIPTION_LENGTH: 1500,
  MAX_FILE_SIZE: 5 * 1024 * 1024, // 5MB
  MIN_PHONE_LENGTH: 9,
  EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  PHONE_PREFIX: '+',
  BORDER_HIGHLIGHT_DURATION: 800
}

const SERVICE_TYPES = {
  FRAUD: 'Penipuan',
  INFRASTRUCTURE: 'Infrastruktur'
}

const CATEGORY_TYPES = {
  PHONE: 'Nomor Hp',
  EMAIL: 'Email'
}

// Props from Inertia
const page = usePage()
const {
  feedbacks,
  provinces,
  verifiedReports = 0,
  totalReports = 0,
  fraudReports = 0
} = page.props

// Computed properties
const userIsLoggedIn = computed(() => !!page.props.auth.user)

// Reactive state
const selectedService = ref(SERVICE_TYPES.FRAUD)
const showSuccessModal = ref(false)
const showLoginModal = ref(false)
const showRegisterModal = ref(false)
const reportForm = ref(null)

// Form data
const formData = reactive({
  category: '',
  description: '',
  evidence: null,
  location: null,
  address: '',
  source: '',
  region: '',
  email: ''
})

// Validation errors
const validationErrors = reactive({
  category: false,
  description: false,
  source: false,
  location: false,
  region: false,
  email: false
})

// Service configuration
const services = [
  { 
    label: 'Penipuan', 
    value: SERVICE_TYPES.FRAUD, 
    icon: 'bi bi-shield-exclamation' 
  },
  { 
    label: 'Infrastruktur', 
    value: SERVICE_TYPES.INFRASTRUCTURE, 
    icon: 'bi bi-building-gear' 
  }
]

const fraudCategories = [
  { label: 'Nomor Telepon', value: CATEGORY_TYPES.PHONE },
  { label: 'Email', value: CATEGORY_TYPES.EMAIL }
]

const infrastructureCategories = [
  { label: 'Jalan Rusak', value: 'Jalan' },
  { label: 'Lampu Penerangan', value: 'Lampu' },
  { label: 'Saluran Air', value: 'Saluran Air' },
  { label: 'Fasilitas Umum', value: 'Fasilitas Umum' },
  { label: 'Lainnya', value: 'Lainnya' }
]

const serviceInfo = {
  [SERVICE_TYPES.FRAUD]: {
    badge: 'Platform Pelaporan',
    title: 'Pelaporan Penipuan',
    description: 'Laporkan nomor HP, email, atau akun yang terindikasi mencurigakan untuk verifikasi lebih lanjut.',
    icon: 'bi bi-shield-check',
    formTitle: 'Formulir Pelaporan Penipuan',
    descriptionPlaceholder: 'Ceritakan bagaimana kejadian yang mencurigakan terjadi dan modus yang digunakan...'
  },
  [SERVICE_TYPES.INFRASTRUCTURE]: {
    badge: 'Lapor Infrastruktur',
    title: 'Pelaporan Infrastruktur',
    description: 'Laporkan fasilitas umum dan infrastruktur yang rusak untuk perbaikan yang lebih cepat.',
    icon: 'bi bi-building-gear',
    formTitle: 'Formulir Pelaporan Infrastruktur',
    descriptionPlaceholder: 'Jelaskan detail kerusakan dan dampaknya terhadap lingkungan...'
  }
}

// Computed
const currentCategories = computed(() =>
  selectedService.value === SERVICE_TYPES.FRAUD ? fraudCategories : infrastructureCategories
)

const isFormValid = computed(() => {
  const baseValidation = validateBaseForm()
  
  if (selectedService.value === SERVICE_TYPES.FRAUD) {
    return baseValidation && validateFraudSpecificFields()
  }
  
  return baseValidation
})

// Validation functions
function validateBaseForm() {
  return (
    isFieldValid(formData.category) &&
    isFieldValid(formData.description) &&
    formData.description.length <= VALIDATION_CONSTANTS.MAX_DESCRIPTION_LENGTH &&
    formData.location !== null &&
    isFieldValid(formData.region)
  )
}

function validateFraudSpecificFields() {
  if (formData.category === CATEGORY_TYPES.EMAIL) {
    return formData.email && VALIDATION_CONSTANTS.EMAIL_REGEX.test(formData.email)
  }
  
  return (
    formData.source &&
    formData.source.startsWith(VALIDATION_CONSTANTS.PHONE_PREFIX) &&
    formData.source.length >= VALIDATION_CONSTANTS.MIN_PHONE_LENGTH
  )
}

function isFieldValid(field) {
  return field && field.trim() !== ''
}

// Watchers
watch(selectedService, () => {
  resetFormData()
  clearValidationErrors()
})

// Event handlers
function handleServiceSelection(value) {
  selectedService.value = value
  formData.category = ''
}

function handleFileUpload(event) {
  const file = event.target.files[0]
  
  if (!file) {
    formData.evidence = null
    return
  }
  
  if (file.size > VALIDATION_CONSTANTS.MAX_FILE_SIZE) {
    showAlert('Ukuran file terlalu besar. Maksimal 5MB.')
    return
  }
  
  formData.evidence = file
}

function handleReportSubmission() {
  if (!userIsLoggedIn.value) {
    showLoginModal.value = true
    return
  }
  
  submitReport()
}

function handleDescriptionValidation() {
  if (formData.description.length > VALIDATION_CONSTANTS.MAX_DESCRIPTION_LENGTH) {
    formData.description = formData.description.substring(0, VALIDATION_CONSTANTS.MAX_DESCRIPTION_LENGTH)
    highlightTextareaError()
  }
}

function handleLocationRequest() {
  if (!navigator.geolocation) {
    showAlert('Browser Anda tidak mendukung Geolocation.')
    return
  }
  
  getCurrentLocation()
}

// Core business logic
function submitReport() {
  clearValidationErrors()
  
  if (!validateFormData()) {
    scrollToFirstError()
    return
  }
  
  const formDataToSubmit = prepareFormData()
  
  Inertia.post('/pelaporan/create', formDataToSubmit, {
    onSuccess: handleSubmitSuccess,
    onError: handleSubmitError
  })
}

function validateFormData() {
  const validators = [
    () => validateField('category', formData.category),
    () => validateField('description', formData.description),
    () => validateField('location', formData.location),
    () => validateField('region', formData.region),
    () => validateServiceSpecificFields()
  ]
  
  return validators.every(validator => validator())
}

function validateField(fieldName, value) {
  const isValid = fieldName === 'location' ? value !== null : isFieldValid(value)
  validationErrors[fieldName] = !isValid
  return isValid
}

function validateServiceSpecificFields() {
  if (selectedService.value !== SERVICE_TYPES.FRAUD) {
    return true
  }
  
  if (formData.category === CATEGORY_TYPES.EMAIL) {
    const isValid = formData.email && VALIDATION_CONSTANTS.EMAIL_REGEX.test(formData.email)
    validationErrors.email = !isValid
    return isValid
  }
  
  const isValid = (
    formData.source &&
    formData.source.startsWith(VALIDATION_CONSTANTS.PHONE_PREFIX) &&
    formData.source.length >= VALIDATION_CONSTANTS.MIN_PHONE_LENGTH
  )
  validationErrors.source = !isValid
  return isValid
}

function prepareFormData() {
  const dataToSubmit = new FormData()
  
  const fields = [
    'category',
    'description',
    'source',
    'address',
    'region',
    'email'
  ]
  
  fields.forEach(field => {
    if (formData[field]) {
      dataToSubmit.append(field, formData[field])
    }
  })
  
  if (formData.evidence) {
    dataToSubmit.append('evidence', formData.evidence)
  }
  
  if (formData.location) {
    dataToSubmit.append('location[lat]', formData.location.lat)
    dataToSubmit.append('location[lng]', formData.location.lng)
  }
  
  dataToSubmit.append('service', selectedService.value)
  
  return dataToSubmit
}

function getCurrentLocation() {
  formData.address = 'Mendapatkan alamat...'
  
  navigator.geolocation.getCurrentPosition(
    handleLocationSuccess,
    handleLocationError,
    { enableHighAccuracy: true }
  )
}

async function handleLocationSuccess(position) {
  const userLocation = {
    lat: position.coords.latitude,
    lng: position.coords.longitude
  }
  
  formData.location = userLocation
  
  try {
    const { fullAddress, region } = await reverseGeocode(userLocation.lat, userLocation.lng)
    formData.address = fullAddress
    formData.region = region || ''
    validationErrors.location = false
  } catch (error) {
    console.error('Reverse geocoding error:', error)
    formData.address = 'Alamat tidak tersedia'
    formData.region = ''
  }
}

function handleLocationError(error) {
  const errorMessages = {
    [error.PERMISSION_DENIED]: 'Akses lokasi ditolak oleh pengguna.',
    [error.POSITION_UNAVAILABLE]: 'Informasi lokasi tidak tersedia.',
    [error.TIMEOUT]: 'Waktu permintaan lokasi habis.',
    default: 'Terjadi kesalahan saat mendapatkan lokasi.'
  }
  
  const message = errorMessages[error.code] || errorMessages.default
  showAlert(message)
  
  formData.address = ''
  formData.region = ''
}

async function reverseGeocode(lat, lng) {
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
  
  try {
    const response = await fetch(url)
    
    if (!response.ok) {
      throw new Error('Failed to fetch address')
    }
    
    const data = await response.json()
    
    return {
      fullAddress: data.display_name || 'Alamat tidak ditemukan',
      region: data.address?.state || data.address?.city || data.address?.county || ''
    }
  } catch (error) {
    console.error('Reverse geocoding error:', error)
    return {
      fullAddress: 'Alamat tidak tersedia',
      region: ''
    }
  }
}

// Success and error handlers
function handleSubmitSuccess() {
  resetFormData()
  
  if (reportForm.value?.resetForm) {
    reportForm.value.resetForm()
  }
  
  showSuccessModal.value = true
}

function handleSubmitError(errors) {
  console.error('Submit error:', errors)
  showAlert('Gagal mengirim laporan. Silakan coba lagi.')
}

// Utility functions
function resetFormData() {
  Object.assign(formData, {
    category: '',
    description: '',
    evidence: null,
    location: null,
    address: '',
    source: '',
    region: '',
    email: ''
  })
}

function clearValidationErrors() {
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false
  })
}

function scrollToFirstError() {
  const firstError = document.querySelector('.is-invalid, .border-danger')
  firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' })
}

function highlightTextareaError() {
  const textarea = document.querySelector('.custom-textarea')
  
  if (!textarea) return
  
  textarea.classList.add('border-danger')
  setTimeout(() => {
    textarea.classList.remove('border-danger')
  }, VALIDATION_CONSTANTS.BORDER_HIGHLIGHT_DURATION)
}

function showAlert(message) {
  alert(message)
}

function closeSuccessModal() {
  showSuccessModal.value = false
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  overflow: hidden;
  position: relative;
  min-height: auto;
  padding-bottom: 2rem;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
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