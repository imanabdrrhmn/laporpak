<!-- File: src/components/TopUpPage.vue -->
<template>
  <AppLayout>
    <Head title="Top Up Credit" />
    <div class="top-up-page-container">
      <TopUpHeader :current-balance="currentBalance" />
      <div class="container py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 800px;">
          <div class="card-body p-md-5 p-4">
            <NominalSelection
              v-if="currentStep === 'nominal'"
              :nominal-options="nominalOptions"
              :selected-nominal-package="selectedNominalPackage"
              :custom-amount-input="customAmountInput"
              :custom-amount-error-internal="customAmountErrorInternal"
              :form="form"
              @select-nominal="selectNominalPackage"
              @update:custom-amount="handleCustomAmountInput"
              @blur:custom-amount="validateAndFormatCustomAmount"
              @proceed="proceedToPaymentMethod"
              @back="goBack"
            />
            <PaymentMethodSelection
              v-if="currentStep === 'method'"
              :available-payment-methods="availablePaymentMethods"
              :selected-payment-method-id="selectedPaymentMethodId"
              :form="form"
              @update:payment-method="selectedPaymentMethodId = $event"
              @back="goToStep('nominal')"
              @proceed="goToStep('confirmation')"
            />
            <PaymentConfirmation
              v-if="currentStep === 'confirmation' && selectedPaymentMethodDetails"
              :selected-payment-method-details="selectedPaymentMethodDetails"
              :bca-virtual-account="bcaVirtualAccount"
              :Name="Name"
              :final-selected-amount="finalSelectedAmount"
              :proof-preview="proofPreview"
              :form="form"
              @copy-to-clipboard="copyToClipboard"
              @upload-proof="handleProofUpload"
              @back="goToStep('method')"
              @confirm-payment="handlePaymentConfirmed"
            />
          </div>
        </div>
      </div>
      <Notification
        :show="notification.show"
        :type="notification.type"
        :message="notification.message"
        @close="hideNotification"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Notification from '@/Components/Notification.vue';
import TopUpHeader from './Components/TopUpHeader.vue';
import NominalSelection from './Components/NominalSelection.vue';
import PaymentMethodSelection from './Components/PaymentMethodSelection.vue';
import PaymentConfirmation from './Components/PaymentConfirmation.vue';

const currentStep = ref('nominal');
const currentBalance = ref(250000);
const nominalOptions = ref([
  { idr: 100000 },
  { idr: 200000 },
  { idr: 300000 },
  { idr: 500000 },
  { idr: 1000000 },
  { idr: 2000000 },
]);
const selectedNominalPackage = ref(null);
const customAmountRaw = ref(0);
const customAmountInput = ref('');
const customAmountErrorInternal = ref('');
const availablePaymentMethods = ref([
  {
    id: 'BCA',
    name: 'Bank Central Asia (BCA)',
    description: 'transfer via BCA Mobile Banking atau Internet Banking',
    icon: '/images/Bank_Central_Asia.svg',
    type: 'Transfer'
  },
]);
const selectedPaymentMethodId = ref(null);
const bcaVirtualAccount = ref('2063131911');
const Name = ref('PT Pelopor Ide Kreatif');
const proofPreview = ref(null);
const notification = ref({
  show: false,
  message: '',
  type: 'success',
});
let timerInterval = null;

const form = useForm({
  amount: null,
  payment_method: null,
  proof: null,
});

const finalSelectedAmount = computed(() => {
  if (customAmountRaw.value && customAmountRaw.value >= 100000 && customAmountRaw.value % 100000 === 0) {
    return customAmountRaw.value;
  }
  return selectedNominalPackage.value ? selectedNominalPackage.value.idr : 0;
});

const selectedPaymentMethodDetails = computed(() =>
  availablePaymentMethods.value.find(m => m.id === selectedPaymentMethodId.value)
);

const formatCurrency = (value) => {
  if (isNaN(parseFloat(value))) return '';
  return new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
};

const selectNominalPackage = (option) => {
  selectedNominalPackage.value = option;
  customAmountInput.value = '';
  customAmountRaw.value = 0;
  customAmountErrorInternal.value = '';
  form.clearErrors('amount');
};

const handleCustomAmountInput = (event) => {
  let value = event.target.value.replace(/[^0-9]/g, '');
  customAmountRaw.value = parseInt(value) || 0;
  customAmountInput.value = value;
  customAmountErrorInternal.value = '';
  if (value) {
    selectedNominalPackage.value = null;
    form.clearErrors('amount');
  }
};

const validateAndFormatCustomAmount = (finalCheck = false) => {
  customAmountErrorInternal.value = '';
  if (customAmountRaw.value > 0) {
    if (customAmountRaw.value < 100000) {
      customAmountErrorInternal.value = 'Jumlah minimal adalah Rp 100.000.';
    } else if (customAmountRaw.value % 100000 !== 0) {
      customAmountErrorInternal.value = 'Jumlah harus kelipatan Rp 100.000.';
    }
    if (!customAmountErrorInternal.value || !finalCheck) {
      customAmountInput.value = formatCurrency(customAmountRaw.value);
    }
  } else if (customAmountInput.value !== '') {
    customAmountRaw.value = 0;
    customAmountInput.value = '';
  }
  return !customAmountErrorInternal.value;
};

const isNominalValid = () => {
  if (selectedNominalPackage.value) return true;
  if (customAmountRaw.value > 0) {
    return customAmountRaw.value >= 100000 && customAmountRaw.value % 100000 === 0;
  }
  return false;
};

const proceedToPaymentMethod = () => {
  let customIsValid = true;
  if (customAmountRaw.value > 0 || (customAmountInput.value !== '' && !selectedNominalPackage.value)) {
    customIsValid = validateAndFormatCustomAmount(true);
  }
  if (!selectedNominalPackage.value && !customIsValid) {
    return;
  }
  if (isNominalValid()) {
    goToStep('method');
  } else {
    if (!customAmountErrorInternal.value && customAmountRaw.value > 0) {
      customAmountErrorInternal.value = 'Jumlah tidak valid. Pastikan minimal Rp 100.000 dan kelipatan Rp 100.000.';
    } else if (!selectedNominalPackage.value && customAmountRaw.value === 0) {
      showAppNotification('Silakan pilih nominal atau masukkan jumlah custom yang valid.', 'danger');
    }
  }
};

const goToStep = (stepName) => {
  currentStep.value = stepName;
  if (stepName === 'confirmation' && selectedPaymentMethodDetails.value?.id === 'BCA') {
    startPaymentTimer(59 * 60 + 15);
  } else {
    clearInterval(timerInterval);
  }
  form.clearErrors();
};

const goBack = () => {
  window.history.back();
};


const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text);
    showAppNotification('Nomor Virtual Account disalin!', 'success');
  } catch (err) {
    showAppNotification('Gagal menyalin. Silakan coba lagi.', 'danger');
    console.error('Failed to copy text: ', err);
  }
};

const handleProofUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.proof = file;
    proofPreview.value = URL.createObjectURL(file);
    form.clearErrors('proof');
  } else {
    form.proof = null;
    proofPreview.value = null;
  }
};

const showAppNotification = (message, type = 'success', duration = 4000) => {
  notification.value.message = message;
  notification.value.type = type;
  notification.value.show = true;
  setTimeout(() => {
    hideNotification();
  }, duration);
};

const hideNotification = () => {
  notification.value.show = false;
};

const handlePaymentConfirmed = () => {
  if (!form.proof) {
    showAppNotification('Harap upload bukti pembayaran.', 'danger');
    form.setError('proof', 'Bukti pembayaran wajib diunggah.');
    return;
  }
  form.amount = finalSelectedAmount.value;
  form.payment_method = selectedPaymentMethodId.value;
  form.post('/top-ups/create', {
    onSuccess: () => {
      form.reset();
      proofPreview.value = null;
      selectedNominalPackage.value = null;
      customAmountInput.value = '';
      customAmountRaw.value = 0;
      selectedPaymentMethodId.value = null;
      currentStep.value = 'nominal';
    },
    onError: (errors) => {
      let errorMessages = Object.values(errors).join(' ');
      showAppNotification(`Gagal mengirim permintaan: ${errorMessages || 'Terjadi kesalahan.'}`, 'danger');
    }
  });
};

onMounted(() => {
  if (!document.querySelector('link[href*="bootstrap-icons"]')) {
    const link = document.createElement('link');
    link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css';
    link.rel = 'stylesheet';
    document.head.appendChild(link);
  }
});

onUnmounted(() => {
  clearInterval(timerInterval);
});
</script>

<style scoped>
.top-up-page-container {
  background-color: #f8f9fa;
  min-height: calc(100vh - var(--navbar-height, 60px));
  padding-bottom: 2rem;
}
</style>