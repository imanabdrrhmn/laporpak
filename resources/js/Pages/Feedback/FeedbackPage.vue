<template>
  <Head title="Ulasan" />
  <AppLayout>
    <FeedbackHeader />
    <div class="container py-3 py-md-5">
      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $page.props.flash.error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <FeedbackFilters
        :is-logged-in="isLoggedIn"
        :contact-verified="contactVerified"
        :is-admin="isAdmin"
        :selected-category.sync="selectedCategory"
        :sort-by.sync="sortBy"
        :is-my-feedback-active.sync="isMyFeedbackActive"
        @open-feedback-modal="openFeedbackModal"
        @toggle-my-feedback="toggleMyFeedback"
        @handle-category-change="handleCategoryChange"
      />
      <FeedbackList
        :feedbacks="paginatedFeedbacks"
        :is-loading="isLoading"
        :is-logged-in="isLoggedIn"
        :is-admin="isAdmin"
        :active-dropdown-id="activeDropdownId"
        @open-detail-modal="openDetailModal"
        @open-feedback-modal="openFeedbackModal"
        @open-delete-modal="openDeleteModal"
        @toggle-dropdown="toggleDropdown"
      />
      <FeedbackPagination
        :current-page.sync="currentPage"
        :total-pages="totalPages"
        :start-index="startIndex"
        :end-index="endIndex"
        :total-items="totalItems"
        @go-to-previous-page="goToPreviousPage"
        @go-to-next-page="goToNextPage"
        @jump-to-page="jumpToPage"
      />
      <FeedbackModal
        ref="feedbackModalRef"
        :mode="mode"
        :feedback-data="currentFeedback"
        @modal-closed="handleModalClosed"
      />
      <FeedbackDetailModal
        ref="detailModal"
        :selected-feedback="selectedFeedback"
      />
      <FeedbackDeleteModal
        ref="deleteModal"
        @confirm-delete="confirmDelete"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick, watch, onMounted } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeedbackModal from './FeedbackModal.vue';
import FeedbackHeader from '@/Components/Feedback/FeedbackHeader.vue';
import FeedbackFilters from '@/Components/Feedback/FeedbackFilters.vue';
import FeedbackList from '@/Components/Feedback/FeedbackList.vue';
import FeedbackPagination from '@/Components/Feedback/FeedbackPagination.vue';
import FeedbackDetailModal from '@/Components/Feedback/FeedbackDetailModal.vue';
import FeedbackDeleteModal from '@/Components/Feedback/FeedbackDeleteModal.vue';
import { Modal, Tooltip } from 'bootstrap/dist/js/bootstrap.bundle.js';

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
const isMyFeedbackActive = ref(false);

const filteredFeedbacks = computed(() => {
  let result = props.feedbacks;

  if (isMyFeedbackActive.value && isLoggedIn.value) {
    result = result.filter(feedback => feedback.user_id === page.props.auth?.user?.id);
  } else if (selectedCategory.value) {
    result = result.filter(feedback => feedback.kategori === selectedCategory.value);
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

const startIndex = computed(() => (currentPage.value - 1) * feedbacksPerPage + 1);
const endIndex = computed(() => Math.min(currentPage.value * feedbacksPerPage, filteredFeedbacks.value.length));
const totalItems = computed(() => filteredFeedbacks.value.length);

const paginatedFeedbacks = computed(() => {
  const start = (currentPage.value - 1) * feedbacksPerPage;
  const end = start + feedbacksPerPage;
  return filteredFeedbacks.value.slice(start, end);
});

watch(filteredFeedbacks, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value || 1;
  }
});

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const goToNextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const jumpToPage = (event) => {
  const page = parseInt(event.target.value, 10);
  if (!isNaN(page) && page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  } else if (event.target.value === '') {
    return;
  } else {
    event.target.value = currentPage.value;
  }
};

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

const initializeModal = (modalElement) => {
  if (!modalElement) {
    console.warn('Modal element is null or undefined');
    return null;
  }

  // Check if element is a Vue component instance
  let element = modalElement;
  if (modalElement.$el) {
    element = modalElement.$el;
  }

  // Find the actual modal element if needed
  const modalEl = element.classList?.contains('modal') 
    ? element 
    : element.querySelector('.modal');

  if (!modalEl) {
    console.warn('Modal element not found or does not have modal class');
    return null;
  }

  try {
    // Check if modal instance already exists
    if (modalEl._modal && modalEl._modal._element) {
      return modalEl._modal;
    }

    // Create new modal instance with proper configuration
    const modalInstance = new Modal(modalEl, {
      backdrop: true,
      keyboard: true,
      focus: true
    });

    // Store instance reference
    modalEl._modal = modalInstance;
    return modalInstance;
  } catch (error) {
    console.error('Error initializing modal:', error);
    return null;
  }
};

const openDeleteModal = (id) => {
  feedbackToDelete.value = id;
  nextTick(() => {
    if (deleteModal.value) {
      deleteModalInstance = initializeModal(deleteModal.value);
      if (deleteModalInstance) {
        deleteModalInstance.show();
      } else {
        console.error('Failed to initialize delete modal');
      }
    }
  });
};

const openDetailModal = (feedback) => {
  selectedFeedback.value = { ...feedback };
  nextTick(() => {
    if (detailModal.value) {
      detailModalInstance = initializeModal(detailModal.value);
      if (detailModalInstance) {
        detailModalInstance.show();
      } else {
        console.error('Failed to initialize detail modal');
      }
    }
  });
};

const confirmDelete = () => {
  if (feedbackToDelete.value) {
    router.delete(`/feedback/${feedbackToDelete.value}`, {
      onSuccess: () => {
        feedbackToDelete.value = null;
        // Close modal if it exists
        if (deleteModalInstance) {
          deleteModalInstance.hide();
        }
      },
      onError: (errors) => {
        console.error('Delete failed:', errors);
        feedbackToDelete.value = null;
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

// Handle outside clicks to close dropdowns
const handleOutsideClick = (e) => {
  const clickedInside = e.target.closest('.dropdown-menu') || e.target.closest('.btn');
  if (!clickedInside) {
    activeDropdownId.value = null;
  }
};

onMounted(() => {
  // Initialize tooltips
  nextTick(() => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
      try {
        new Tooltip(element);
      } catch (error) {
        console.warn('Failed to initialize tooltip:', error);
      }
    });
  });

  // Add event listener for outside clicks
  document.addEventListener('click', handleOutsideClick);
});

// Clean up event listener on unmount
import { onUnmounted } from 'vue';

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
  
  // Clean up modal instances
  if (deleteModalInstance) {
    try {
      deleteModalInstance.dispose();
    } catch (error) {
      console.warn('Error disposing delete modal:', error);
    }
  }
  
  if (detailModalInstance) {
    try {
      detailModalInstance.dispose();
    } catch (error) {
      console.warn('Error disposing detail modal:', error);
    }
  }
});
</script>

<style scoped>
/* Only include styles relevant to the main component */
.alert {
  border-radius: 8px;
  margin-bottom: 1rem;
}
</style>