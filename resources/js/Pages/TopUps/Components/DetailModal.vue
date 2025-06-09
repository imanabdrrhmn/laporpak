<template>
  <div
    class="modal fade"
    id="detailModal"
    tabindex="-1"
    aria-labelledby="detailModalLabel"
    aria-hidden="true"
    ref="detailModalRef"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius: 0.75rem;">
        <div class="modal-body p-4">
          <div v-if="topUp">
            <h3 class="text-center fw-bold mb-1" style="font-size: 1.25rem;">Detail Top Up</h3>
            <p class="text-center fw-bold mb-3" style="font-size: 1.75rem;">
              Rp {{ formatCurrency(topUp.amount) }}
            </p>

            <div class="detail-item d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Status</span>
              <span
                class="badge rounded-pill"
                :class="statusBadgeClass(topUp.status)"
              >
                {{ topUp.status.toLowerCase() === 'success' ? 'Verified' : capitalize(topUp.status) }}
              </span>
            </div>
            <div class="detail-item d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Tanggal</span>
              <span>{{ formatDate(topUp.created_at) }}</span>
            </div>
            <div class="detail-item d-flex justify-content-between align-items-center mb-3">
              <span class="text-muted">Metode Pembayaran</span>
              <span>{{ topUp.payment_method || 'Tidak tersedia' }}</span>
            </div>

            <div v-if="proofImageUrl" class="text-center mt-3">
              <p class="fw-bold">Bukti Pembayaran:</p>
              <img
                :src="proofImageUrl"
                alt="Bukti Pembayaran"
                class="img-fluid rounded shadow-sm"
                style="max-height: 200px; max-width: 100%;"
              />
            </div>
            <div v-else-if="!proofImageUrl && topUp.status.toLowerCase() !== 'pending'" class="text-muted fst-italic text-center mt-3 small">
              Bukti pembayaran tidak tersedia.
            </div>
          </div>
          <div class="d-grid mt-4">
            <button type="button" class="btn btn-primary" @click="close">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';

const props = defineProps({
  topUp: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close']);

const detailModalRef = ref(null);
let modalInstance = null;

const proofImageUrl = computed(() => {
  if (!props.topUp?.proof) return '';
  return `/storage/${props.topUp.proof}`;
});

const formatCurrency = (value) =>
  new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const options = {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  };
  return new Intl.DateTimeFormat('id-ID', options).format(date).replace(/\./g, ':');
};

const statusBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case 'verified':
    case 'success':
      return 'status-badge approved';
    case 'pending':
      return 'status-badge pending';
    case 'failed':
    case 'gagal':
      return 'status-badge rejected';
    default:
      return 'status-badge rejected';
  }
};

const capitalize = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

const show = () => {
  if (!modalInstance && detailModalRef.value) {
    modalInstance = new Modal(detailModalRef.value);
  }
  modalInstance?.show();
};

const close = () => {
  modalInstance?.hide();
  emit('close');
};

defineExpose({ show, close });

onMounted(() => {
  if (detailModalRef.value) {
    modalInstance = new Modal(detailModalRef.value);
  }
});
</script>

<style scoped>
.status-badge.pending {
  background-color: #fff8e1;
  color: #ffa000;
}

.status-badge.approved {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status-badge.rejected {
  background-color: #ffebee;
  color: #c62828;
}

.badge {
  font-size: 0.78rem;
  padding: 0.45em 0.9em;
  font-weight: 500;
}

@media (max-width: 576px) {
  .modal-body p.text-center.fw-bold {
    font-size: 1.45rem !important;
  }
  .modal-body h3.fw-bold {
    font-size: 1.1rem !important;
  }
  .modal-body .detail-item span {
    font-size: 0.85rem;
  }
}
</style>
