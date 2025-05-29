<template>
  <div
    class="modal-overlay"
    :class="{ active: isOpen }"
    v-if="isOpen"
    @click.self="close"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalTitle"
  >
    <div class="modal-container" :class="{ active: isOpen }" role="document">
      <div class="modal-content">
        <header class="modal-header">
          <h2 id="modalTitle">Laporkan</h2>
          <button
            type="button"
            class="close-button"
            aria-label="Close modal"
            @click="close"
            :disabled="isLoading"
          >
            ✕
          </button>
        </header>

        <section class="modal-body">
          <label for="reasonInput" class="form-label">Alasan Pelaporan</label>
          <textarea
            id="reasonInput"
            v-model="reason"
            placeholder="Masukkan alasan mengapa laporan ini hoax atau misinformasi"
            rows="4"
            :disabled="isLoading"
            class="form-control"
          ></textarea>
        </section>

        <footer class="modal-footer">
          <button
            class="btn btn-secondary"
            @click="close"
            :disabled="isLoading"
          >Batal</button>

          <button
            class="btn btn-primary"
            @click="submitReport"
            :disabled="isLoading"
          >
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
      type: [String, Number],
      required: true,
    },
  },
  emits: ['close', 'reported'],
  data() {
    return {
      reason: '',
      isLoading: false,
    };
  },
  methods: {
    close() {
      if (this.isLoading) return;
      this.reason = '';
      this.$emit('close');
    },
    submitReport() {
      if (!this.reportId) {
        alert('Laporan tidak valid.');
        return;
      }

      this.isLoading = true;

      Inertia.post(
        route('laporan.flag'), // Pastikan route ini sesuai dengan backend-mu
        {
          report_id: this.reportId,
          reason: this.reason,
        },
        {
          onSuccess: () => {
            alert('Laporan berhasil dikirim, terima kasih.');
            this.reason = '';
            this.$emit('reported');
            this.close();
          },
          onError: (errors) => {
            alert(errors.reason || 'Terjadi kesalahan saat mengirim laporan.');
          },
          onFinish: () => {
            this.isLoading = false;
          },
        }
      );
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
  z-index: 1050;
  padding: 1rem;
  backdrop-filter: blur(5px);
}

.modal-container {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 450px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: scale(0.95);
  transition: transform 0.3s ease;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

.modal-container.active {
  transform: scale(1);
}

.modal-header {
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
}

.close-button {
  background: transparent;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  line-height: 1;
  color: #333;
}

.modal-body {
  padding: 1rem 1.5rem;
  flex: 1;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #444;
}

.form-control {
  width: 100%;
  resize: vertical;
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
  font-family: inherit;
}

.modal-footer {
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  border-top: 1px solid #eee;
}

.btn {
  padding: 0.6rem 1.3rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s ease;
  font-family: inherit;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}
</style>
