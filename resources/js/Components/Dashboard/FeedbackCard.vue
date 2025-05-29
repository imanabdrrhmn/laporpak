<template>
  <div class="card main-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">Ulasan Saya</h5>
      <a href="#" class="text-primary text-decoration-none small">Lihat Semua ></a>
    </div>
    <div class="card-body text-center">
      <div v-if="!feedbacks || feedbacks.length === 0" class="empty-state">
        <div class="empty-icon mb-3">
          <i class="bi bi-chat-text text-muted"></i>
        </div>
        <p class="text-muted mb-4">Belum ada ulasan</p>
        <Link href="/feedback" class="btn btn-primary btn-sm px-4">
          <i class="bi bi-plus-circle me-2"></i>
          Buat Ulasan
        </Link>
      </div>

      <div v-else>
        <div
          class="testimonial mb-3 text-start"
          v-for="(fb, index) in feedbacks"
          :key="index"
        >
          <div class="d-flex align-items-center mb-3">
            <img :src="fb.user.avatar_url" alt="avatar" class="rounded-circle me-3 avatar-img">
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
import { Link } from '@inertiajs/vue3'
defineProps({
  feedbacks: Array,
});
</script>

<style scoped>
.main-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  background-color: #ffffff;
  overflow: hidden;
  transition: all 0.3s ease;
  width: 100%; /* Mengganti nilai tetap dengan 100% */
  max-width: 100%; /* Membatasi lebar maksimum */
}

.main-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.card-header {
  background: #ffffff;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  padding: 1rem 1.5rem;
}

.card-header h5 {
  color: #2c3e50;
  font-weight: 600;
  font-size: clamp(1rem, 2.5vw, 1.25rem);
}

.card-body {
  padding: clamp(1rem, 3vw, 2rem) clamp(1rem, 3vw, 1.5rem);
}

.empty-state {
  padding: 1rem 0;
}

.empty-icon i {
  font-size: clamp(2rem, 5vw, 3rem);
  opacity: 0.6;
}

.btn-primary {
  background: #4285f4;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  padding: 0.5rem 1.5rem;
  font-size: clamp(0.875rem, 2vw, 1rem);
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
  padding: clamp(0.75rem, 2vw, 1.25rem);
  transition: all 0.3s ease;
}

.testimonial:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(66, 133, 244, 0.1);
  border-color: rgba(66, 133, 244, 0.2);
}

.avatar-img {
  width: clamp(40px, 10vw, 50px);
  height: clamp(40px, 10vw, 50px);
  object-fit: cover;
}

.text-primary {
  color: #4285f4 !important;
}

/* Responsive Design */
@media (max-width: 768px) {
  .card-header {
    padding: 0.75rem 1rem;
  }

  .card-body {
    padding: 1rem;
  }

  .avatar-img {
    width: 40px;
    height: 40px;
  }

  .testimonial {
    padding: 0.75rem;
  }

  .btn-primary {
    padding: 0.5rem 1rem;
  }
}

@media (max-width: 576px) {
  .card-header h5 {
    font-size: 1rem;
  }

  .card-body {
    padding: 0.5rem;
  }

  .avatar-img {
    width: 32px;
    height: 32px;
  }

  .testimonial {
    padding: 0.5rem;
  }
}
</style>