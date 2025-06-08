<template>
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3 p-md-4">
      <div class="row g-3 align-items-center">
        <!-- Beri Ulasan Button -->
        <div class="col-12 col-md-6 col-xl-3 order-1">
          <div v-if="isLoggedIn && contactVerified">
            <button class="btn btn-primary w-100 filter-btn" @click="$emit('open-feedback-modal', 'create')">
              <i class="bi bi-chat-square-text me-2"></i> Beri Ulasan
            </button>
          </div>
          <div v-else-if="isLoggedIn && !contactVerified">
            <button
              class="btn btn-warning w-100 filter-btn"
              disabled
              data-bs-toggle="tooltip"
              data-bs-placement="top"
              title="Verifikasi email atau nomor HP diperlukan untuk memberikan ulasan"
            >
              <i class="bi bi-exclamation-triangle me-2"></i> Verifikasi Diperlukan
            </button>
          </div>
        </div>

        <!-- Ulasan Saya Button -->
        <div class="col-12 col-md-6 col-xl-3 order-2" v-if="isLoggedIn">
          <button
            class="btn w-100 filter-btn"
            :class="isMyFeedbackActive ? 'btn-primary active' : 'btn-outline-primary'"
            @click="$emit('toggle-my-feedback')"
          >
            <i class="bi bi-person-lines-fill me-1"></i> Ulasan Saya
          </button>
        </div>

        <!-- Category Dropdown -->
        <div class="col-12 col-md-6 col-xl-3 order-3">
          <div class="input-group filter-input-group">
            <span class="input-group-text bg-primary text-white">
              <i class="bi bi-filter me-2"></i> Kategori
            </span>
            <select v-model="localSelectedCategory" class="form-select filter-select" @change="$emit('handle-category-change')">
              <option value="">Semua Kategori</option>
              <option value="Pelaporan">Pelaporan</option>
              <option value="Verifikasi">Verifikasi</option>
              <option value="Lapor Map">Lapor Map</option>
              <option value="Umum">Umum</option>
              <option value="Cari Laporan">Cari Laporan</option>
            </select>
          </div>
        </div>

        <!-- Sort Buttons -->
        <div class="col-12 col-md-6 col-xl-3 order-4">
          <div class="btn-group w-100 filter-btn-group">
            <button
              class="btn flex-grow-1 filter-btn"
              :class="sortBy === 'latest' ? 'btn-primary active' : 'btn-outline-primary'"
              @click="$emit('update:sortBy', 'latest')"
            >
              <i class="bi bi-clock-history me-1"></i> Terbaru
            </button>
            <button
              class="btn flex-grow-1 filter-btn"
              :class="sortBy === 'highestRating' ? 'btn-primary active' : 'btn-outline-primary'"
              @click="$emit('update:sortBy', 'highestRating')"
            >
              <i class="bi bi-star-fill me-1"></i> Rating Tertinggi
            </button>
          </div>
        </div>
      </div>

      <!-- Verification Warning -->
      <div v-if="isLoggedIn && !contactVerified" class="mt-3">
        <div class="alert alert-warning mb-0 py-2 filter-alert" role="alert">
          <small>
            <i class="bi bi-exclamation-triangle me-1"></i> Anda perlu verifikasi email atau nomor HP terlebih dahulu untuk memberi feedback.
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  isLoggedIn: Boolean,
  contactVerified: Boolean,
  isAdmin: Boolean,
  selectedCategory: String,
  sortBy: String,
  isMyFeedbackActive: Boolean,
});

const emit = defineEmits(['open-feedback-modal', 'toggle-my-feedback', 'handle-category-change', 'update:sortBy', 'update:selectedCategory', 'update:isMyFeedbackActive']);

const localSelectedCategory = computed({
  get: () => props.selectedCategory,
  set: (value) => emit('update:selectedCategory', value),
});
</script>

<style scoped>
.card {
  transition: all 0.3s ease;
  border-radius: 15px;
  background-color: #ffffff;
}

.card-body {
  padding: 1.5rem;
}

.filter-btn {
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  background-color: #0a58ca;
  color: white;
}

.filter-btn.active {
  background-color: #0a3d62;
  color: white;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.filter-input-group {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-input-group .input-group-text {
  background-color: #0d6efd;
  color: white;
  border: none;
  font-size: 0.9rem;
  padding: 0.5rem 0.75rem;
}

.filter-select {
  border: none;
  background-color: #f8f9fa;
  font-size: 0.9rem;
  padding: 0.5rem;
  transition: all 0.2s ease;
}

.filter-select:focus {
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  background-color: #ffffff;
}

.filter-btn-group {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.filter-btn-group .btn {
  border: none;
  font-size: 0.9rem;
  padding: 0.5rem;
  transition: all 0.2s ease;
}

.filter-btn-group .btn-outline-primary {
  background-color: #f8f9fa;
  color: #0d6efd;
}

.filter-btn-group .btn-primary {
  background-color: #0d6efd;
  color: white;
}

.filter-btn-group .btn-outline-primary:hover:not(:disabled) {
  background-color: #0d6efd;
  color: white;
}

.filter-alert {
  border-radius: 8px;
  font-size: 0.85rem;
  background-color: #fff3cd;
  color: #856404;
  padding: 0.5rem 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  background-color: #0d6efd;
}

.btn-primary:hover:not(:disabled),
.btn-primary.active {
  background-color: #0d6efd;
  color: white;
}

@media (max-width: 767.98px) {
  .card-body {
    padding: 1rem;
  }

  .filter-btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.75rem;
  }

  .filter-input-group .input-group-text {
    font-size: 0.85rem;
    padding: 0.4rem 0.75rem;
  }

  .filter-select {
    font-size: 0.85rem;
    padding: 0.4rem;
  }

  .filter-btn-group .btn {
    font-size: 0.85rem;
    padding: 0.4rem;
  }

  .filter-alert {
    font-size: 0.8rem;
    padding: 0.4rem 0.75rem;
  }
}

@media (max-width: 575.98px) {
  .card-body {
    padding: 0.75rem;
  }

  .filter-btn {
    font-size: 0.8rem;
    padding: 0.35rem 0.5rem;
  }

  .filter-input-group .input-group-text {
    font-size: 0.8rem;
  }

  .filter-select {
    font-size: 0.8rem;
  }

  .filter-btn-group .btn {
    font-size: 0.8rem;
  }

  .filter-alert {
    font-size: 0.75rem;
  }
}

@media (max-width: 767.98px) {
  .btn {
    padding: 0.5rem 0.75rem;
  }

  .input-group-text {
    font-size: 0.875rem;
  }

  .input-group .form-select {
    font-size: 0.875rem;
  }

  .card {
    margin-bottom: 0.75rem;
  }
}

@media (max-width: 991.98px) {
  .row {
    margin: 0 -0.5rem;
  }

  .col-12,
  .col-sm-6,
  .col-md-4,
  .col-lg-4 {
    padding: 0 0.5rem;
  }
}
</style>