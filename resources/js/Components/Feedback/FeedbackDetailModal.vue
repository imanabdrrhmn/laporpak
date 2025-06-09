<template>
  <div class="modal fade" ref="modal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-body" v-if="selectedFeedback">
          <!-- Close Button -->
          <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
          
          <!-- User Info Section -->
          <div class="d-flex align-items-start mb-3">
            <div class="user-avatar me-3">
              <img
                v-if="selectedFeedback.user?.avatar_url"
                :src="selectedFeedback.user.avatar_url"
                alt="Avatar"
                class="rounded-circle"
              />
              <div
                v-else
                class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
              >
                {{ selectedFeedback.user?.name?.charAt(0) }}{{ selectedFeedback.user?.name?.split(' ').length > 1 ? selectedFeedback.user.name.split(' ')[1].charAt(0) : '' }}
              </div>
            </div>
            <div class="flex-grow-1 user-content">
              <div class="d-flex justify-content-between align-items-start">
                <div class="user-info">
                  <h5 class="user-name mb-1">{{ selectedFeedback.user?.name || 'Abuyy' }}</h5>
                  <div class="text-muted time-info">
                    {{ selectedFeedback.created_at }}
                    <span v-if="selectedFeedback.is_updated"> (diperbarui)</span>
                  </div>
                </div>
                <div class="d-flex flex-column align-items-end badge-rating-section">
                  <span 
                    :class="[
                      'category-badge',
                      selectedFeedback.kategori === 'Verifikasi' ? 'badge-verifikasi' :
                      selectedFeedback.kategori === 'Pelaporan' ? 'badge-pelaporan' :
                      selectedFeedback.kategori === 'Umum' ? 'badge-umum' :
                      selectedFeedback.kategori === 'Cari Laporan' ? 'badge-cari-laporan' :
                      selectedFeedback.kategori === 'Lapor Map' ? 'badge-lapor-map' :
                      'badge-default'
                    ]"
                  >
                    {{ selectedFeedback.kategori }}
                  </span>
                  <div class="rating-stars mt-1">
                    <span v-for="i in 5" :key="i">
                      <i class="bi" :class="i <= selectedFeedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Feedback Section -->
          <div class="feedback-section">
            <h6 class="feedback-label">Feedback:</h6>
            <p class="feedback-text">{{ selectedFeedback.message }}</p>
          </div>

          <!-- Admin Response (if exists) -->
          <div v-if="selectedFeedback.admin_response" class="admin-response mt-3">
            <h6 class="admin-label">Respon Admin:</h6>
            <p class="admin-text">{{ selectedFeedback.admin_response }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  selectedFeedback: Object,
});
</script>

<style scoped>
.modal-content {
  border: none;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

.modal-body {
  padding: 24px;
  position: relative;
  overflow-x: hidden;
  word-wrap: break-word;
}

.btn-close {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 10;
  background: none;
  border: none;
  opacity: 0.6;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  cursor: pointer;
  color: #666;
}

.btn-close:hover {
  opacity: 1;
  background-color: rgba(0, 0, 0, 0.1);
  transform: rotate(90deg);
  color: #333;
}

.btn-close i {
  font-size: 16px;
  transition: inherit;
}

.user-content {
  margin-right: 50px; /* Give space for close button */
}

.user-info {
  flex: 1;
}

.badge-rating-section {
  min-width: 120px;
  max-width: 150px;
}

.user-avatar {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 60px;
  height: 60px;
  background-color: #6c757d;
  color: white;
  font-weight: 600;
  font-size: 18px;
}

.user-name {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  margin: 0;
  word-break: break-word;
  line-height: 1.3;
}

.time-info {
  font-size: 14px;
  color: #666;
  word-break: break-word;
}

.category-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

/* Badge Colors */
.badge-pelaporan {
  background-color: #FFD1D1;
  color: #B91C1C;
}

.badge-verifikasi {
  background-color: #FFF3CD;
  color: #856404;
}

.badge-umum {
  background-color: #D1FAE5;
  color: #065F46;
}

.badge-cari-laporan {
  background-color: #BFDBFE;
  color: #1E40AF;
}

.badge-lapor-map {
  background-color: #E9D5FF;
  color: #7E22CE;
}

.badge-default {
  background-color: #f8f9fa;
  color: #6c757d;
}

.rating-stars {
  color: #ffc107;
  font-size: 14px;
}

.rating-stars .bi-star {
  color: #dee2e6;
}

.feedback-section {
  margin-top: 20px;
}

.feedback-label {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.feedback-text {
  font-size: 14px;
  color: #333;
  line-height: 1.5;
  margin: 0;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

.admin-response {
  background-color: #f8f9fa;
  padding: 16px;
  border-radius: 8px;
  border-left: 4px solid #0d6efd;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.admin-label {
  font-size: 14px;
  font-weight: 600;
  color: #0d6efd;
  margin-bottom: 8px;
}

.admin-text {
  font-size: 14px;
  color: #333;
  line-height: 1.5;
  margin: 0;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

@media (max-width: 767.98px) {
  .modal-dialog {
    margin: 16px;
    width: calc(100% - 32px);
  }
  
  .modal-content {
    width: 100%;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .user-avatar {
    width: 50px;
    height: 50px;
  }
  
  .avatar-placeholder {
    width: 50px;
    height: 50px;
    font-size: 16px;
  }
  
  .user-name {
    font-size: 16px;
  }
  
  .user-content {
    margin-right: 40px; /* Reduced margin for mobile */
  }
  
  .badge-rating-section {
    min-width: 100px;
    max-width: 120px;
  }
  
  .btn-close {
    width: 28px;
    height: 28px;
  }
  
  .btn-close i {
    font-size: 14px;
  }
}
</style>