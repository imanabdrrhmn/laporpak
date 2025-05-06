<template>
  <div v-if="show">
    <!-- Backdrop -->
    <div class="modal-backdrop fade show" @click.self="onClose"></div>

    <!-- Modal -->
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-avatar-modal mx-auto">
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold modal-title">Ubah Foto Profil</h4>
              <p class="text-muted mb-4">Unggah avatar terbaru Anda</p>
            </div>
            <button type="button" class="btn-close" @click="onClose"></button>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit">
            <div class="text-center mb-3">
              <img :src="avatarPreview" class="rounded-circle object-cover" style="width: 120px; height: 120px;" />
            </div>

            <div class="mb-3">
              <input type="file" accept="image/*" @change="handleAvatarChange" class="form-control" />
              <small v-if="form?.errors?.avatar" class="text-danger">{{ form.errors.avatar }}</small>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
            </div>
          </form>
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
.custom-avatar-modal {
  width: 90%;
  max-width: 500px;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
  background-color: #fff;
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

.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100%;
  height: 100%;
  overflow-y: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 576px) {
  .custom-avatar-modal {
    width: 95% !important;
    padding: 24px;
    border-radius: 16px;
  }

  .modal-title {
    font-size: 1.2rem;
  }

  .form-control,
  .btn {
    font-size: 0.95rem;
  }
}
</style>
