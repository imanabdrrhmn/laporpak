<template>
  <div class="p-4">
    <button
      @click="confirmUserDeletion"
      class="btn btn-outline-danger w-100"
    >
      <i class="bi bi-trash me-2"></i>
      Hapus Akun
    </button>

    <!-- Modal -->
    <div v-if="confirmingUserDeletion" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header border-0">
            <h5 class="modal-title fw-bold">Konfirmasi Penghapusan Akun</h5>
            <button type="button" class="btn-close close-animated" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4">
              Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Masukkan kata sandi Anda untuk konfirmasi.
            </p>
            <form @submit.prevent="deleteUser">
              <div class="mb-3">
                <label for="password" class="form-label fw-bold">Kata Sandi</label>
                <input
                  id="password"
                  ref="passwordInput"
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  placeholder="Masukkan kata sandi"
                  @keyup.enter="deleteUser"
                />
                <small v-if="form.errors.password" class="text-danger d-block mt-2">{{ form.errors.password }}</small>
              </div>
            </form>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" @click="closeModal">Batal</button>
            <button
              type="submit"
              class="btn btn-danger"
              :disabled="form.processing"
              @click="deleteUser"
            >
              Hapus Akun
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.clearErrors();
  form.reset();
};
</script>

<style scoped>
.btn-outline-danger:hover {
  background-color: #ef476f;
  border-color: #ef476f;
}

.note {
  background-color: #f3f4f6;
  border-left: 4px solid #5a67d8;
  padding: 1rem;
  margin: 1rem 0;
  border-radius: 0.25rem;
}
  .note p {
    margin: 0;
    color: #2d3748;
  }


/* Animation for close button */
.close-animated {
  transition: all 0.3s ease;
}

.close-animated:hover {
  transform: rotate(90deg) scale(1.2);
  opacity: 0.8;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
}
.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}
.custom-modal-content {
  width: 100%;
  max-width: 650px;
  min-height: 400px;
  padding: 3rem;
  background-color: #ffffff;
  border-radius: 24px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
}
.form-control {
  width: 100%;
  padding: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid #ccc;
  margin-top: 0.5rem;
}
.form-control:focus {
  border-color: #5a67d8;
  outline: none;
  background-color: #f7fafc;
}
.text-red-600 {
  color: #e53e3e;
}
.text-gray-600 {
  color: #718096;
}
.text-gray-700 {
  color: #4a5568;
}
.text-gray-900 {
  color: #2d3748;
}
@media (max-width: 576px) {
  .custom-modal-content {
    padding: 1.5rem;
    border-radius: 16px;
  }
}
</style>