<template>
  <div class="p-4">
    <button
      @click="confirmUserDeletion"
      class="btn btn-outline-danger w-100"
    >
      <i class="bi bi-trash me-2"></i>
      Hapus Profil
    </button>

    <div v-if="confirmingUserDeletion" class="modal-backdrop fixed inset-0 z-50 bg-gray-500 bg-opacity-75 flex justify-center items-center">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal-content mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold text-gray-900">Are you sure you want to delete your account?</h4>
              <p class="text-muted mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.</p>
            </div>
          </div>

          <form @submit.prevent="deleteUser">
            <div class="mb-3">
              <label for="password" class="form-label fw-bold text-gray-700">Password</label>
              <input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="form-control border-primary"
                placeholder="Enter your password"
                @keyup.enter="deleteUser"
              />
              <small class="text-red-600 d-block mt-2">{{ form.errors.password }}</small>
            </div>

            <div class="d-flex justify-content-between space-x-3 mt-5">
              <button
                type="button"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                @click="closeModal"
              >
                Cancel
              </button>

              <button
                type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                :disabled="form.processing"
              >
                Delete Account
              </button>
            </div>
          </form>
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