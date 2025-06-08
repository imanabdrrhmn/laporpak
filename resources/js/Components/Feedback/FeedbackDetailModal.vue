<template>
  <div class="modal fade" ref="modal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Detail Feedback</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" v-if="selectedFeedback">
          <div class="d-flex align-items-center mb-3">
            <img
              v-if="selectedFeedback.user?.avatar_url"
              :src="selectedFeedback.user.avatar_url"
              alt="Avatar"
              class="rounded-circle me-3"
              style="width: 48px; height: 48px; object-fit: cover;"
            />
            <div
              v-else
              class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
              style="width: 48px; height: 48px;"
            >
              {{ selectedFeedback.user?.name?.charAt(0) }}{{ selectedFeedback.user?.name?.split(' ').length > 1 ? selectedFeedback.user.name.split(' ')[1].charAt(0) : '' }}
            </div>
            <div>
              <h5 class="mb-0 text-break">{{ selectedFeedback.user?.name }}</h5>
              <div class="text-muted small">
                {{ selectedFeedback.created_at }}
                <template v-if="selectedFeedback.is_updated"> (diperbarui)</template>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <span
              :class="[
                'badge',
                'rounded-pill',
                selectedFeedback.kategori === 'Verifikasi' ? 'bg-verifikasi text-verifikasi' :
                selectedFeedback.kategori === 'Pelaporan' ? 'bg-pelaporan text-pelaporan' :
                selectedFeedback.kategori === 'Umum' ? 'bg-umum text-umum' :
                selectedFeedback.kategori === 'Cari Laporan' ? 'bg-cari-laporan text-cari-laporan' :
                'bg-purple text-purple'
              ]"
            >
              {{ selectedFeedback.kategori }}
            </span>
          </div>
          <div class="text-warning mb-3">
            <span v-for="i in 5" :key="i">
              <i class="bi" :class="i <= selectedFeedback.rating ? 'bi-star-fill' : 'bi-star'"></i>
            </span>
          </div>
          <div class="border-top pt-3 mt-3">
            <div class="mb-3">
              <label class="form-label fw-bold">Feedback:</label>
              <p class="text-break">{{ selectedFeedback.message }}</p>
            </div>
          </div>
          <div v-if="selectedFeedback.admin_response" class="bg-light p-3 rounded mt-3">
            <h6 class="fw-bold">Respon Admin:</h6>
            <p class="mb-0">{{ selectedFeedback.admin_response }}</p>
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
.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

.bg-pelaporan {
  background-color: #FFD1D1;
}

.text-pelaporan {
  color: #B91C1C;
}

.bg-verifikasi {
  background-color: #FFF3CD;
}

.text-verifikasi {
  color: #856404;
}

.bg-umum {
  background-color: #D1FAE5;
}

.text-umum {
  color: #065F46;
}

.bg-cari-laporan {
  background-color: #BFDBFE;
}

.text-cari-laporan {
  color: #1E40AF;
}

.bg-purple {
  background-color: #E9D5FF;
}

.text-purple {
  color: #7E22CE;
}

@media (max-width: 767.98px) {
  .modal-footer {
    justify-content: center;
  }

  .modal-footer .btn {
    flex-grow: 1;
  }
}
</style>