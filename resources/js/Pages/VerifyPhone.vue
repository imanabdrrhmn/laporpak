<template>
  <AppLayout>
    <Head title="Verifikasi Nomor" />
    <div class="page-wrapper">
      <div class="card-container">
        <div class="card">
          <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="whatsapp-icon">
              <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
          </div>

          <h1 class="title">Verifikasi Nomor WhatsApp</h1>

          <div class="content">
            <div v-if="componentState === 'initial'">
              <p class="description">
                Klik tombol di bawah untuk mendapatkan kode verifikasi (OTP) melalui WhatsApp.
              </p>
              <div class="button-group">
                <button type="button" class="verify-button" @click="getWhatsAppTrigger" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner"></span>
                  <span v-else>Kirim Kode Verifikasi</span>
                </button>
              </div>
            </div>

            <div v-if="componentState === 'wating_for_code'">
              <p class="description">
                Pesan pemicu telah dibuat. Klik link di bawah untuk membuka WhatsApp dan kirim pesan tersebut, lalu masukkan kode OTP yang Anda terima.
              </p>

              <a :href="whatsAppUrl" target="_blank" class="whatsapp-link-button">Buka WhatsApp untuk Kirim Pesan</a>
              
              <form @submit.prevent="submit" class="otp-form">
                <div class="input-group">
                  <label for="code" class="input-label">Kode Verifikasi</label>
                  <input
                    id="code" type="text" inputmode="numeric" pattern="[0-9]*"
                    v-model="form.code" class="otp-input" maxlength="6"
                    autocomplete="one-time-code" placeholder="000000"
                    @input="validateNumericInput" required
                  />
                  <div v-if="form.errors.code" class="error-message">
                    {{ form.errors.code }}
                  </div>
                </div>

                <div class="button-group">
                  <button type="submit" class="verify-button" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner"></span>
                    <span v-else>Verifikasi</span>
                  </button>
                </div>
              </form>
            </div>

            <div v-if="successMessage" class="status-container success">
               <span class="status-text">{{ successMessage }}</span>
            </div>
            <div v-if="errorMessage" class="status-container error">
               <span class="status-text">{{ errorMessage }}</span>
            </div>

            <p class="help-text">
              Butuh bantuan? <a href="#" class="help-link">Hubungi support</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router, usePage, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

// === STATE MANAGEMENT BARU ===
const componentState = ref('initial'); 
const isLoading = ref(false);
const whatsAppUrl = ref(''); 

// State yang sudah ada
const form = useForm({ code: '' });
const successMessage = ref('');
const errorMessage = ref('');

// === FUNGSI BARU: Untuk mendapatkan link pemicu dari backend ===
const getWhatsAppTrigger = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('whatsapp/generate-trigger');
    const { token, phone_number } = response.data;

    const text = `Verifikasi akun saya dengan kode: ${token}`;
    
    whatsAppUrl.value = `https://wa.me/${phone_number}?text=${encodeURIComponent(text)}`;

    componentState.value = 'wating_for_code';
    successMessage.value = 'Link WhatsApp berhasil dibuat!';

  } catch (e) {
    console.error('Gagal mendapatkan trigger WhatsApp:', e);
    errorMessage.value = 'Gagal membuat link verifikasi. Coba beberapa saat lagi.';
  } finally {
    isLoading.value = false;
  }
};

const validateNumericInput = () => {
  form.code = form.code.replace(/[^0-9]/g, '');
};

const submit = () => {
  errorMessage.value = '';
  successMessage.value = '';

  form.post('/phone/verify', {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Verifikasi berhasil! Anda akan diarahkan...';
      errorMessage.value = '';
      setTimeout(() => router.visit('/dashboard'), 1500);
    },
    onError: (errors) => {
      errorMessage.value = errors.code || 'Kode salah atau sudah kedaluwarsa.';
    }
  });
};

// Layout Inertia
const page = usePage();
page.layout = AppLayout;
</script>

<style scoped>
/* Reset and base styles */
* { box-sizing: border-box; }

.page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.card-container { width: 100%; max-width: 400px; }

.card {
  background: #ffffff !important;
  border-radius: 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  text-align: center;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.icon-container { margin-bottom: 1.5rem; }
.whatsapp-icon { width: 3rem; height: 3rem; color: #25D366; }
.title { font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0; }
.content { display: flex; flex-direction: column; gap: 1rem; }
.description { color: #6b7280; font-size: 0.95rem; line-height: 1.5; margin: 0 0 0.5rem 0; }
.input-group { display: flex; flex-direction: column; gap: 0.5rem; text-align: left; }
.input-label { color: #374151; font-size: 0.875rem; font-weight: 600; }
.otp-input {
  width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 0.5rem;
  font-size: 1.125rem; letter-spacing: 0.25rem; text-align: center; font-weight: 600;
  color: #111827; background-color: #ffffff; transition: all 0.2s ease;
}
.otp-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.error-message { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
.status-container { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; }
.status-container.success { background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }
.status-container.error { background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
.status-text { font-weight: 500; }
.button-group { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem; }
.verify-button, .whatsapp-link-button {
  width: 100%; padding: 0.75rem 1rem; background: #0d6efd; color: white; border: none;
  border-radius: 0.5rem; font-size: 0.95rem; font-weight: 600; cursor: pointer;
  transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;
  min-height: 2.75rem; text-decoration: none;
}
.verify-button:hover:not(:disabled), .whatsapp-link-button:hover {
  transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  background-color: #0b5ed7; border-color: #0a58ca;
}
.verify-button:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.spinner {
  width: 1rem; height: 1rem; border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%; border-top-color: white; animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.help-text { color: #9ca3af; font-size: 0.875rem; margin: 0.5rem 0 0 0; line-height: 1.4; }
.help-link { color: #3b82f6; text-decoration: none; font-weight: 500; }
.help-link:hover { text-decoration: underline; }
.otp-form { margin-top: 1rem; }
</style>