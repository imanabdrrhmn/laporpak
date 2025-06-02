<template>
  <AppLayout>
    <Head title="Top Up Credit" />
    <div class="top-up-page-container">
      <div class="top-up-header-bar">
        <div class="container d-flex flex-wrap justify-content-between align-items-center py-3">
          <h2 class="text-white fw-bold mb-0 h5 me-3">Top Up Credit</h2>
        </div>
      </div>

      <div class="container py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 800px;">
          <div class="card-body p-md-5 p-3">

            <div v-if="currentStep === 'nominal'">
              <h3 class="fw-bold mb-4 text-center text-md-start">Pilih Nominal</h3>
              <div class="row g-3 mb-4">
                <div v-for="option in nominalOptions" :key="option.idr" class="col-6 col-sm-4">
                  <button
                    type="button"
                    class="btn btn-outline-primary w-100 p-3 text-center nominal-option d-flex flex-column justify-content-center align-items-center"
                    :class="{ 'active': selectedNominalPackage && selectedNominalPackage.idr === option.idr }"
                    @click="selectNominalPackage(option)"
                    style="min-height: 70px;"
                  >
                    <div class="fw-bold fs-5">Rp {{ formatCurrency(option.idr) }}</div>
                  </button>
                </div>
              </div>
              <p class="text-muted mb-2">atau masukan jumlah kelipatan Rp 100.000 (min. Rp 100.000)</p>
              <div class="input-group mb-1">
                <span class="input-group-text fw-bold">Rp</span>
                <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': (form.errors.amount && customAmountRaw > 0) || customAmountErrorInternal}"
                  placeholder="Contoh: 200000"
                  v-model="customAmountInput"
                  @input="handleCustomAmountInput"
                  @blur="validateAndFormatCustomAmount"
                >
              </div>
              <div v-if="(form.errors.amount && customAmountRaw > 0) || customAmountErrorInternal" class="invalid-feedback d-block mb-3">
                {{ form.errors.amount || customAmountErrorInternal }}
              </div>

              <div class="d-flex justify-content-end mt-4">
                <button
                  class="btn btn-primary px-4 w-100 w-md-auto"
                  @click="proceedToPaymentMethod"
                  :disabled="!isNominalValid()"
                >
                  Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
              </div>
            </div>

            <div v-if="currentStep === 'method'">
              <h3 class="fw-bold mb-4 text-center text-md-start">Metode Pembayaran</h3>
              <div
                v-for="method in availablePaymentMethods"
                :key="method.id"
                class="form-check p-0 border rounded mb-3 payment-method-option"
                :class="{'selected': selectedPaymentMethodId === method.id, 'is-invalid': form.errors.payment_method && selectedPaymentMethodId === method.id}"
                @click="selectedPaymentMethodId = method.id"
              >
                <label class="form-check-label d-flex justify-content-between align-items-center w-100 p-3" :for="method.id">
                  <div class="d-flex align-items-center">
                    <img :src="method.icon" :alt="method.name" style="height: 24px; width: auto;" class="me-3 d-none d-sm-inline">
                    <div>
                      <span class="fw-bold">{{ method.name }}</span>
                      <div class="small text-muted">{{ method.description }}</div>
                    </div>
                  </div>
                  <input
                    class="form-check-input"
                    type="radio"
                    :id="method.id"
                    :value="method.id"
                    v-model="selectedPaymentMethodId"
                    style="float: right; margin-left: auto; margin-right: 0.5rem; width: 1.2em; height: 1.2em;"
                  />
                </label>
              </div>
               <div v-if="form.errors.payment_method" class="text-danger small mt-2">
                    {{ form.errors.payment_method }}
                </div>
              <div class="d-flex flex-column flex-md-row justify-content-between mt-5">
                <button class="btn btn-outline-secondary px-4 mb-2 mb-md-0 w-100 w-md-auto" @click="goToStep('nominal')">
                  <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button class="btn btn-primary px-4 w-100 w-md-auto" @click="goToStep('confirmation')" :disabled="!selectedPaymentMethodId">
                  Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
              </div>
            </div>

            <div v-if="currentStep === 'confirmation' && selectedPaymentMethodDetails">
              <h3 class="fw-bold mb-4 text-center">Pembayaran {{ selectedPaymentMethodDetails.name }}</h3>
              <div v-if="selectedPaymentMethodDetails.id === 'BCA'" class="mb-4">
                <p class="fw-bold">Intruksi Pembayaran</p>
                <ol class="mb-4 ps-4">
                  <li>Buka BCA Mobile Banking atau Internet Banking Anda</li>
                  <li>Pilih menu "Transfer" ke Rekening Virtual Account</li>
                  <li>Masukkan Nomer Rekening Virtual di bawah ini</li>
                  <li>Konfirmasikan jumlah dan selesaikan transaksi</li>
                </ol>
                <div class="text-center bg-light p-3 rounded">
                  <p class="mb-1 text-muted small">Nomer {{ selectedPaymentMethodDetails.name }} Virtual Account</p>
                  <div class="d-flex flex-wrap justify-content-center align-items-center">
                    <span class="fs-4 fw-bold me-3 mb-2 mb-sm-0">{{ bcaVirtualAccount }}</span>
                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard(bcaVirtualAccount)">
                      <i class="bi bi-clipboard me-1"></i> copy
                    </button>
                  </div>
                </div>
              </div>
              <div class="border rounded p-3 mb-4">
                <h5 class="fw-bold mb-3">Ringkasan Pembayaran</h5>
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Jumlah :</span>
                  <span class="fw-bold">Rp {{ formatCurrency(finalSelectedAmount) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Metode Pembayaran :</span>
                  <span class="fw-bold">{{ selectedPaymentMethodDetails.name }} {{ selectedPaymentMethodDetails.type || '' }}</span>
                </div>
              </div>

              <div class="mb-4">
                <label for="proof" class="form-label fw-bold">Upload Bukti Pembayaran</label>
                <input
                  type="file"
                  id="proof"
                  class="form-control"
                  :class="{'is-invalid': form.errors.proof}"
                  accept="image/jpeg,image/png,image/jpg"
                  @change="handleProofUpload"
                >
                <div v-if="form.errors.proof" class="invalid-feedback">
                    {{ form.errors.proof }}
                </div>
                <div v-if="proofPreview" class="mt-3 text-center">
                  <img :src="proofPreview" class="img-fluid rounded shadow-sm" style="max-height: 200px; max-width: 100%;"/>
                </div>
              </div>

              <div v-if="paymentTimerValue" class="text-center text-danger mb-4">
                Pembayaran selesai dalam waktu : <span class="fw-bold">{{ paymentTimerValue }}</span>
              </div>
              <div class="d-flex flex-column flex-md-row justify-content-between mt-5">
                <button class="btn btn-outline-secondary px-4 mb-2 mb-md-0 w-100 w-md-auto" @click="goToStep('method')" :disabled="form.processing">
                 <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button class="btn btn-success px-4 w-100 w-md-auto" @click="handlePaymentConfirmed" :disabled="form.processing || !form.proof">
                   <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Sudah melakukan pembayaran
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <Notification
      :show="notification.show"
      :type="notification.type"
      :message="notification.message"
      @close="hideNotification"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Notification from '@/Components/Notification.vue';

const currentStep = ref('nominal');

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

const finalSelectedAmount = computed(() => {
  if (customAmountRaw.value && customAmountRaw.value >= 100000 && customAmountRaw.value % 100000 === 0) {
    return customAmountRaw.value;
  }
  return selectedNominalPackage.value ? selectedNominalPackage.value.idr : 0;
});

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

const availablePaymentMethods = ref([
  {
    id: 'BCA',
    name: 'Bank Central Asia (BCA)',
    description: 'transfer via BCA Mobile Banking atau Internet Banking',
    icon: 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia_logo.svg/2560px-Bank_Central_Asia_logo.svg.png',
    type: 'Transfer'
  },
]);
const selectedPaymentMethodId = ref(null);

const selectedPaymentMethodDetails = computed(() =>
  availablePaymentMethods.value.find(m => m.id === selectedPaymentMethodId.value)
);

const bcaVirtualAccount = ref('39012 76783 43336');
const paymentTimerValue = ref('');
let timerInterval = null;

const proofPreview = ref(null);

const form = useForm({
  amount: null,
  payment_method: null,
  proof: null,
});

const notification = ref({
  show: false,
  message: '',
  type: 'success',
});

const formatCurrency = (value) => {
  if (isNaN(parseFloat(value))) return '';
  return new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
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

const startPaymentTimer = (durationInSeconds) => {
  let timer = durationInSeconds;
  clearInterval(timerInterval);
  const updateTimer = () => {
    const minutes = String(Math.floor(timer / 60)).padStart(2, '0');
    const seconds = String(timer % 60).padStart(2, '0');
    paymentTimerValue.value = `${minutes}:${seconds}`;
    if (--timer < 0) {
      clearInterval(timerInterval);
      paymentTimerValue.value = 'Waktu Habis';
    }
  };
  updateTimer();
  timerInterval = setInterval(updateTimer, 1000);
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
      clearInterval(timerInterval);
      paymentTimerValue.value = '';
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
  background-color: #f0f2f5;
  min-height: calc(100vh - var(--navbar-height, 60px));
  padding-bottom: 2rem;
}

.top-up-header-bar {
  background-color: #0D6EFD;
}

.saldo-display {
  font-size: 0.9rem;
}

.card {
  border: none;
}

.nominal-option {
  transition: all 0.2s ease-in-out;
  border-width: 1px;
  font-size: 0.9rem;
}

.nominal-option:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.nominal-option.active {
  background-color: #0D6EFD;
  color: white;
  border-color: #0D6EFD;
}
.nominal-option:not(.active) {
    background-color: #fff;
    color: #0D6EFD;
}
.nominal-option:not(.active):hover {
    background-color: #eef5ff;
}

.input-group-text {
    background-color: #e9ecef;
}

.payment-method-option {
  cursor: pointer;
  transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.payment-method-option:hover {
  border-color: #0D6EFD !important;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
.payment-method-option.selected {
  border-color: #0D6EFD !important;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  background-color: #f8f9fa;
}

.form-check-input[type="radio"] {
  border-color: #adb5bd;
}
.form-check-input[type="radio"]:checked {
  background-color: #0D6EFD;
  border-color: #0D6EFD;
}

.form-control.is-invalid, .form-check-input.is-invalid ~ .form-check-label {
    border-color: #dc3545;
}
.invalid-feedback {
    width: 100%;
    margin-top: 0.25rem;
    font-size: .875em;
    color: #dc3545;
}
.invalid-feedback.d-block {
    display: block !important;
}

@media (max-width: 767.98px) {
  .card-body {
    padding: 1.5rem !important;
  }
  .nominal-option .fs-5 {
    font-size: 1.1rem !important;
  }
  .top-up-header-bar .container {
    flex-direction: column;
    align-items: flex-start !important;
  }
  .top-up-header-bar .h5 {
    margin-bottom: 0.5rem !important;
  }
}
@media (max-width: 575.98px) {
    .nominal-option {
        min-height: 70px;
        padding: 0.75rem !important;
    }
    .nominal-option .fs-5 {
        font-size: 1rem !important;
    }
}
</style>
