<template>
  <Head title="Ulasan" />
  <AppLayout>
    <div class="header-gradient text-white py-4 py-md-5 text-center">
      <h1 class="display-5 display-md-4 fw-bold">Ulasan</h1>
      <p class="lead mt-2 px-3">
        Bagikan pengalaman Anda dan lihat ulasan yang dikaitkan pengguna lain tentang fitur-fitur LaporPak
      </p>
    </div>

    <div class="container py-3 py-md-5">
      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $page.props.flash.error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
          <div class="row g-3">
            <div class="col-12 col-md-6 col-xl-3 order-1">
              <div v-if="isLoggedIn && contactVerified">
                <button class="btn btn-primary w-100" @click="openFeedbackModal('create')">
                  <i class="bi bi-chat-square-text me-2"></i> Beri Ulasan
                </button>
              </div>
              <div v-else-if="isLoggedIn && !contactVerified">
                <button class="btn btn-warning w-100" disabled title="Verifikasi diperlukan">
                  <i class="bi bi-exclamation-triangle me-2"></i> Verifikasi Diperlukan
                </button>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3 order-2" v-if="isLoggedIn">
              <button 
                class="btn w-100" 
                :class="isMyFeedbackActive ? 'btn-primary' : 'btn-outline-primary'"
                @click="toggleMyFeedback"
              >
                <i class="bi bi-person-lines-fill me-1"></i> Ulasan Saya
              </button>
            </div>

            <div class="col-12 col-md-6 col-xl-3 order-3">
              <div class="input-group">
                <span class="input-group-text bg-light">Kategori</span>
                <select v-model="selectedCategory" class="form-select" @change="handleCategoryChange">
                  <option value="">Semua Kategori</option>
                  <option value="Pelaporan">Pelaporan</option>
                  <option value="Verifikasi">Verifikasi</option>
                  <option value="Lapor Map">Lapor Map</option>
                  <option value="Umum">Umum</option>
                  <option value="Cari Laporan">Cari Laporan</option>
                </select>
              </div>
            </div>
            
            <div class="col-12 col-md-6 col-xl-3 order-4">
              <div class="btn-group w-100">
                <button class="btn flex-grow-1" :class="sortBy === 'latest' ? 'btn-primary' : 'btn-outline-primary'" @click="sortBy = 'latest'">
                  <i class="bi bi-clock-history me-1 d-none d-sm-inline"></i> Terbaru
                </button>
                <button class="btn flex-grow-1" :class="sortBy === 'highestRating' ? 'btn-primary' : 'btn-outline-primary'" @click="sortBy = 'highestRating'">
                  <i class="bi bi-star-fill me-1 d-none d-sm-inline"></i> Rating Tertinggi
                </button>
              </div>
            </div>
          </div>
          
          <div v-if="isLoggedIn && !contactVerified" class="mt-3">
            <div class="alert alert-warning mb-0 py-2" role="alert">
              <small><i class="bi bi-exclamation-triangle me-1"></i> Anda perlu verifikasi email atau nomor HP terlebih dahulu untuk memberi feedback.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="feedback-container">
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
          <div v-if="paginatedFeedbacks.length > 0" class="row g-3">
            <div v-for="feedback in paginatedFeedbacks" :key="feedback.id" class="col-12 col-sm-6 col-lg-4 mb-3">
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
                        @click.stop="toggleDropdown(feedback.id)"
                      >
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>
                      <ul
                        v-if="activeDropdownId === feedback.id"
                        class="dropdown-menu show position-absolute end-0 mt-2"
                        style="display: block; z-index: 1000;"
                      >
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
                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                    <span :class="[ 'badge', 'rounded-pill', 'mb-1', 'me-1', feedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' : feedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' : feedback.kategori === 'Umum' ? 'bg-umum text-umum' : 'bg-purple text-white' ]">
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
      </div>

      <div class="d-flex justify-content-center mt-4">
        <button
          class="btn btn-outline-primary"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        <span class="mx-2 align-self-center">{{ currentPage }} / {{ totalPages }}</span>
        <button
          class="btn btn-outline-primary"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Selanjutnya <i class="bi bi-chevron-right"></i>
        </button>
      </div>

      <FeedbackModal
        ref="feedbackModalRef"
        :mode="mode"
        :feedback-data="currentFeedback"
        @modal-closed="handleModalClosed"
      />

      <div class="modal fade" ref="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
                  <h5 class="mb-0 text-break">{{ selectedFeedback.user?.name }}</h5>
                  <div class="text-muted small">
                    {{ selectedFeedback.created_at }}
                    <template v-if="selectedFeedback.is_updated"> (diperbarui)</template>
                  </div>
                </div>
              </div>
              

              <div class="mb-3">
                <span :class="[ 'badge', 'rounded-pill', selectedFeedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' : selectedFeedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' : selectedFeedback.kategori === 'Umum' ? 'bg-umum text-umum' : 'bg-purple text-white' ]">
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
          </div>
        </div>
      </div>

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

const selectedCategory = ref('');
const sortBy = ref('latest');
const isLoading = ref(false);
const searchQuery = ref('');
const isMyFeedbackActive = ref(false); 

const filteredFeedbacks = computed(() => {
  let result = props.feedbacks;

  if (isMyFeedbackActive.value && isLoggedIn.value) {
    result = result.filter(feedback => feedback.user_id === page.props.auth?.user?.id);
  } 
  else if (selectedCategory.value) {
    result = result.filter(feedback => feedback.kategori === selectedCategory.value);
  }

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    result = result.filter(feedback => 
      feedback.message.toLowerCase().includes(query) || 
      feedback.user.name.toLowerCase().includes(query) ||
      feedback.kategori.toLowerCase().includes(query)
    );
  }

  if (sortBy.value === 'highestRating') {
    const maxRating = Math.max(...result.map(f => f.rating));
    result = result.filter(feedback => feedback.rating === maxRating);
  } else {
    result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  }

  return result;
});

const currentPage = ref(1);
const feedbacksPerPage = 6;
const totalPages = computed(() => Math.ceil(filteredFeedbacks.value.length / feedbacksPerPage));

const paginatedFeedbacks = computed(() => {
  const start = (currentPage.value - 1) * feedbacksPerPage;
  const end = start + feedbacksPerPage;
  return filteredFeedbacks.value.slice(start, end);
});

const toggleMyFeedback = () => {
  isMyFeedbackActive.value = !isMyFeedbackActive.value;
  
  if (isMyFeedbackActive.value) {
    selectedCategory.value = '';
  }
  
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 700);
};

const handleSearch = () => {
  isLoading.value = true;
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    isLoading.value = false;
  }, 500);
};

let searchTimeout;

const handleCategoryChange = () => {
  if (selectedCategory.value !== '') {
    isMyFeedbackActive.value = false;
  }
  
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 700);
};

watch([sortBy], () => {
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 700);
});

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

const deleteModal = ref(null);
const feedbackToDelete = ref(null);
let deleteModalInstance = null;

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

const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

const activeDropdownId = ref(null);

const toggleDropdown = (id) => {
  activeDropdownId.value = activeDropdownId.value === id ? null : id;
};

document.addEventListener('click', (e) => {
  const clickedInside = e.target.closest('.dropdown-menu') || e.target.closest('.btn');
  if (!clickedInside) {
    activeDropdownId.value = null;
  }
});
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
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 200px;
}

/* Card Enhancements */
.feedback-card {
  transition: all 0.3s ease;
}

.feedback-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
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

/* Responsive adjustments */
@media (max-width: 767.98px) {
  .display-5 {
    font-size: 1.75rem;
  }
  
  .lead {
    font-size: 1rem;
  }

  .modal-footer {
    justify-content: center;
  }
  
  .modal-footer .btn {
    flex-grow: 1;
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

/* Media queries for better responsive design */
@media (max-width: 575.98px) {
  .card-body {
    padding: 0.75rem;
  }
  
  h1.display-5 {
    font-size: 1.5rem;
  }
  
  .lead {
    font-size: 0.9rem;
  }
  
  .header-gradient {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
  }
}

/* Make buttons more touch-friendly on mobile */
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

/* Ensure cards have consistent height on all screens */
@media (max-width: 991.98px) {
  .row {
    margin: 0 -0.5rem;
  }
  
  .col-12, .col-sm-6, .col-md-4, .col-lg-4 {
    padding: 0 0.5rem;
  }
}
</style>