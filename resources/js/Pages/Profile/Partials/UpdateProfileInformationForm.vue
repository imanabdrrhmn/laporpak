<template>
  <section class="py-5">
    <div class="container px-4">
      <header class="mb-4">
        <h2 class="h4 fw-bold">Informasi Profil</h2>
        <p class="text-muted small">
          Perbarui informasi profil dan alamat email akun Anda.
        </p>
      </header>

      <form @submit.prevent="form.patch(route('profile.update'))" class="card p-4 shadow-sm">
        <!-- Name Field -->
        <div class="mb-3">
          <label for="name" class="form-label fw-bold">Nama</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            class="form-control"
            required
            autocomplete="name"
          />
          <div v-if="form.errors.name" class="text-danger small mt-2">
            {{ form.errors.name }}
          </div>
        </div>
        <!-- Email Field -->
        <div class="mb-3">
          <label for="email" class="form-label fw-bold">Email</label>
          <input
            id="email"
            type="email"
            v-model="form.email"
            class="form-control"
            required
            autocomplete="username"
          />
          <div v-if="form.errors.email" class="text-danger small mt-2">
            {{ form.errors.email }}
          </div>
        </div>

        <!-- Email Verification Link -->
        <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mb-3">
          <p class="text-muted small">
            Alamat email Anda belum diverifikasi.
            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="text-primary text-decoration-underline"
            >
              Klik di sini untuk mengirim ulang email verifikasi.
            </Link>
          </p>
          <div v-show="status === 'verification-link-sent'" class="text-success small mt-2">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
          </div>
        </div>

        <!-- Save Button -->
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
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
  name: user.name,
  email: user.email,
});
</script>

<style scoped>
.card {
  border-radius: 0.75rem;
}
</style>