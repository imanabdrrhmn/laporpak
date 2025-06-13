<template>
  <div class="modal-overlay" :class="{ active: isOpen }" v-if="isOpen" @click.self="handleClose" role="dialog" aria-modal="true" aria-labelledby="flagModalTitle">
    <div class="modal-container" :class="{ active: isOpen }" role="document">
      <div class="modal-content">
        <header class="modal-header">
          <h2 id="flagModalTitle">Laporkan Konten</h2>
          <button type="button" class="close-button" aria-label="Close modal" @click="handleClose" :disabled="isLoading">✕</button>
        </header>
        <section class="modal-body">
          <p v-if="reportId" class="report-info">Anda akan melaporkan konten dengan ID: <strong>{{ reportId }}</strong></p>
          <label for="reasonSelect" class="form-label">Alasan Pelaporan</label>
          <select id="reasonSelect" v-model="reason" :disabled="isLoading" class="form-control" aria-describedby="reasonError">
            <option disabled value="">-- Pilih alasan --</option>
            <option value="Hoax">Hoax</option>
            <option value="Spam">Spam</option>
            <option value="Konten Tidak Pantas">Konten Tidak Pantas</option>
            <option value="Pelanggaran Hak Cipta">Pelanggaran Hak Cipta</option>
            <option value="Informasi Pribadi">Informasi Pribadi</option>
            <option value="Lainnya">Lainnya</option>
          </select>
          <p v-if="validationError" id="reasonError" class="error-message">{{ validationError }}</p>
        </section>
        <footer class="modal-footer">
          <button class="btn btn-secondary" @click="handleClose" :disabled="isLoading">Batal</button>
          <button class="btn btn-primary" @click="submitReport" :disabled="isLoading || !reason">
            <span v-if="isLoading">Mengirim...</span>
            <span v-else>Laporkan</span>
          </button>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
  name: 'ReportFlagModal',
  props: {
    isOpen: Boolean,
    reportId: {
      type: [String, Number, null],
      required: false,
      default: null,
    },
  },
  emits: ['close', 'reported', 'error', 'submitting'],
  data() {
    return {
      reason: '',
      isLoading: false,
      validationError: '',
    };
  },
  methods: {
    handleClose() {
      if (this.isLoading) return;
      this.resetForm();
      this.$emit('close');
    },
    resetForm() {
      this.reason = '';
      this.validationError = '';
    },
    submitReport() {
      this.validationError = '';
      if (!this.reportId) { this.validationError = 'ID laporan tidak valid.'; this.$emit('error', 'ID laporan tidak valid.'); return; }
      if (!this.reason) { this.validationError = 'Silakan pilih alasan pelaporan.'; return; }

      this.isLoading = true;
      this.$emit('submitting', true);

      Inertia.post(route('laporan.flag'), {
          report_id: this.reportId,
          reason: this.reason,
        }, {
          preserveScroll: true,
          onSuccess: (page) => { this.$emit('reported'); },
          onError: (errors) => {
            const firstErrorKey = Object.keys(errors)[0];
            this.$emit('error', firstErrorKey ? errors[firstErrorKey] : 'Terjadi kesalahan.');
          },
          onFinish: () => {
            this.$emit('submitting', false);
            this.isLoading = false;
            this.handleClose();
          },
        }
      );
    },
  },
  watch: {
    isOpen(newVal) {
      if (newVal) { this.resetForm(); }
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1060;
  padding: 1rem;
  backdrop-filter: blur(5px);
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}
.modal-overlay.active {
  opacity: 1;
}
.modal-container {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 450px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: scale(0.95) translateY(10px);
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  opacity: 0;
}
.modal-container.active {
  transform: scale(1) translateY(0);
  opacity: 1;
}
.modal-header {
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e9ecef;
}
#flagModalTitle {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
  color: #343a40;
}
.close-button {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  font-weight: 300;
  cursor: pointer;
  line-height: 1;
  color: #6c757d;
  padding: 0.25rem 0.5rem;
}
.close-button:hover {
  color: #343a40;
}
.modal-body {
  padding: 1.5rem;
  flex: 1;
  overflow-y: auto;
}
.report-info {
  font-size: 0.9rem;
  color: #495057;
  margin-bottom: 1rem;
  background-color: #f8f9fa;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  border: 1px solid #e9ecef;
}
.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #495057;
  font-size: 0.9rem;
}
.form-control {
  width: 100%;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #ced4da;
  font-size: 1rem;
  font-family: inherit;
  background-color: #fff;
  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.form-control:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
}
.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
.modal-footer {
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  border-top: 1px solid #e9ecef;
  background-color: #f8f9fa;
}
.btn {
  padding: 0.6rem 1.3rem;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s ease, transform 0.1s ease;
  font-family: inherit;
  font-size: 0.95rem;
}
.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}
.btn:hover:not(:disabled) {
  transform: translateY(-1px);
}
.btn-secondary {
  background-color: #6c757d;
  color: white;
  border: 1px solid #6c757d;
}
.btn-secondary:hover:not(:disabled) {
  background-color: #5a6268;
  border-color: #545b62;
}
.btn-primary {
  background-color: #007bff;
  color: white;
  border: 1px solid #007bff;
}
.btn-primary:hover:not(:disabled) {
  background-color: #0056b3;
  border-color: #0056b3;
}
</style>