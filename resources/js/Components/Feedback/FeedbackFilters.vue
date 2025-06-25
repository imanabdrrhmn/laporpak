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

        <!-- Category Dropdown - PERBAIKAN UTAMA -->
        <div class="col-12 col-md-6 col-xl-3 order-3">
          <div class="input-group filter-input-group">
            <span class="input-group-text bg-primary text-white category-label">
              <span class="category-text">Kategori</span>
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
              type="button"
              class="btn flex-grow-1 filter-btn"
              :class="sortBy === 'latest' ? 'btn-primary active' : 'btn-outline-primary'"
              @click.prevent="handleSortChange('latest')"
            >
              <i></i> Terbaru
            </button>
            <button
              type="button"
              class="btn flex-grow-1 filter-btn"
              :class="sortBy === 'highestRating' ? 'btn-primary active' : 'btn-outline-primary'"
              @click.prevent="handleSortChange('highestRating')"
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

// Fungsi untuk menangani perubahan sort
const handleSortChange = (sortType) => {
  console.log('Sort changed to:', sortType); // Debug log
  emit('update:sortBy', sortType);
};
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
  cursor: pointer;
  user-select: none;
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

/* PERBAIKAN UTAMA - Category Label Styling */
.category-label {
  background-color: #0d6efd;
  color: white;
  border: none;
  font-size: 0.9rem;
  padding: 0.5rem 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  min-width: fit-content;
  white-space: nowrap;
}

.category-label i {
  flex-shrink: 0;
}

.category-text {
  display: inline-block;
}

.filter-select {
  border: none;
  background-color: #f8f9fa;
  font-size: 0.9rem;
  padding: 0.5rem;
  transition: all 0.2s ease;
  flex: 1;
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
  cursor: pointer;
  pointer-events: auto;
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

.btn-group {
  position: relative;
  z-index: 1;
}

/* Responsive Design - Mobile First */
@media (max-width: 575.98px) {
  .card-body {
    padding: 0.75rem;
  }

  .filter-btn {
    font-size: 0.8rem;
    padding: 0.35rem 0.5rem;
  }

  .category-label {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
    gap: 0.25rem;
  }

  .category-text {
    display: none; /* Sembunyikan text "Kategori" di mobile */
  }

  .filter-select {
    font-size: 0.8rem;
    padding: 0.4rem;
  }

  .filter-btn-group .btn {
    font-size: 0.8rem;
    padding: 0.4rem;
  }

  .filter-alert {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .card-body {
    padding: 1rem;
  }

  .filter-btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.75rem;
  }

  .category-label {
    font-size: 0.85rem;
    padding: 0.45rem 0.7rem;
    gap: 0.4rem;
  }

  .filter-select {
    font-size: 0.85rem;
    padding: 0.45rem;
  }

  .filter-btn-group .btn {
    font-size: 0.85rem;
    padding: 0.45rem;
  }

  .filter-alert {
    font-size: 0.8rem;
    padding: 0.45rem 0.75rem;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .row {
    margin: 0 -0.5rem;
  }

  .col-12,
  .col-md-6 {
    padding: 0 0.5rem;
  }
}

@media (min-width: 992px) {
  .category-text {
    display: inline-block; /* Tampilkan text "Kategori" di desktop */
  }
}
</style>