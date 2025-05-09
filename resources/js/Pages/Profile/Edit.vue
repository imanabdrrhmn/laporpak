<template>
  <Head title="Profile" />
  <AppLayout>
    <template #header>
      <div class="bg-primary bg-gradient py-5 rounded-bottom position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25">
          <div class="position-absolute top-0 start-0 w-100 h-100" 
               style="background-image: url('https://placehold.co/1200x300?text=Cover'); background-size: cover;"></div>
        </div>
        <div class="container position-relative">
          <div class="d-flex justify-content-end">
            <button class="btn btn-sm btn-light" @click="showCoverModal = true">
              <i class="bi bi-camera-fill me-1"></i> Ubah Cover
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="py-4">
      <div class="container">
        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
              <div class="card-body p-0">
                <!-- Profile Image -->
                <div class="position-relative text-center">
                  <div class="d-inline-block mt-n5 position-relative">
                    <div class="bg-white rounded-circle p-2 shadow">
                      <img
                        :src="avatarPreview"
                        class="rounded-circle"
                        alt="Avatar"
                        style="width: 150px; height: 150px; object-fit: cover;"
                      />
                      <button 
                        @click="showModal = true" 
                        class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0"
                        style="width: 36px; height: 36px;"
                      >
                        <i class="bi bi-camera-fill"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="mt-3 px-4 pb-3">
                    <h4 class="fw-bold mb-1">{{ user.firstName }} {{ user.lastName }}</h4>
                    <p class="text-muted mb-3">{{ user.email }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                      <div class="badge bg-success-subtle text-success px-3 py-2 rounded-pill d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Pelapor Aktif
                      </div>
                      <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill d-flex align-items-center">
                        <i class="bi bi-shield-check me-1"></i>
                        Terverifikasi
                      </div>
                    </div>
                  </div>
                </div>

                <hr class="my-0">

                <!-- Statistics -->
                <div class="row text-center py-3 g-0">
                  <div class="col-4 border-end">
                    <div class="fs-4 fw-bold text-primary">12</div>
                    <div class="small text-secondary">Laporan</div>
                  </div>
                  <div class="col-4 border-end">
                    <div class="fs-4 fw-bold text-primary">5</div>
                    <div class="small text-secondary">Seleksi</div>
                  </div>
                  <div class="col-4">
                    <div class="fs-4 fw-bold text-primary">7</div>
                    <div class="small text-secondary">Proses</div>
                  </div>
                </div>

                <hr class="my-0">

                <!-- Activity -->
                <div class="p-4">
                  <h6 class="mb-3 text-uppercase fw-bold text-secondary small">Aktivitas</h6>
                  <div class="d-flex align-items-center mb-2">
                    <div class="bg-light p-2 rounded-circle me-3">
                      <i class="bi bi-clock-history text-secondary"></i>
                    </div>
                    <div>
                      <div class="small text-secondary">Login terakhir</div>
                      <div class="fw-medium">2 jam yang lalu</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="bg-light p-2 rounded-circle me-3">
                      <i class="bi bi-calendar-check text-secondary"></i>
                    </div>
                    <div>
                      <div class="small text-secondary">Bergabung sejak</div>
                      <div class="fw-medium">23 Maret 2023</div>
                    </div>
                  </div>
                </div>

                <hr class="my-0">

                <!-- Action Buttons -->
                <div class="p-4">
                  <button class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-cloud-arrow-up me-2"></i>
                    Perbarui Profil
                  </button>
                  <DeleteUserForm />
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-lg-8">
            <UpdateProfileInformationForm />
            <UpdatePasswordForm />
          </div>
        </div>
      </div>
    </div>

    <!-- Avatar Preview Modal -->
    <AvatarPreviewModal :show="showModal" :onClose="() => showModal = false" />
  </AppLayout>
</template>

<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AvatarPreviewModal from './Partials/AvatarPreviewModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const showModal = ref(false);
const showCoverModal = ref(false);
const avatarPreview = ref(user?.avatar ? `/storage/${user.avatar}` : 'https://placehold.co/150x150?text=Avatar');
page.layout = AppLayout;

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});
</script>

<style scoped>
.btn-primary {
  background-color: #4361ee;
  border-color: #4361ee;
}
.btn-primary:hover {
  background-color: #3a56d4;
  border-color: #3a56d4;
}
.text-primary {
  color: #4361ee !important;
}
.bg-primary {
  background-color: #4361ee !important;
}
.bg-primary-subtle {
  background-color: #eef0ff !important;
}
</style>