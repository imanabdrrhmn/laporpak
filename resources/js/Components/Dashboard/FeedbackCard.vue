<template>
  <div class="card main-card">
    <div class="card-header">
      <h5 class="fw-bold mb-0">Ulasan Saya</h5>
    </div>
    <div class="card-body">
      <div class="testimonial">
        <div class="d-flex align-items-center mb-3">
          <img :src="userAvatar" alt="avatar" class="rounded-circle me-3" width="50" height="50">
          <div>
            <strong>{{ namaUser }}</strong><br>
            <small class="text-muted">Kategori: {{ feedbackKategori }}</small>
          </div>
        </div>
        <div class="mb-2">
          <span class="text-warning fs-5">★★★★★</span>
        </div>
        <p class="mb-0">{{ feedbackText }}</p>
      </div>
      <a href="/feedback" class="btn btn-sm btn-outline-primary w-100 mt-3">
        Kasih Ulasan
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  namaUser: String,
  userAvatar: String,
  feedbackKategori: String,
  feedbackText: String
});
</script>

<style scoped>
.main-card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  height: 100%;
  transition: var(--transition);
  background-color: var(--card-bg);
}

.main-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.main-card .card-header {
  background: var(--card-bg);
  border-bottom: 1px solid rgba(0,0,0,0.05);
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
}

.main-card .card-header h5 {
  color: var(--text-color);
}

.testimonial {
  background: rgba(0,0,0,0.02);
  border-radius: 10px;
  padding: 1.25rem;
  transition: var(--transition);
}

.testimonial:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
</style>