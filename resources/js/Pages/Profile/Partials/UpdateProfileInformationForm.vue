<template>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
      <h5 class="card-title mb-0 d-flex align-items-center">
        <i class="bi bi-person-fill me-2 text-primary"></i>
        Informasi Pribadi
      </h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="firstName" class="form-label small fw-medium">Nama Depan</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-person"></i>
              </span>
              <input
                type="text"
                class="form-control"
                id="firstName"
                v-model="form.firstName"
                required
                autocomplete="given-name"
              />
            </div>
            <div v-if="form.errors.firstName" class="mt-2 text-sm text-red-600">
              {{ form.errors.firstName }}
            </div>
          </div>
          <div class="col-md-6">
            <label for="lastName" class="form-label small fw-medium">Nama Belakang</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-person"></i>
              </span>
              <input
                type="text"
                class="form-control"
                id="lastName"
                v-model="form.lastName"
                autocomplete="family-name"
              />
            </div>
            <div v-if="form.errors.lastName" class="mt-2 text-sm text-red-600">
              {{ form.errors.lastName }}
            </div>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label small fw-medium">Email</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-envelope"></i>
              </span>
              <input
                type="email"
                class="form-control"
                id="email"
                v-model="form.email"
                required
                autocomplete="username"
              />
            </div>
            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
              {{ form.errors.email }}
            </div>
          </div>
          <div class="col-md-6">
            <label for="phone" class="form-label small fw-medium">No. Telp</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-telephone"></i>
              </span>
              <input
                type="tel"
                class="form-control"
                id="phone"
                v-model="form.phone"
                autocomplete="tel"
              />
            </div>
            <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">
              {{ form.errors.phone }}
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-medium">Jenis Kelamin</label>
            <div class="d-flex gap-4 mt-2">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  name="gender"
                  id="male"
                  value="male"
                  v-model="form.gender"
                />
                <label class="form-check-label" for="male">Laki-laki</label>
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  name="gender"
                  id="female"
                  value="female"
                  v-model="form.gender"
                />
                <label class="form-check-label" for="female">Perempuan</label>
              </div>
            </div>
            <div v-if="form.errors.gender" class="mt-2 text-sm text-red-600">
              {{ form.errors.gender }}
            </div>
          </div>
          <div class="col-md-6">
            <label for="location" class="form-label small fw-medium">Lokasi</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-geo-alt"></i>
              </span>
              <input
                type="text"
                class="form-control"
                id="location"
                v-model="form.location"
                autocomplete="address-level1"
              />
            </div>
            <div v-if="form.errors.location" class="mt-2 text-sm text-red-600">
              {{ form.errors.location }}
            </div>
          </div>
          <div class="col-12">
            <label for="biography" class="form-label small fw-medium">Cerpen (Cerita Singkat Pengalaman Hidup)</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-journal-text"></i>
              </span>
              <textarea
                class="form-control"
                id="biography"
                rows="4"
                v-model="form.biography"
                placeholder="Ceritakan pengalaman hidup Anda secara singkat"
              ></textarea>
            </div>
            <div v-if="form.errors.biography" class="mt-2 text-sm text-red-600">
              {{ form.errors.biography }}
            </div>
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
  firstName: user.firstName || '',
  lastName: user.lastName || '',
  email: user.email,
});
</script>

<style scoped>
.card {
  border-radius: 0.75rem;
}
</style>