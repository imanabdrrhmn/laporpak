<template>
  <section class="py-5">
    <div class="container px-4">
      <header class="mb-4">
        <h2 class="h4 fw-bold">Perbarui Kata Sandi</h2>
        <p class="text-muted small">
          Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk keamanan.
        </p>
      </header>

      <form @submit.prevent="updatePassword" class="card p-4 shadow-sm">
        <!-- Current Password -->
        <div class="mb-3">
          <label for="current_password" class="form-label fw-bold">Kata Sandi Saat Ini</label>
          <input
            id="current_password"
            ref="currentPasswordInput"
            v-model="form.current_password"
            type="password"
            class="form-control"
            autocomplete="current-password"
          />
          <div v-if="form.errors.current_password" class="text-danger small mt-2">
            {{ form.errors.current_password }}
          </div>
        </div>

        <!-- New Password -->
        <div class="mb-3">
          <label for="password" class="form-label fw-bold">Kata Sandi Baru</label>
          <input
            id="password"
            ref="passwordInput"
            v-model="form.password"
            type="password"
            class="form-control"
            autocomplete="new-password"
          />
          <div v-if="form.errors.password" class="text-danger small mt-2">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
          <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Kata Sandi</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="form-control"
            autocomplete="new-password"
          />
          <div v-if="form.errors.password_confirmation" class="text-danger small mt-2">
            {{ form.errors.password_confirmation }}
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-flex align-items-center gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="btn btn-primary"
          >
            Simpan
          </button>
          <transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-if="form.recentlySuccessful" class="text-muted small">
              Tersimpan.
            </p>
          </transition>
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const currentPasswordInput = ref(null);
const passwordInput = ref(null);

const user = usePage().props.auth.user;

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation');
        passwordInput.value.focus();
      }
      if (form.errors.current_password) {
        form.reset('current_password');
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<style scoped>
.card {
  border-radius: 0.75rem;
}
</style>