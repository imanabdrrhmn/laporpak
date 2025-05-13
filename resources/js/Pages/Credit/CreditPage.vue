<template>
  <AppLayout>
    <div class="container py-4">
      <div class="card shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
          <h1 class="text-center mb-4 fw-bold">Top Up Credit</h1>
          
          <p class="text-center mb-5">
            Untuk menggunakan layanan verifikasi data, Anda perlu melakukan deposit awal
            sebesar IDR 100.000. Setelah deposit berhasil, saldo Anda dapat digunakan untuk
            berbagai jenis query verifikasi.
          </p>

          <div v-if="errorMessage" class="alert alert-danger" role="alert">
            {{ errorMessage }}
          </div>

          <form @submit.prevent="proceedToPayment">
            <div class="row">
              <div class="col-md-6 mb-4">
                <div class="mb-4">
                  <label class="form-label fw-bold">Jumlah Deposit (Minimal IDR 100.000)</label>
                  <div class="btn-group d-flex flex-wrap gap-2" role="group">
                    <button
                      v-for="amount in depositOptions"
                      :key="amount"
                      type="button"
                      class="btn"
                      :class="depositAmount === amount ? 'btn-primary' : 'btn-outline-primary'"
                      @click="selectDepositAmount(amount)"
                      :disabled="isLoading"
                    >
                      Rp {{ formatCurrency(amount) }}
                    </button>
                  </div>
                  <div v-if="amountError" class="text-danger mt-1 small">
                    {{ amountError }}
                  </div>
                </div>

                <div>
                  <label class="form-label fw-bold">Metode Pembayaran</label>
                  <div class="form-check">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      name="paymentMethod" 
                      id="bankBCA" 
                      value="BCA" 
                      v-model="paymentMethod"
                      :disabled="isLoading"
                      required
                    >
                    <label class="form-check-label" for="bankBCA">
                      Transfer BANK BCA
                    </label>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title mb-4">Ringkasan Pembayaran</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                      <span>Deposit Saldo</span>
                      <span>Rp {{ formatCurrency(depositAmount) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                      <span>Biaya Admin</span>
                      <span>Rp {{ formatCurrency(adminFee) }} (Gratis)</span>
                    </div>
                    
                    <div class="d-flex justify-content-between fw-bold">
                      <span>Total Dibayar</span>
                      <span>Rp {{ formatCurrency(totalPayment) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5">
              <button 
                type="button"
                class="btn btn-outline-primary"
                @click="goBack"
                :disabled="isLoading"
              >
                Kembali ke beranda
              </button>
              <button 
                type="submit"
                class="btn btn-primary"
                :disabled="isLoading || !isFormValid"
              >
                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                {{ isLoading ? 'Memproses...' : 'Lanjutkan Pembayaran' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { Router } from '@inertiajs/inertia';

// State
const router = useRouter();
const depositAmount = ref(100000);
const paymentMethod = ref('BCA');
const isLoading = ref(false);
const errorMessage = ref('');
const paymentDetails = ref(null);
const amountError = ref('');

// Constants
const MIN_DEPOSIT = 100000;
const ADMIN_FEE = 0;
const depositOptions = [100000, 200000, 300000, 500000, 1000000];

// Computed properties
const totalPayment = computed(() => depositAmount.value + ADMIN_FEE);

const isFormValid = computed(() => {
  return depositAmount.value >= MIN_DEPOSIT && paymentMethod.value && !amountError.value;
});

// Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
};

const selectDepositAmount = (amount) => {
  depositAmount.value = amount;
  amountError.value = amount < MIN_DEPOSIT
    ? `Deposit minimum adalah IDR ${formatCurrency(MIN_DEPOSIT)}`
    : '';
};

const checkUserSession = async () => {
  try {
    const response = await axios.get('/api/user/check-session');
    if (!response.data.authenticated) {
      Router.push('/login');
    }
  } catch (error) {
    errorMessage.value = 'Gagal memverifikasi sesi pengguna';
    console.error('Session check error:', error);
  }
};

const proceedToPayment = async () => {
  if (!isFormValid.value) {
    errorMessage.value = 'Harap lengkapi formulir dengan benar';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await axios.post('/api/payments/create', {
      amount: depositAmount.value,
      paymentMethod: paymentMethod.value,
      type: 'topup'
    });

    paymentDetails.value = response.data;

    if (response.data.redirectUrl) {
      window.location.href = response.data.redirectUrl;
    } else if (response.data.paymentInstructions) {
      router.push({
        name: 'payment.instructions',
        params: { reference: response.data.reference }
      });
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat memproses pembayaran';
    console.error('Payment processing error:', error);
  } finally {
    isLoading.value = false;
  }
};

const goBack = () => {
  router.push('/dashboard');
};

// Lifecycle
onMounted(() => {
  checkUserSession();
});
</script>

<style scoped>
.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.spinner-border {
  width: 1rem;
  height: 1rem;
}

.alert {
  margin-bottom: 1.5rem;
}

.btn-group .btn {
  flex: 1 1 auto;
  min-width: 100px;
}

.text-danger {
  font-size: 0.875rem;
}
</style>