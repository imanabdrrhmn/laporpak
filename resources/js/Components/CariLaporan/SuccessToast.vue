<template>
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div 
      id="successToast" 
      class="toast" 
      role="alert" 
      aria-live="assertive" 
      aria-atomic="true"
      data-bs-delay="3000"
    >
      <div class="toast-header bg-success text-white">
        <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
        <strong class="me-auto">Berhasil</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        Laporan berhasil dikirim. Terima kasih atas kontribusi Anda!
      </div>
    </div>
  </div>
</template>

<script setup>
// Tidak ada logika, hanya presentational
</script>

<style scoped>
/* Tidak ada gaya spesifik, karena posisi diatur inline */
</style>