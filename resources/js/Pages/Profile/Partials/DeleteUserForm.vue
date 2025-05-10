<template>
  <section class="py-5">
    <div class="container px-4">
      <header class="mb-4">
        <h2 class="h4 fw-bold">Hapus Akun</h2>
        <p class="text-muted small">
          Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Silakan unduh data atau informasi yang ingin Anda simpan sebelum menghapus akun.
        </p>
      </header>

      <button
        @click="confirmUserDeletion"
        class="btn btn-danger"
      >
        Hapus Akun
      </button>

      <!-- Modal -->
      <div v-if="confirmingUserDeletion" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-0">
              <h5 class="modal-title fw-bold">Konfirmasi Penghapusan Akun</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
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
  </section>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
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
@media (max-width: 576px) {
  .modal-dialog {
    margin: 0.5rem;
  }
  .modal-content {
    padding: 1rem;
  }
}
</style>