<template>
  <div class="access-denied-container text-center py-5 px-3">
    <div class="icon-wrapper mb-4">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="icon animate-pulse"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.5"
        aria-hidden="true"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    </div>
    <h3 class="mb-3 fw-bold text-danger">Akses Ditolak</h3>
    <p class="text-muted fs-5 mx-auto description">
      Anda tidak memiliki akses untuk halaman ini.<br />
      Silakan hubungi admin untuk mendapatkan akses.
    </p>
    <button class="btn btn-outline-danger mt-4" @click="goBack">
      <i class="bi bi-arrow-left me-2"></i> Kembali
    </button>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const goBack = () => router.visit('/');

</script>

<style scoped>
.access-denied-container {
  background: linear-gradient(135deg, #f8d7da 0%, #ffffff 100%);
  border-radius: 12px;
  max-width: 500px;
  margin: 0 auto;
  padding: 3rem 2rem;
  box-shadow: 0 10px 30px rgba(220, 53, 69, 0.1);
  transition: all 0.3s ease;
  animation: fadeIn 0.4s ease-out;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  color: #dc3545;
  margin: 0 auto;
  background: rgba(220, 53, 69, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon {
  width: 50px;
  height: 50px;
}

.description {
  line-height: 1.6;
}

.btn {
  font-weight: 600;
  padding: 0.6rem 1.5rem;
  border-radius: 50px;
}

/* Simple animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
