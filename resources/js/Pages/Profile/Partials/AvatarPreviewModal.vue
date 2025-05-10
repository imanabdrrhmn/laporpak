<template>
  <div v-if="show">
    <!-- Backdrop -->
    <div class="modal fade show" :class="{ 'd-block': show }" style="background-color: rgba(0, 0, 0, 0.5);" @click.self="onClose"></div>

    <!-- Modal -->
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <!-- Header -->
          <div class="modal-header border-0">
            <h5 class="modal-title fw-bold">Ubah Foto Profil</h5>
            <button type="button" class="btn-close" @click="onClose"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <p class="text-muted mb-4">Unggah avatar terbaru Anda</p>
            <form @submit.prevent="submit">
              <div class="text-center mb-4">
                <img :src="avatarPreview" class="rounded-circle object-fit-cover" style="width: 120px; height: 120px;" alt="Avatar Preview" />
              </div>

              <div class="mb-3">
                <input type="file" accept="image/*" @change="handleAvatarChange" class="form-control" />
                <small v-if="form?.errors?.avatar" class="text-danger">{{ form.errors.avatar }}</small>
              </div>
            </form>
          </div>

          <!-- Footer -->
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" @click="onClose">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="form.processing" @click="submit">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  onClose: Function
});

const user = usePage().props.auth.user;
const avatarPreview = ref(user?.avatar ? `/storage/${user.avatar}` : 'https://placehold.co/120x120?text=Avatar');

const form = useForm({
  avatar: null
});

const handleAvatarChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  form.post(route('profile.avatar.update'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset('avatar');
      props.onClose();
      router.reload({ only: ['auth'] });
    },
  });
};
</script>

<style scoped>
.modal-content {
  padding: 1.5rem;
}

@media (max-width: 576px) {
  .modal-dialog {
    margin: 0.5rem;
  }
  .modal-content {
    padding: 1rem;
  }
  .modal-title {
    font-size: 1.25rem;
  }
}
</style>