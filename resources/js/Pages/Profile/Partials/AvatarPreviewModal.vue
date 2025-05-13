<template>
  <Transition name="modal-fade">
    <div v-if="show" class="modal-backdrop">
      <div class="modal-wrapper" @click.self="onClose">
        <div class="modal-container">
          <div class="modal-header">
            <h5 class="modal-title">Ubah Foto Profil</h5>
            <button type="button" class="btn-close" @click="onClose"></button>
          </div>
          
          <div class="modal-body">
            <!-- Avatar Preview -->
            <div class="avatar-preview mb-3">
              <img 
                :src="avatarPreview || defaultAvatar" 
                alt="Avatar Preview" 
                class="preview-image"
              />
            </div>
            
            <!-- Upload Options -->
            <div class="upload-options">
              <label for="avatarUpload" class="upload-btn">
                <i class="bi bi-upload me-2"></i>
                Pilih Foto
              </label>
              <input 
                type="file" 
                id="avatarUpload" 
                class="d-none" 
                accept="image/*"
                @change="handleFileUpload"
              />
              <div class="small text-muted mt-1">
                Maksimal 5MB (JPG, PNG)
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" @click="onClose">
              Batal
            </button>
            <button type="button" class="btn btn-primary" @click="saveChanges" :disabled="!hasChanges">
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

const props = defineProps({
  show: Boolean,
  onClose: Function,
});

const defaultAvatar = computed(() =>
  user.value?.avatar_url || 'https://placehold.co/150x150?text=Avatar'
);

const avatarPreview = ref('');
const hasChanges = ref(false);
const selectedFile = ref(null);

watch(() => props.show, (newVal) => {
  if (newVal) {
    avatarPreview.value = defaultAvatar.value;
    selectedFile.value = null;
    hasChanges.value = false;
  }
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 5 * 1024 * 1024) {
    alert('File terlalu besar. Maksimal ukuran file adalah 5MB.');
    return;
  }

  selectedFile.value = file;

  const reader = new FileReader();
  reader.onload = (e) => {
    avatarPreview.value = e.target.result;
    hasChanges.value = true;
  };
  reader.readAsDataURL(file);
};

const saveChanges = () => {
  if (!selectedFile.value) {
    alert('Silakan pilih file terlebih dahulu.');
    return;
  }

  const formData = new FormData();
  formData.append('avatar', selectedFile.value);

  router.post(route('profile.avatar.update'), formData, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      props.onClose();
      router.reload({ only: ['auth'] }); // reload auth data untuk refresh avatar
    },
    onError: () => {
      alert('Gagal memperbarui avatar.');
    }
  });
};
</script>



<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 15px;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  overflow-y: auto;
}

.modal-header {
  padding: 12px 16px;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-title {
  font-weight: 600;
  margin: 0;
  font-size: 1.1rem;
}

.modal-body {
  padding: 16px;
  text-align: center;
}

.modal-footer {
  padding: 12px 16px;
  border-top: 1px solid #e9ecef;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.avatar-preview {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto;
  border: 2px solid #4361ee;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-btn {
  display: inline-flex;
  align-items: center;
  background-color: #4361ee;
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.upload-btn:hover {
  background-color: #3a56d4;
}

.btn-outline-secondary {
  padding: 6px 12px;
  font-size: 0.9rem;
}

.btn-primary {
  background-color: #4361ee;
  border-color: #4361ee;
  padding: 6px 12px;
  font-size: 0.9rem;
}

@media (max-width: 575.98px) {
  .modal-container {
    max-width: 90%;
  }
  
  .avatar-preview {
    width: 120px;
    height: 120px;
  }
}
</style>