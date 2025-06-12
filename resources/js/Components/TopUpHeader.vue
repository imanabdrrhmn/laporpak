<template>
  <div class="topup-header bg-light py-3 px-4 rounded-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-0 fs-4 fw-bold text-primary">Top Up Saldo</h2>
        <p class="mb-0 text-muted">Isi ulang saldo Anda untuk menikmati layanan premium kami.</p>
      </div>
      <div>
        <button class="btn btn-primary" @click="$emit('open-topup-modal')">
          <i class="bi bi-plus-circle me-2"></i>
          Top Up Sekarang
        </button>
      </div>
    </div>
    <div class="mt-3">
      <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
          <i class="bi bi-wallet2 fs-3 text-primary"></i>
        </div>
        <div class="flex-grow-1 ms-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="text-muted small">Saldo Anda</span>
              <h3 class="mb-0 fw-bold">{{ formatCurrency(currentBalance) }}</h3>
            </div>
            <div>
              <button class="btn btn-link text-decoration-none p-0" @click="$emit('open-history-modal')">
                <i class="bi bi-clock-history me-1"></i>
                Riwayat Transaksi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentBalance: {
    type: Number,
    required: true,
    default: 0
  }
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
};
</script>

<style scoped>
.topup-header {
  background-color: #f8f9fa;
  border-radius: 0.375rem;
  margin-bottom: 1.5rem;
}

.topup-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.topup-header p {
  margin-bottom: 0;
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0b5ed7;
}

.text-primary {
  color: #0d6efd !important;
}

.text-muted {
  color: #6c757d !important;
}

.wallet-icon {
  font-size: 2rem;
  color: #0d6efd;
}

.history-link {
  font-size: 0.875rem;
  color: #6c757d;
  text-decoration: none;
}

.history-link:hover {
  text-decoration: underline;
}
</style>