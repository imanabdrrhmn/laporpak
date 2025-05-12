<template>
  <Head title="Ulasan" />
  <AppLayout>
    <!-- Header Section -->
    <div class="header-gradient text-white py-5 text-center">
      <h1 class="display-4 fw-bold">Feedback</h1>
      <p class="lead mt-2">
        Bagikan pengalaman Anda dan lihat ulasan yang dikaitkan pengguna lain tentang fitur-fitur LaptopPak
      </p>
    </div>

    <div class="container py-5">
      <!-- Filter Section -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
          <span class="me-3 fw-medium text-muted">FILTER:</span>
          <select v-model="selectedCategory" class="form-select" style="width: 200px;">
            <option value="">Semua Kategori</option>
            <option value="Pelaporan">Pelaporan</option>
            <option value="Verifikasi">Verifikasi</option>
            <option value="Lapor Map">Lapor Map</option>
            <option value="Umum">Umum</option>
          </select>
        </div>
        <div>
          <button class="btn btn-primary me-2" @click="sortBy = 'latest'">
            Terbaru
          </button>
          <button class="btn btn-white text-primary border-primary hover-bg-primary hover-text-white" @click="sortBy = 'highestRating'">
            Rating Tertinggi
          </button>
        </div>
      </div>

      <!-- Flash Messages -->
      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $page.props.flash.error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <!-- Button to Open Modal -->
      <div v-if="isLoggedIn && contactVerified" class="mb-4">
        <button class="btn btn-primary" @click="openFeedbackModal('create')">
          <i class="bi bi-chat-square-text me-2"></i> Beri Feedback
        </button>
      </div>
      <div v-else-if="isLoggedIn && !contactVerified" class="mb-4">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Anda perlu verifikasi email atau nomor HP terlebih dahulu untuk memberi feedback.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>

      <!-- Feedback List with Fixed Layout -->
      <div class="feedback-container">
        <!-- Skeleton View -->
        <div v-if="isLoading" class="row">
          <div v-for="n in 3" :key="n" class="col-md-4 mb-4">
            <div class="card border-0 shadow-lg h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="skeleton skeleton-badge"></span>
                  <span class="skeleton skeleton-small-text"></span>
                </div>
                <div class="skeleton skeleton-stars mb-3"></div>
                <div class="skeleton skeleton-text mb-3"></div>
                <div class="d-flex align-items-center">
                  <div class="skeleton skeleton-circle me-3"></div>
                  <div class="skeleton skeleton-name"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Feedback List or Empty State -->
        <div v-else>
          <div v-if="filteredFeedbacks.length > 0" class="row">
            <div v-for="feedback in filteredFeedbacks" :key="feedback.id" class="col-md-4 mb-4">
              <div class="card border-0 shadow-lg rounded-lg h-100">
                <div class="card-body p-4">
                  <!-- Category and Timestamp -->
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span :class="[
                      'badge',
                      'rounded-pill',
                      feedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' :
                      feedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' :
                      feedback.kategori === 'Umum' ? 'bg-umum text-umum' :
                      'bg-purple text-white'
                    ]">
                      {{ feedback.kategori }}
                    </span>
                    <small class="text-muted">
                      {{ feedback.created_at }}
                      <template v-if="feedback.is_updated"> (diperbarui)</template>
                    </small>
                  </div>

                  <!-- Star Rating -->
                  <div class="text-warning mb-3">
                    <span v-for="i in 5" :key="i">
                      <i class="bi" :class="i <= feedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
                    </span>
                  </div>

                  <!-- Feedback Message -->
                  <p class="card-text mb-3">“{{ feedback.message }}”</p>

                  <!-- User Info -->
                  <div class="d-flex align-items-center">
                    <img
                      v-if="feedback.user.avatar_url"
                      :src="feedback.user.avatar_url"
                      alt="Avatar"
                      class="rounded-circle me-3"
                      style="width: 50px; height: 50px; object-fit: cover;"
                    />
                    <div
                      v-else
                      class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                      style="width: 50px; height: 50px;"
                    >
                      {{ feedback.user.name.charAt(0) }}{{ feedback.user.name.split(' ').length > 1 ? feedback.user.name.split(' ')[1].charAt(0) : '' }}
                    </div>
                    <h5 class="card-title mb-0">{{ feedback.user.name }}</h5>
                  </div>
                  <!-- Edit/Delete Buttons for Owner or Admin -->
                  <div v-if="page.props.auth?.user?.id === feedback.user_id || isAdmin" class="mt-3">
                    <button class="btn btn-sm btn-outline-primary me-2" @click="openFeedbackModal('edit', feedback)">
                      <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="openDeleteModal(feedback.id)">
                      <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="empty-state text-center">
            <p class="text-muted">Tidak ditemukan feedback untuk kategori ini.</p>
          </div>
        </div>
      </div>

      <!-- Modal Component -->
      <FeedbackModal
        ref="feedbackModalRef"
        :mode="mode"
        :feedback-data="currentFeedback"
        @modal-closed="handleModalClosed"
      />

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" ref="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Apakah Anda yakin ingin menghapus feedback ini?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="button" class="btn btn-danger" @click="confirmDelete">Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick, watch } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeedbackModal from './FeedbackModal.vue';
import { Modal } from 'bootstrap';

const props = defineProps({
  feedbacks: Array,
  contactVerified: Boolean,
});

const page = usePage();
page.layout = AppLayout;

const isLoggedIn = computed(() => !!page.props.auth?.user);
const roles = page.props.auth?.user?.roles ?? [];
const isAdmin = computed(() => roles.includes('admin'));

// Filter and Sort state
const selectedCategory = ref('');
const sortBy = ref('latest');
const isLoading = ref(false);

// Filtered and sorted feedbacks
const filteredFeedbacks = computed(() => {
  let result = props.feedbacks;

  // Filter by category
  if (selectedCategory.value) {
    result = result.filter(feedback => feedback.kategori === selectedCategory.value);
  }

  // Sort by rating or latest
  if (sortBy.value === 'highestRating') {
    const maxRating = Math.max(...result.map(f => f.rating));
    result = result.filter(feedback => feedback.rating === maxRating);
  } else {
    result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  }

  return result;
});

// Watch for changes in filters to simulate loading
watch([selectedCategory, sortBy], () => {
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 1000); // Simulate 1-second loading
});

// Modal management for feedback
const feedbackModalRef = ref(null);
const mode = ref('create');
const currentFeedback = ref({});

const openFeedbackModal = (type, feedback = null) => {
  mode.value = type;
  if (type === 'edit' && feedback) {
    currentFeedback.value = feedback;
  } else {
    currentFeedback.value = {};
  }
  nextTick(() => {
    feedbackModalRef.value.showModal();
  });
};

const handleModalClosed = () => {
  currentFeedback.value = {};
};

// Delete Modal management
const deleteModal = ref(null);
const feedbackToDelete = ref(null);
let deleteModalInstance = null;

const openDeleteModal = (id) => {
  feedbackToDelete.value = id;
  deleteModalInstance = new Modal(deleteModal.value);
  deleteModalInstance.show();
};

const confirmDelete = () => {
  if (feedbackToDelete.value) {
    if (deleteModalInstance) {
      deleteModalInstance.hide();
    }
    router.delete(`/feedback/${feedbackToDelete.value}`, {
      onSuccess: () => {
        feedbackToDelete.value = null;
        deleteModalInstance = null;
      },
      onError: (errors) => {
        console.error('Delete failed:', errors);
        feedbackToDelete.value = null;
        deleteModalInstance = null;
      }
    });
  }
};
</script>

<style scoped>
.header-gradient {
  background: linear-gradient(to right, #0d6efd, #66b0ff);
}

.card {
  transition: all 0.3s ease;
  border-radius: 15px; /* Sudut lebih lembut */
}

.card:hover {
  transform: translateY(-5px); /* Efek hover lebih kentara */
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important; /* Bayangan lebih tegas */
}

.card-body {
  padding: 20px; /* Padding lebih besar untuk konten nyaman */
}

.badge {
  font-size: 0.8rem;
  padding: 0.35em 0.65em;
}

.bg-pelaporan {
  background-color: #D4EDFF;
}

.text-pelaporan {
  color: #004085;
}

.bg-verifikasi {
  background-color: #FFF3CD;
}

.text-verifikasi {
  color: #856404;
}

.bg-umum {
  background-color: #D1FAE5;
}

.text-umum {
  color: #065F46;
}

.bg-purple {
  background-color: #6f42c1;
}

.btn-white {
  background-color: white;
}

.border-primary {
  border-color: #0d6efd !important;
}

.text-primary {
  color: #0d6efd !important;
}

.hover-bg-primary:hover {
  background-color: #0d6efd !important;
}

.hover-text-white:hover {
  color: white !important;
}

/* Fixed Layout */
.feedback-container {
  min-height: 400px;
  display: flex;
  flex-direction: column;
}

/* Empty State */
.empty-state {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

/* Skeleton Styles */
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
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.skeleton-name {
  width: 120px;
  height: 20px;
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