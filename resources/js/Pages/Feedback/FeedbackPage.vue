<template>
  <Head title="Ulasan" />
  <AppLayout>
    <div class="container mt-4">
      <h1 class="mb-3">Daftar Feedback</h1>

      <!-- Flash Message -->
      <div v-if="$page.props.flash?.success" class="alert alert-success">{{ $page.props.flash.success }}</div>
      <div v-if="$page.props.flash?.error" class="alert alert-danger">{{ $page.props.flash.error }}</div>

      <!-- Tombol buka modal -->
      <div v-if="isLoggedIn && contactVerified">
        <button class="btn btn-primary mb-3" @click="openFeedbackModal('create')">
          <i class="bi bi-chat-square-text me-1"></i> Beri Feedback
        </button>
      </div>
      <div v-else-if="isLoggedIn && !contactVerified">
        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Anda perlu verifikasi email atau nomor HP terlebih dahulu untuk memberi feedback.
        </div>
      </div>

      <!-- List Feedback -->
      <div v-for="feedback in feedbacks" :key="feedback.id" class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <span class="badge bg-primary mb-2">{{ feedback.kategori }}</span>
              <h5 class="card-title">{{ feedback.user.name }}</h5>
              <small class="text-muted">
                Dikirim {{ feedback.created_at }}
                <template v-if="feedback.is_updated"> (diperbarui {{ feedback.updated_at }})</template>
              </small>
            </div>
            <div class="text-warning">
              <span v-for="i in 5" :key="i">
                <i class="bi" :class="i <= feedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
              </span>
            </div>
          </div>
          
          <p class="card-text">{{ feedback.message }}</p>

          <!-- Jika user yang login adalah pemilik feedback atau admin -->
          <div v-if="page.props.auth?.user?.id === feedback.user_id || isAdmin" class="mt-2">
            <button class="btn btn-sm btn-outline-secondary me-2" @click="openFeedbackModal('edit', feedback)">
              <i class="bi bi-pencil-square me-1"></i> Edit
            </button>
            <button class="btn btn-sm btn-outline-danger" @click="destroy(feedback.id)">
              <i class="bi bi-trash me-1"></i> Hapus
            </button>
          </div>
        </div>
      </div>

      <!-- Modal komponen -->
      <FeedbackModal
        ref="feedbackModalRef"
        :mode="mode"
        :feedback-data="currentFeedback"
        @modal-closed="handleModalClosed"
      />

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FeedbackModal from './FeedbackModal.vue';

const props = defineProps({
  feedbacks: Array,
  contactVerified: Boolean,
});

const page = usePage();
page.layout = AppLayout;

const isLoggedIn = computed(() => !!page.props.auth?.user);
const roles = page.props.auth?.user?.roles ?? [];
const isAdmin = computed(() => roles.includes('admin'));

// Refs for modal management
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
  
  // Use nextTick to ensure the modal component has updated props before showing
  nextTick(() => {
    feedbackModalRef.value.showModal();
  });
};

const handleModalClosed = () => {
  // Optional: Handle any cleanup after modal is closed
  currentFeedback.value = {};
};

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus feedback ini?')) {
    router.delete(`/feedback/${id}`);
  }
};
</script>

<style scoped>
.badge {
  font-size: 0.8rem;
  padding: 0.35em 0.65em;
}

.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  color: white;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  color: white;
}
</style>