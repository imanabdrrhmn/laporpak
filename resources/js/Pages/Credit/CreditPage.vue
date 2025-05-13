
<template>
  <AppLayout>
    <div class="container py-4">
      <div class="card shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body p-4">
          <h1 class="text-center mb-4 fw-bold">Top Up Credit</h1>
          
          <p class="text-center mb-5">
            Untuk menggunakan layanan verifikasi data, Anda perlu melakukan deposit awal
            sebesar IDR 100.000 Setelah deposit berhasil, saldo Anda dapat digunakan untuk
            berbagai jenis query verifikasi.
          </p>

          <div v-if="errorMessage" class="alert alert-danger" role="alert">
            {{ errorMessage }}
          </div>

          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="mb-4">
                <label for="depositAmount" class="form-label fw-bold">Jumlah Deposit (Minimal IDR 100.000)</label>
                <input 
                  type="number" 
                  class="form-control" 
                  id="depositAmount"
                  v-model="depositAmount"
                  :min="minDeposit"
                  @input="updateTotal"
                  :disabled="isLoading"
                >
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
                    <span>Rp{{ depositAmount.toLocaleString('id-ID') }}</span>
                  </div>
                  
                  <div class="d-flex justify-content-between mb-3">
                    <span>Biaya Admin</span>
                    <span>Rp. {{ adminFee }} (Gratis)</span>
                  </div>
                  
                  <div class="d-flex justify-content-between fw-bold">
                    <span>Total Dibayar</span>
                    <span>Rp.{{ totalPayment.toLocaleString('id-ID') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-5">
            <button 
              class="btn btn-outline-primary"
              @click="goBack"
              :disabled="isLoading"
            >
              Kembali ke beranda
            </button>
            <button 
              class="btn btn-primary"
              @click="proceedToPayment"
              :disabled="isLoading"
            >
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              {{ isLoading ? 'Memproses...' : 'Lanjutkan Pembayaran' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { Router } from '@inertiajs/inertia';

const depositAmount = ref(100000);
const paymentMethod = ref('BCA');
const isLoading = ref(false);
const errorMessage = ref('');
const paymentDetails = ref(null);

const minDeposit = 100000;
const adminFee = 0;
const totalPayment = ref(depositAmount.value);

onMounted(() => {
  // Check if there's an active session or any needed initial data
  checkUserSession();
});

const checkUserSession = async () => {
  try {
    const response = await axios.get('/api/user/check-session');
    if (!response.data.authenticated) {
      Router.push('/login');
    }
  } catch (error) {
    errorMessage.value = 'Failed to verify user session';
    console.error('Session check error:', error);
  }
};

const updateTotal = () => {
  // Make sure deposit amount is not less than minimum
  if (depositAmount.value < minDeposit) {
    depositAmount.value = minDeposit;
  }
  totalPayment.value = depositAmount.value;
};

const proceedToPayment = async () => {
  if (depositAmount.value < minDeposit) {
    errorMessage.value = `Deposit minimum adalah IDR ${minDeposit.toLocaleString('id-ID')}`;
    return;
  }

  errorMessage.value = '';
  isLoading.value = true;

  try {
    // Create payment request on the backend
    const response = await axios.post('/api/payments/create', {
      amount: depositAmount.value,
      paymentMethod: paymentMethod.value,
      type: 'topup'
    });

    paymentDetails.value = response.data;
    
    // Redirect to payment page or show payment instructions
    if (response.data.redirectUrl) {
      window.location.href = response.data.redirectUrl;
    } else if (response.data.paymentInstructions) {
      // Handle virtual account or bank transfer instructions
      router.push({
        name: 'payment.instructions',
        params: { reference: response.data.reference }
      });
    }
  } catch (error) {
    console.error('Payment processing error:', error);
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat memproses pembayaran';
  } finally {
    isLoading.value = false;
  }
};

const goBack = () => {
  // Navigation logic to return to previous page
  router.push('/dashboard');
};
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
</style>