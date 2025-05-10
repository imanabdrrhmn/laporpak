<template>
  <Head title="Profil" />
  <AppLayout>
    <template #header>
      <h2 class="h3 fw-bold text-dark">
        Profil
      </h2>
    </template>

    <div class="py-5 bg-light">
      <div class="container px-4">
        <div class="text-center mb-5">
          <img
            :src="avatarPreview"
            class="rounded-circle shadow-sm"
            alt="Avatar"
            style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
            @click="showModal = true"
          />
        </div>
        <AvatarPreviewModal :show="showModal" :onClose="() => showModal = false" />

        <!-- Update Profile Information -->
        <div class="mb-5">
          <UpdateProfileInformationForm
            :must-verify-email="mustVerifyEmail"
            :status="status"
            class="mx-auto"
          />
        </div>

        <!-- Update Password Form -->
        <div class="mb-5">
          <UpdatePasswordForm class="mx-auto" />
        </div>

        <!-- Delete User Form -->
        <div>
          <DeleteUserForm class="mx-auto" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AvatarPreviewModal from './Partials/AvatarPreviewModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const showModal = ref(false);
const avatarPreview = ref(user?.avatar ? `/storage/${user.avatar}` : 'https://placehold.co/120x120?text=Avatar');

defineProps({
  mustVerifyEmail: Boolean,
  status: String,
});
</script>

<style scoped>
@media (max-width: 576px) {
  img.rounded-circle {
    width: 150px;
    height: 150px;
  }
}
</style>