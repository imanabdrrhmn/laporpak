<template>
    <div 
      class="modal fade" 
      :class="{ show: isVisible }" 
      :style="{ display: isVisible ? 'block' : 'none' }" 
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ title }}</h5>
            <button 
              type="button" 
              class="btn-close" 
              @click="$emit('close')"
            ></button>
          </div>
          
          <div class="modal-body">
            <div class="terms-item">
              <h6>1. Kebenaran Data yang Dimasukkan</h6>
              <ul>
                <li>Pengguna wajib mengisi informasi dengan data asli dan sesuai dengan identitas resmi (seperti NIK, nama lengkap, dan nomor handphone).</li>
                <li>Kesalahan dalam pengisian dapat menyebabkan hasil verifikasi tidak ditemukan.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>2. Tanpa Biaya Jika Data Tidak Valid</h6>
              <ul>
                <li>Pengguna tidak akan dikenakan biaya apabila hasil verifikasi menyatakan data tidak valid, tidak ditemukan, atau gagal diverifikasi.</li>
                <li>Biaya hanya akan dikenakan jika proses verifikasi berhasil dan data ditemukan sesuai permintaan layanan.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>3. Biaya dan Jenis Layanan</h6>
              <ul>
                <li>Setiap layanan memiliki biaya yang tercantum, misalnya "IDR 4,800 / Success", artinya hanya akan dibebankan jika verifikasi sukses.</li>
                <li>Layanan dengan model "/Query" berarti akan dikenakan biaya saat permintaan dilakukan, terlepas dari hasilnya.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>4. Persetujuan Penggunaan Data</h6>
              <ul>
                <li>Dengan mencentang persetujuan, pengguna memberikan izin kepada penyedia layanan untuk memproses dan mengirimkan data ke pihak ketiga terpercaya untuk keperluan verifikasi.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>5. Keamanan dan Privasi</h6>
              <ul>
                <li>Data pengguna akan dijaga kerahasiaannya dan tidak disalahgunakan.</li>
                <li>Informasi akan dienkripsi dan hanya digunakan untuk kepentingan verifikasi.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>6. Layanan yang Belum Aktif</h6>
              <ul>
                <li>Layanan yang berstatus "Coming Soon" belum tersedia dan tidak bisa digunakan hingga diumumkan resmi.</li>
              </ul>
            </div>
  
            <div class="terms-item">
              <h6>7. Persetujuan Final</h6>
              <ul>
                <li>Dengan menekan tombol "Cek Sekarang", pengguna menyatakan setuju terhadap seluruh syarat dan ketentuan ini.</li>
              </ul>
            </div>
          </div>
          
          <div class="modal-footer">
            <div class="form-check mb-3 w-100">
              <input 
                class="form-check-input" 
                type="checkbox" 
                v-model="agreed" 
                id="agreeCheck"
              >
              <label class="form-check-label" for="agreeCheck">
                Saya telah membaca dan menyetujui syarat dan ketentuan di atas
              </label>
            </div>
            <div class="d-flex gap-2">
              <button 
                type="button" 
                class="btn btn-secondary" 
                @click="$emit('close')"
              >
                Tutup
              </button>
              <button 
                type="button" 
                class="btn btn-primary" 
                :disabled="!agreed" 
                @click="handleAccept"
              >
                Setuju & Lanjutkan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Backdrop -->
    <div 
      class="modal-backdrop fade" 
      :class="{ show: isVisible }" 
      v-if="isVisible"
      @click="$emit('close')"
    ></div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const props = defineProps({
    isVisible: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Syarat dan Ketentuan'
    },
    content: {
      type: String,
      default: ''
    }
  })
  
  const emit = defineEmits(['close', 'accept'])
  
  const agreed = ref(false)
  
  const handleAccept = () => {
    if (agreed.value) {
      emit('accept')
      agreed.value = false // Reset checkbox
    }
  }
  </script>
  
  <style scoped>
  .modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }
  
  .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0;
  }
  
  .modal-body {
    max-height: 500px;
    overflow-y: auto;
    padding: 20px;
  }
  
  .terms-item {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .terms-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }
  
  .terms-item h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
  }
  
  .terms-item ul {
    margin-bottom: 0;
    padding-left: 20px;
  }
  
  .terms-item li {
    margin-bottom: 5px;
    line-height: 1.5;
    color: #6c757d;
  }
  
  .modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0 0 10px 10px;
  }
  
  .form-check-label {
    font-size: 14px;
    color: #495057;
  }
  
  .btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  /* Custom scrollbar */
  .modal-body::-webkit-scrollbar {
    width: 6px;
  }
  
  .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }
  
  .modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }
  
  .modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
  </style>