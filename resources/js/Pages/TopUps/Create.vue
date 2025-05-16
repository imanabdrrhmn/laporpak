<template>
  <AppLayout>
    <div class="container py-4">
      <div class="card shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
          <h1 class="text-center mb-4 fw-bold">Top Up Credit</h1>

          <p class="text-center mb-4">
            Deposit awal minimal IDR 100.000 untuk menggunakan layanan verifikasi.
          </p>

          <form @submit.prevent="showPaymentSummary">
            <div class="mb-4">
              <label class="form-label fw-bold">Jumlah Deposit</label>
              <div class="btn-group d-flex flex-wrap gap-2">
                <button
                  v-for="amount in depositOptions"
                  :key="amount"
                  type="button"
                  class="btn"
                  :class="depositAmount === amount ? 'btn-primary' : 'btn-outline-primary'"
                  @click="selectDepositAmount(amount)"
                >
                  Rp {{ formatCurrency(amount) }}
                </button>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold">Metode Pembayaran</label>
              <div class="form-check" v-for="method in paymentMethods" :key="method">
                <input
                  class="form-check-input"
                  type="radio"
                  :id="method"
                  :value="method"
                  v-model="paymentMethod"
                  required
                />
                <label class="form-check-label" :for="method">{{ method }}</label>
              </div>
            </div>

            <!-- INFO REKENING DINAMIS -->
            <div class="alert alert-info">
              <template v-if="paymentInfo">
                <p class="mb-1 fw-bold">Silakan transfer ke rekening berikut:</p>
                <p class="mb-0">Bank: {{ paymentInfo.bank }}</p>
                <p class="mb-0">No. Rekening: {{ paymentInfo.account }}</p>
                <p class="mb-0">Atas Nama: {{ paymentInfo.name }}</p>
              </template>
              <template v-else>
                <p class="mb-0 text-danger fw-bold">Metode pembayaran ini belum tersedia. Silakan pilih metode lain.</p>
              </template>
            </div>

            <div class="mb-4">
              <label for="proof" class="form-label fw-bold">Upload Bukti Pembayaran</label>
              <input
                type="file"
                id="proof"
                class="form-control"
                accept="image/*"
                @change="handleProofUpload"
                required
              />
              <div v-if="proofPreview" class="mt-3 text-center">
                <img
                  :src="proofPreview"
                  class="img-fluid rounded shadow-sm preview-image"
                  :class="{ zoomed: isZoomed }"
                  style="max-height: 200px; cursor: zoom-in;"
                  @click="toggleZoom"
                />

                <!-- Overlay fullscreen untuk zoom -->
                <div v-if="isZoomed" class="overlay" @click="toggleZoom">
                  <img
                    :src="proofPreview"
                    class="zoomed-image"
                    alt="Zoomed proof"
                  />
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="!isFormValid"
              >
                Lanjutkan Pembayaran
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal Ringkasan Pembayaran -->
      <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="modalLabel">Ringkasan Pembayaran</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between">
                  <span>Jumlah Deposit</span>
                  <span>Rp {{ formatCurrency(depositAmount) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Metode Pembayaran</span>
                  <span>{{ paymentMethod }}</span>
                </li>
              </ul>
              <div class="text-end">
                <button class="btn btn-success" @click="submitTopUp" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  Konfirmasi & Bayar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Toast Notifikasi Sukses -->
      <div
        class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        ref="successToast"
        style="z-index: 1100;"
      >
        <div class="d-flex">
          <div class="toast-body">
            Top up berhasil dikirim!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>

      <!-- Toast Notifikasi Error -->
      <div
        class="toast align-items-center text-bg-danger border-0 position-fixed bottom-0 end-0 m-3"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        ref="errorToast"
        style="z-index: 1100;"
      >
        <div class="d-flex">
          <div class="toast-body" ref="errorToastMessage">
            <!-- error message akan diset dinamis -->
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { Modal, Toast } from 'bootstrap';

const router = useRouter();
const depositAmount = ref(100000);
const depositOptions = [100000, 200000, 300000, 500000];
const paymentMethods = ['BCA', 'DANA', 'QRIS'];
const paymentMethod = ref('BCA');
const proofFile = ref(null);
const proofPreview = ref(null);
const isLoading = ref(false);
const modalRef = ref(null);
const successToast = ref(null);
const errorToast = ref(null);
const errorToastMessage = ref(null);

let modalInstance = null;
let toastSuccessInstance = null;
let toastErrorInstance = null;

// Format ke mata uang IDR
const formatCurrency = (value) =>
  new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);

// Info rekening per metode pembayaran
const paymentInfoMap = {
  BCA: {
    bank: 'BCA',
    account: '1234567890',
    name: 'PT. Layanan Digital',
  },
  DANA: null,
  QRIS: null,
};

const paymentInfo = computed(() => {
  return paymentInfoMap[paymentMethod.value] || null;
});

const selectDepositAmount = (amount) => {
  depositAmount.value = amount;
};

const handleProofUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    proofFile.value = file;
    proofPreview.value = URL.createObjectURL(file);
  }
};

const isFormValid = computed(() =>
  depositAmount.value >= 100000 && paymentMethod.value && proofFile.value
);

const showPaymentSummary = () => {
  if (!isFormValid.value) {
    showErrorToast('Harap lengkapi semua kolom.');
    return;
  }
  if (!modalInstance) {
    modalInstance = new Modal(modalRef.value);
  }
  modalInstance.show();
};

const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

const resetForm = () => {
  depositAmount.value = 100000;
  paymentMethod.value = 'BCA';
  proofFile.value = null;
  proofPreview.value = null;

  const inputFile = document.getElementById('proof');
  if (inputFile) inputFile.value = '';
};

const showErrorToast = (message) => {
  if (errorToastMessage.value) {
    errorToastMessage.value.textContent = message;
  }
  toastErrorInstance?.show();
};

onMounted(() => {
  if (successToast.value) {
    toastSuccessInstance = new Toast(successToast.value);
  }
  if (errorToast.value) {
    toastErrorInstance = new Toast(errorToast.value);
  }
});

const submitTopUp = async () => {
  isLoading.value = true;
  try {
    const formData = new FormData();
    formData.append('amount', depositAmount.value);
    formData.append('payment_method', paymentMethod.value);
    formData.append('proof', proofFile.value);

    await axios.post('/top-ups/create', formData);

    closeModal();
    toastSuccessInstance?.show();
    resetForm();

    setTimeout(() => {
      router.push({ name: 'top-ups.index' }).catch(() => {});
    }, 2000);
  } catch (error) {
    resetForm();
    showErrorToast(error.response?.data?.message || 'Gagal mengirim top up');
  } finally {
    isLoading.value = false;
  }
};


const isZoomed = ref(false);

const toggleZoom = () => {
  isZoomed.value = !isZoomed.value;
};
</script>

<style scoped>
.btn-group .btn {
  flex: 1 1 auto;
  min-width: 100px;
}

.preview-image.zoomed {
  cursor: zoom-out;
}

/* Overlay fullscreen saat zoom aktif */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0,0,0,0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1200;
  cursor: zoom-out;
}

.zoomed-image {
  max-width: 90vw;
  max-height: 90vh;
  border-radius: 0.5rem;
  user-select: none;
}

</style>
