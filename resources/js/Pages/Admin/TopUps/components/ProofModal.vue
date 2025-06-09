<template>
  <!-- Menggunakan v-if untuk mengontrol visibilitas berdasarkan prop 'isVisible' -->
  <div v-if="isVisible" class="modal-overlay" @click="close">
    <div class="modal-container" @click.stop>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bukti Pembayaran</h5>
          <!-- Tombol close meng-emit event 'close' -->
          <button
            type="button"
            class="btn-close"
            aria-label="Tutup"
            @click="close"
          ></button>
        </div>
        <div class="modal-body p-0 text-center">
          <!-- Menampilkan gambar dari prop 'proofUrl' -->
          <img v-if="proofUrl" :src="proofUrl" alt="Bukti Pembayaran" class="img-fluid w-100" />
          <p v-else class="p-4 text-muted">URL gambar tidak valid.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Menggunakan <script setup> untuk sintaks yang lebih modern dan ringkas
import { onMounted, onUnmounted } from 'vue';

// Mendefinisikan props yang diterima dari parent
const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true,
  },
  proofUrl: {
    type: String,
    default: ''
  },
});

// Mendefinisikan event yang akan di-emit ke parent
const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

// Menangani tombol Escape untuk menutup modal
const handleKeydown = (e) => {
  if (props.isVisible && e.key === 'Escape') {
    close();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
/* Style dari kode yang kamu berikan sebelumnya sudah baik. */
/* Style ini akan memastikan modal tampil dengan baik. */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000; /* Pastikan di atas elemen lain */
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 0.5rem; /* Bootstrap 5 default */
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
  width: 100%;
  max-width: 800px; /* Modal lebih lebar untuk gambar */
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.modal-content {
  /* Tidak perlu style khusus jika modal-container sudah di-styling */
}

.modal-header {
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 1.25rem;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 500;
}

.modal-body {
  overflow-y: auto; /* Jika gambar sangat tinggi */
  display: flex; /* Untuk center gambar jika lebih kecil */
  justify-content: center;
  align-items: center;
  background-color: #f8f9fa; /* Latar belakang body modal */
}

.img-fluid {
  max-height: calc(90vh - 120px); /* Batasi tinggi gambar agar footer dan header terlihat */
  object-fit: contain;
}
</style>
