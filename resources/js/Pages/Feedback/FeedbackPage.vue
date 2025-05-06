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
        <button class="btn btn-primary mb-3" @click="openModal('create')">Beri Feedback</button>
      </div>
      <div v-else-if="isLoggedIn && !contactVerified">
        <div class="alert alert-warning">
          Anda perlu verifikasi email atau nomor HP terlebih dahulu untuk memberi feedback.
        </div>
      </div>

      <!-- List Feedback -->
      <div v-for="feedback in feedbacks" :key="feedback.id" class="card mb-3">
        <div class="card-body">
          <p class="user-category">{{ feedback.kategori }}</p>
          <h5 class="card-title">{{ feedback.user.name }}</h5>
          <p class="card-text">{{ feedback.message }}</p>
          <p class="text-warning">
            <span v-for="i in 5" :key="i">
              <i class="bi" :class="i <= feedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
            </span>
          </p>

          <!-- Jika user yang login adalah pemilik feedback atau admin -->
          <div v-if="page.props.auth?.user?.id === feedback.user_id || isAdmin">
            <button class="btn btn-sm btn-outline-secondary me-2" @click="openModal('edit', feedback)">Edit</button>
            <button class="btn btn-sm btn-outline-danger" @click="destroy(feedback.id)">Hapus</button>
          </div>
        </div>
      </div>

      <!-- Modal Form -->
      <div class="modal fade" id="feedbackModal" tabindex="-1" ref="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <form @submit.prevent="submit">
              <div class="modal-header">
                <h5 class="modal-title">{{ mode === 'create' ? 'Beri Feedback' : 'Edit Feedback' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="kategori" class="form-label">Kategori</label>
                  <select v-model="form.kategori" class="form-control" id="kategori">
                    <option value="Pelaporan">Pelaporan</option>
                    <option value="Verifikasi">Verifikasi</option>
                    <option value="Umum">Umum</option>
                  </select>
                  <div v-if="form.errors.kategori" class="text-danger">{{ form.errors.kategori }}</div>
                </div>
                <div class="mb-3">
                  <label for="message" class="form-label">Pesan</label>
                  <textarea id="message" v-model="form.message" class="form-control" rows="3"></textarea>
                  <div v-if="form.errors.message" class="text-danger">{{ form.errors.message }}</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Rating</label>
                  <div>
                    <span v-for="i in 5" :key="i" @click="form.rating = i" style="cursor: pointer;">
                      <i class="bi fs-5" :class="i <= form.rating ? 'bi-star-fill text-warning' : 'bi-star text-muted'"></i>
                    </span>
                  </div>
                  <div v-if="form.errors.rating" class="text-danger">{{ form.errors.rating }}</div>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary">{{ mode === 'create' ? 'Kirim' : 'Update' }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  feedbacks: Array,
  contactVerified: Boolean,
});

const page = usePage();
page.layout = AppLayout;

const isLoggedIn = computed(() => !!page.props.auth?.user);
const roles = page.props.auth?.user?.role ? [page.props.auth.user.role] : [];
const isAdmin = roles.includes('admin');

// Modal logic
let modalInstance;
const modal = ref(null);
let currentEditId = null;
const mode = ref('create');

const form = useForm({
  message: '',
  rating: 0,
  kategori: 'Umum'
});

const openModal = async (type, feedback = null) => {
  mode.value = type;
  if (type === 'edit' && feedback) {
    form.message = feedback.message;
    form.rating = feedback.rating;
    currentEditId = feedback.id;
  } else {
    form.reset();
    currentEditId = null;
  }

  const bootstrap = await import('bootstrap');
  modalInstance = new bootstrap.Modal(modal.value);
  modalInstance.show();
};

const submit = () => {
  if (mode.value === 'create') {
    form.post('/feedback', {
      onSuccess: () => modalInstance.hide()
    });
  } else {
    form.put(`/feedback/${currentEditId}`, {
      onSuccess: () => modalInstance.hide()
    });
  }
};

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus feedback ini?')) {
    router.delete(`/feedback/${id}`);
  }
};
</script>
