<template>
  <div class="card main-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">Ulasan Saya</h5>
      <a href="#" class="text-primary text-decoration-none small">Lihat Semua ></a>
    </div>
    <div class="card-body text-center">
      <!-- Kondisi ketika belum ada ulasan -->
      <div v-if="!feedbacks || feedbacks.length === 0" class="empty-state">
        <div class="empty-icon mb-3">
          <i class="bi bi-chat-text text-muted" style="font-size: 3rem;"></i>
        </div>
        <p class="text-muted mb-4">Belum ada ulasan</p>
        <button class="btn btn-primary btn-sm px-4">
          <i class="bi bi-plus-circle me-2"></i>
          Buat Ulasan
        </button>
      </div>

      <!-- Kondisi ketika ada ulasan -->
      <div v-else>
        <div
          class="testimonial mb-3 text-start"
          v-for="(fb, index) in feedbacks"
          :key="index"
        >
          <div class="d-flex align-items-center mb-3">
            <img :src="fb.user.avatar_url" alt="avatar" class="rounded-circle me-3" width="50" height="50">
            <div>
              <strong>{{ fb.user.name }}</strong><br>
              <small class="text-muted">Kategori: {{ fb.kategori }}</small>
            </div>
          </div>
          <div class="mb-2">
            <span class="text-warning fs-5">{{ '★'.repeat(fb.rating) }}</span>
          </div>
          <p class="mb-0">{{ fb.message }}</p>
        </div>

        <a href="/feedback" class="btn btn-sm btn-outline-primary w-100 mt-3">
          Kasih Ulasan
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  feedbacks: Array,
});
</script>

<style scoped>
.main-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  height: 100%;
  transition: all 0.3s ease;
  background-color: #ffffff;
  overflow: hidden;
}

.main-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.main-card .card-header {
  background: #ffffff;
  border-bottom: 1px solid rgba(0,0,0,0.08);
  padding: 1.25rem 1.5rem;
}

.main-card .card-header h5 {
  color: #2c3e50;
  font-weight: 600;
  font-size: 1.25rem;
}

.card-body {
  padding: 2rem 1.5rem;
}

.empty-state {
  padding: 1rem 0;
}

.empty-icon {
  opacity: 0.6;
}

.btn-primary {
  background: #4285f4;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background: #3367d6;
  transform: translateY(-1px);
}

.testimonial {
  background: rgba(66, 133, 244, 0.02);
  border: 1px solid rgba(66, 133, 244, 0.1);
  border-radius: 12px;
  padding: 1.25rem;
  transition: all 0.3s ease;
}

.testimonial:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(66, 133, 244, 0.1);
  border-color: rgba(66, 133, 244, 0.2);
}

.text-primary {
  color: #4285f4 !important;
}

</style>