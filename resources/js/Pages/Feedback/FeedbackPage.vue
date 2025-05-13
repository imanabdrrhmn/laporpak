<template>
  <Head title="Ulasan" />
  <AppLayout>
    <!-- Header Section -->
    <div class="header-gradient text-white py-4 py-md-5 text-center">
      <h1 class="display-5 display-md-4 fw-bold">Feedback</h1>
      <p class="lead mt-2 px-3">
        Bagikan pengalaman Anda dan lihat ulasan yang dikaitkan pengguna lain tentang fitur-fitur LaporPak
      </p>
    </div>

    <div class="container py-3 py-md-5">
      <!-- Filter Section -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center w-100 w-md-auto">
          <span class="me-2 me-md-3 fw-medium text-muted">FILTER:</span>
          <select v-model="selectedCategory" class="form-select" style="max-width: 200px;" @change="handleCategoryChange">
            <option value="">Semua Kategori</option>
            <option value="Pelaporan">Pelaporan</option>
            <option value="Verifikasi">Verifikasi</option>
            <option value="Lapor Map">Lapor Map</option>
            <option value="Umum">Umum</option>
            <option value="Cari Laporan">Cari Laporan</option>
          </select>
          
          <!-- Tombol Feedback Saya (terpisah) -->
          <button 
            v-if="isLoggedIn" 
            class="btn ms-2 ms-md-3" 
            :class="isMyFeedbackActive ? 'btn-primary' : 'btn-outline-primary'"
            @click="toggleMyFeedback"
          >
            <i class="bi bi-person-lines-fill me-1"></i> Feedback Saya
          </button>
        </div>
        
        <div class="d-flex w-100 w-md-auto justify-content-end">
          <div class="d-flex">
            <button class="btn btn-primary me-2" @click="sortBy = 'latest'">
              Terbaru
            </button>
            <button class="btn btn-white text-primary border-primary hover-bg-primary hover-text-white" @click="sortBy = 'highestRating'">
              Rating Tertinggi
            </button>
          </div>
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
          <div v-for="n in 3" :key="n" class="col-12 col-sm-6 col-md-4 mb-4">
            <div class="card border-0 shadow-lg h-100">
              <div class="card-body">
                <!-- User info skeleton at top -->
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

        <!-- Feedback List or Empty State -->
        <div v-else>
          <div v-if="filteredFeedbacks.length > 0" class="row">
            <div v-for="feedback in filteredFeedbacks" :key="feedback.id" class="col-12 col-sm-6 col-md-4 mb-4">
              <div class="card border-0 shadow-lg rounded-lg h-100">
                <div class="card-body p-3 p-md-4">
                  <!-- User Info moved to top -->
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
                    <h5 class="card-title mb-0 fs-6">{{ feedback.user.name }}</h5>
                    
                    <!-- Burger Menu Dropdown -->
                    <div class="ms-auto">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li>
                            <a class="dropdown-item" href="#" @click.prevent="openDetailModal(feedback)">
                              <i class="bi bi-eye me-2"></i> Detail
                            </a>
                          </li>
                          <li v-if="page.props.auth?.user?.id === feedback.user_id || isAdmin">
                            <a class="dropdown-item" href="#" @click.prevent="openFeedbackModal('edit', feedback)">
                              <i class="bi bi-pencil-square me-2"></i> Edit
                            </a>
                          </li>
                          <li v-if="page.props.auth?.user?.id === feedback.user_id || isAdmin">
                            <a class="dropdown-item text-danger" href="#" @click.prevent="openDeleteModal(feedback.id)">
                              <i class="bi bi-trash me-2"></i> Hapus
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  
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
                  <p class="card-text mb-0">{{ truncateText(feedback.message, 100) }}</p>
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

      <!-- Modal Component for Feedback Form -->
      <FeedbackModal
        ref="feedbackModalRef"
        :mode="mode"
        :feedback-data="currentFeedback"
        @modal-closed="handleModalClosed"
      />

      <!-- Detail Feedback Modal -->
      <div class="modal fade" ref="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="detailModalLabel">Detail Feedback</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" v-if="selectedFeedback">
              <div class="d-flex align-items-center mb-3">
                <img
                  v-if="selectedFeedback.user?.avatar_url"
                  :src="selectedFeedback.user.avatar_url"
                  alt="Avatar"
                  class="rounded-circle me-3"
                  style="width: 48px; height: 48px; object-fit: cover;"
                />
                <div
                  v-else
                  class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                  style="width: 48px; height: 48px;"
                >
                  {{ selectedFeedback.user?.name?.charAt(0) }}{{ selectedFeedback.user?.name?.split(' ').length > 1 ? selectedFeedback.user.name.split(' ')[1].charAt(0) : '' }}
                </div>
                <div>
                  <h5 class="mb-0">{{ selectedFeedback.user?.name }}</h5>
                  <div class="text-muted small">
                    {{ selectedFeedback.created_at }}
                    <template v-if="selectedFeedback.is_updated"> (diperbarui)</template>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <span :class="[
                  'badge',
                  'rounded-pill',
                  selectedFeedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' :
                  selectedFeedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' :
                  selectedFeedback.kategori === 'Umum' ? 'bg-umum text-umum' :
                  'bg-purple text-white'
                ]">
                  {{ selectedFeedback.kategori }}
                </span>
              </div>
              
              <div class="text-warning mb-3">
                <span v-for="i in 5" :key="i">
                  <i class="bi" :class="i <= selectedFeedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
                </span>
              </div>
              
              <div class="border-top pt-3 mt-3">
                <div class="mb-3">
                  <label class="form-label fw-bold">Feedback:</label>
                  <p class="text-break">{{ selectedFeedback.message }}</p>
                </div>
              </div>
              
              <div v-if="selectedFeedback.admin_response" class="bg-light p-3 rounded mt-3">
                <h6 class="fw-bold">Respon Admin:</h6>
                <p class="mb-0">{{ selectedFeedback.admin_response }}</p>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button 
                v-if="page.props.auth?.user?.id === selectedFeedback?.user_id || isAdmin" 
                type="button" 
                class="btn btn-primary"
                @click="openFeedbackModal('edit', selectedFeedback)"
              >
                Edit
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" ref="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
const searchQuery = ref('');
const isMyFeedbackActive = ref(false); // State untuk tombol "Feedback Saya"

// Filtered and sorted feedbacks
const filteredFeedbacks = computed(() => {
  let result = props.feedbacks;

  // Filter untuk "Feedback Saya" jika tombol aktif
  if (isMyFeedbackActive.value && isLoggedIn.value) {
    result = result.filter(feedback => feedback.user_id === page.props.auth?.user?.id);
  } 
  // Filter by category from dropdown
  else if (selectedCategory.value) {
    // Normal category filtering
    result = result.filter(feedback => feedback.kategori === selectedCategory.value);
  }

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    result = result.filter(feedback => 
      feedback.message.toLowerCase().includes(query) || 
      feedback.user.name.toLowerCase().includes(query) ||
      feedback.kategori.toLowerCase().includes(query)
    );
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

// Toggle fungsi untuk tombol "Feedback Saya"
const toggleMyFeedback = () => {
  isMyFeedbackActive.value = !isMyFeedbackActive.value;
  
  // Reset filter kategori saat mengaktifkan "Feedback Saya"
  if (isMyFeedbackActive.value) {
    selectedCategory.value = '';
  }
  
  // Simulasi loading saat mengubah filter
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
};

// Handle search with debounce
const handleSearch = () => {
  isLoading.value = true;
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    isLoading.value = false;
  }, 500);
};

let searchTimeout;

// Watch for changes in filters to simulate loading
watch([selectedCategory, sortBy], () => {
  // Reset "Feedback Saya" ketika filter kategori berubah
  if (selectedCategory.value !== '') {
    isMyFeedbackActive.value = false;
  }
  
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

// Detail Modal management
const detailModal = ref(null);
const selectedFeedback = ref(null);
let detailModalInstance = null;

const openDeleteModal = (id) => {
  feedbackToDelete.value = id;
  deleteModalInstance = new Modal(deleteModal.value);
  deleteModalInstance.show();
};

const openDetailModal = (feedback) => {
  selectedFeedback.value = {...feedback};
  nextTick(() => {
    detailModalInstance = new Modal(detailModal.value);
    detailModalInstance.show();
  });
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

// Helpers
const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

// Handle category change


const searchInput = ref(null);
</script>

<style scoped>
.header-gradient {
  background: linear-gradient(to right, #0d6efd, #66b0ff);
}

.card {
  transition: all 0.3s ease;
  border-radius: 15px;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
}

.badge {
  font-size: 0.75rem;
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
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.skeleton-name {
  width: 120px;
  height: 20px;
}

@media (max-width: 575.98px) {
  .form-select {
    width: 100%;
    max-width: none;
  }
  
  .input-group {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 6px;
  }
  
  .search-container {
    margin-top: 10px;
    margin-left: 0 !important;
  }
  
  .search-container .input-group {
    width: 100% !important;
  }
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
  .card-body {
    padding: 15px;
  }
  
  .display-5 {
    font-size: 2rem;
  }
  
  .lead {
    font-size: 1rem;
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