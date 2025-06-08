<template>
  <transition-group name="fade" tag="div" class="feedback-container">
    <div v-if="isLoading" class="row g-3">
      <div v-for="n in 3" :key="n" class="col-12 col-sm-6 col-lg-4 mb-3">
        <div class="card border-0 shadow-lg h-100">
          <div class="card-body p-3">
            <div class="d-flex align-items-center mb-3">
              <div class="skeleton skeleton-circle me-3"></div>
              <div class="skeleton skeleton-name"></div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="skeleton skeleton-badge"></span>
              <span class="skeleton skeleton-small-text"></span>
            </div>
            <div class="skeleton skeleton-stars mb-3"></div>
            <div class="skeleton skeleton-text mb-3"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div v-if="feedbacks.length > 0" class="row g-3">
        <div v-for="feedback in feedbacks" :key="feedback.id" class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="card border-0 shadow-lg rounded-lg h-100 feedback-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <img
                  v-if="feedback.user.avatar_url"
                  :src="feedback.user.avatar_url"
                  alt="Avatar"
                  class="rounded-circle me-3"
                  style="width: 40px; height: 40px; object-fit: cover;"
                />
                <div
                  v-else
                  class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                  style="width: 40px; height: 40px; font-size: 0.9rem;"
                >
                  {{ feedback.user.name.charAt(0) }}{{ feedback.user.name.split(' ').length > 1 ? feedback.user.name.split(' ')[1].charAt(0) : '' }}
                </div>
                <h5 class="card-title mb-0 fs-6 text-truncate" style="max-width: 150px;">{{ feedback.user.name }}</h5>

                <div class="ms-auto position-relative">
                  <button
                    class="btn btn-sm btn-light rounded-circle"
                    @click.stop="$emit('toggle-dropdown', feedback.id)"
                  >
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul
                    v-if="activeDropdownId === feedback.id"
                    class="dropdown-menu show position-absolute end-0 mt-2"
                    style="display: block; z-index: 1000;"
                  >
                    <li>
                      <a class="dropdown-item" href="#" @click.prevent="$emit('open-detail-modal', feedback)">
                        <i class="bi bi-eye me-2"></i> Detail
                      </a>
                    </li>
                    <li v-if="isLoggedIn && (page.props.auth?.user?.id === feedback.user_id || isAdmin)">
                      <a class="dropdown-item" href="#" @click.prevent="$emit('open-feedback-modal', 'edit', feedback)">
                        <i class="bi bi-pencil-square me-2"></i> Edit
                      </a>
                    </li>
                    <li v-if="isLoggedIn && (page.props.auth?.user?.id === feedback.user_id || isAdmin)">
                      <a class="dropdown-item text-danger" href="#" @click.prevent="$emit('open-delete-modal', feedback.id)">
                        <i class="bi bi-trash me-2"></i> Hapus
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <span
                  :class="[
                    'badge',
                    'rounded-pill',
                    'mb-1',
                    'me-1',
                    feedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' :
                    feedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' :
                    feedback.kategori === 'Umum' ? 'bg-umum text-umum' :
                    feedback.kategori === 'Cari Laporan' ? 'bg-cari-laporan text-cari-laporan' :
                    'bg-purple text-purple'
                  ]"
                >
                  {{ feedback.kategori }}
                </span>
                <small class="text-muted">
                  {{ feedback.created_at }}
                  <template v-if="feedback.is_updated"> (diperbarui)</template>
                </small>
              </div>

              <div class="text-warning mb-3">
                <span v-for="i in 5" :key="i">
                  <i class="bi" :class="i <= feedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
                </span>
              </div>

              <p class="card-text mb-0">{{ truncateText(feedback.message, 100) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state text-center py-5">
        <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i>
        <p class="text-muted">Tidak ditemukan feedback untuk kategori ini.</p>
      </div>
    </div>
  </transition-group>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();

defineProps({
  feedbacks: Array,
  isLoading: Boolean,
  isLoggedIn: Boolean,
  isAdmin: Boolean,
  activeDropdownId: [Number, null],
});

defineEmits(['open-detail-modal', 'open-feedback-modal', 'open-delete-modal', 'toggle-dropdown']);

const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};
</script>

<style scoped>
:root {
  --badge-colors: {
    pelaporan: { bg: #FFD1D1, text: #B91C1C };
    verifikasi: { bg: #FFF3CD, text: #856404 };
    umum: { bg: #D1FAE5, text: #065F46 };
    cari-laporan: { bg: #BFDBFE, text: #1E40AF };
    purple: { bg: #E9D5FF, text: #7E22CE };
  }
}

.feedback-container {
  min-height: 400px;
  display: flex;
  flex-direction: column;
}

.feedback-card {
  transition: all 0.3s ease;
}

.feedback-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
}

.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

[class*="bg-"] {
  background-color: var(--badge-bg);
}

[class*="text-"] {
  color: var(--badge-text);
}

.bg-pelaporan { --badge-bg: #FFD1D1; }
.text-pelaporan { --badge-text: #B91C1C; }

.bg-verifikasi { --badge-bg: #FFF3CD; }
.text-verifikasi { --badge-text: #856404; }

.bg-umum { --badge-bg: #D1FAE5; }
.text-umum { --badge-text: #065F46; }

.bg-cari-laporan { --badge-bg: #BFDBFE; }
.text-cari-laporan { --badge-text: #1E40AF; }

.bg-purple { --badge-bg: #E9D5FF; }
.text-purple { --badge-text: #7E22CE; }

.empty-state {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}

.skeleton {
  background: #e0e0e0;
  border-radius: 4px;
  animation: skeleton-loading 1s linear infinite alternate;
}

.skeleton-badge {
  width: 80px;
  height: 20px;
}

.skeleton-small-text {
  width: 100px;
  height: 14px;
}

.skeleton-stars {
  width: 100px;
  height: 20px;
}

.skeleton-text {
  width: 100%;
  height: 40px;
}

.skeleton-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.skeleton-name {
  width: 120px;
  height: 20px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
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

@keyframes skeleton-loading {
  0% {
    background-color: #e0e0e0;
  }
  100% {
    background-color: #f5f5f5;
  }
}
</style>