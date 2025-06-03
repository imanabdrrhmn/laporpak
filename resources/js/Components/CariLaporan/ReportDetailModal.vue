<template>
  <div
    class="modal-overlay"
    :class="{ active: isOpen }"
    v-if="isOpen"
    @click.self="handleClose"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalTitle"
  >
    <div class="modal-container" :class="{ active: isOpen }" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div class="header-content">
            <div class="icon-wrapper">
              <svg class="report-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
              </svg>
            </div>
            <div>
              <h2 class="modal-title" id="modalTitle">Detail Laporan</h2>
              <p class="modal-subtitle">Informasi lengkap mengenai laporan</p>
            </div>
          </div>
          <button
            type="button"
            class="close-button"
            aria-label="Close"
            @click="handleClose"
            :disabled="isLoading || flagModalOpen"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <div class="modal-body" v-if="report && report.id">
          <div class="info-grid">
          </div>
          <div class="description-section">
            <div class="section-header">
              <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
              </svg>
              <h3>Deskripsi</h3>
            </div>
            <div class="description-content">
              <p v-if="report.description">{{ report.description }}</p>
              <p v-else class="no-data">Tidak ada deskripsi tersedia.</p>
            </div>
          </div>

          <div class="evidence-section">
            <div class="section-header">
              <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21,15 16,10 5,21"/>
              </svg>
              <h3>Bukti Laporan</h3>
            </div>
            <div class="evidence-content">
              <div v-if="report.evidence" class="evidence-wrapper">
                <div class="image-container" @click="openImageModal" @keyup.enter="openImageModal" tabindex="0">
                  <img
                    :src="report.evidence"
                    alt="Bukti Laporan"
                    class="evidence-image"
                    loading="lazy"
                  />
                  <div class="image-overlay">
                    <svg class="zoom-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <circle cx="11" cy="11" r="8"/>
                      <path d="M21 21l-4.35-4.35"/>
                      <line x1="11" y1="8" x2="11" y2="14"/>
                      <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                    <span>Klik untuk memperbesar</span>
                  </div>
                </div>
              </div>
              <div v-else class="no-evidence">
                <svg class="no-evidence-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <line x1="9" y1="9" x2="15" y2="15"/>
                  <line x1="15" y1="9" x2="9" y2="15"/>
                </svg>
                <p>Tidak ada bukti visual yang tersedia untuk laporan ini.</p>
              </div>
            </div>
          </div>

          <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e9ecef;">
            <button
                class="btn btn-danger"
                @click="openFlagModal"
                :disabled="flagModalOpen || !user"
                v-if="user" 
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:16px; height:16px; margin-right: 0.5rem;">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                <line x1="4" y1="22" x2="4" y2="15"></line>
              </svg>
              Laporkan
            </button>
          </div>
        </div>
        <div v-else class="modal-body no-data-container">
            <p>Data laporan tidak tersedia atau tidak dapat dimuat.</p>
        </div>

        <div class="modal-footer">
          <div class="footer-actions">
            <button class="btn btn-secondary" @click="handleClose" :disabled="isLoading || flagModalOpen">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                 <path d="M18 6L6 18M6 6l12 12" /> </svg>
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <ReportFlagModal
      :isOpen="flagModalOpen"
      :reportId="report ? report.id : null"
      :user="user"
      @close="flagModalOpen = false"
      @reported="onFlagReported"
      @error="onFlagError"
    />

    <div v-if="imageModalOpen" class="image-modal-overlay" @click="closeImageModal" @keyup.esc="closeImageModal">
      <div class="image-modal-content" @click.stop>
        <button class="image-close-btn" @click="closeImageModal" aria-label="Close image view">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
        <img :src="report.evidence" alt="Bukti Laporan - Tampilan Penuh" class="full-image" />
      </div>
    </div>
  </div>
</template>

<script>
import ReportFlagModal from './ReportFlagModal.vue';

export default {
  name: 'ReportDetailModal',
  components: { ReportFlagModal },
  props: {
    isOpen: Boolean,
    report: {
      type: Object,
      default: () => ({}),
    },
    user: { 
      type: Object,
      default: () => null,
    },
  },
  emits: ['close'],
  data() {
    return {
      imageModalOpen: false,
      flagModalOpen: false,
      isLoading: false,
    };
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '-';
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    openImageModal() {
      if (this.report && this.report.evidence) {
        this.imageModalOpen = true;
      }
    },
    closeImageModal() {
      this.imageModalOpen = false;
    },
    openFlagModal() {
        if (this.report && this.report.id) {
            this.flagModalOpen = true;
        } else {
            alert('Tidak ada ID laporan yang valid untuk dilaporkan.');
        }
    },
    handleClose() {
      if (this.isLoading || this.flagModalOpen) return;
      this.$emit('close');
    },
    onFlagReported(payload) {
      this.flagModalOpen = false;
      this.handleClose();
    },
    onFlagError(errorMessage) {
      console.warn('Gagal melaporkan:', errorMessage);
    }
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 1050;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.modal-overlay.active {
  opacity: 1;
}

.modal-container {
  max-width: 900px;
  width: 100%;
  max-height: 95vh;
  transform: scale(0.95) translateY(20px);
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-container.active {
  transform: scale(1) translateY(0);
}

.modal-content {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 2rem);
}

.modal-header {
  background: #007bff;
  color: white;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.icon-wrapper {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.75rem;
  border-radius: 12px;
}

.report-icon {
  width: 24px;
  height: 24px;
  stroke: white;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

.modal-subtitle {
  font-size: 0.9rem;
  opacity: 0.85;
  margin: 0.25rem 0 0 0;
}

.close-button {
  border: none;
  padding: 0.5rem;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  transform-origin: center;
  background: transparent;
}

.close-button:hover:not(:disabled) {
  transform: rotate(90deg) scale(1.1);
  background: rgba(255,255,255,0.1);
}

.close-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.close-button svg {
  width: 24px;
  height: 24px;
  stroke-width: 2;
  stroke: white;
}

.modal-body {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
  scrollbar-width: thin;
  scrollbar-color: #6c757d #f8f9fa;
}

.modal-body::-webkit-scrollbar {
  width: 8px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f8f9fa;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
  background-color: #adb5bd;
  border-radius: 4px;
  border: 2px solid #f8f9fa;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background-color: #6c757d;
}

.no-data-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    color: #6c757d;
    font-style: italic;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.info-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid #e9ecef;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
}

.info-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.info-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.info-icon {
  width: 20px;
  height: 20px;
  color: #007bff;
}

.info-label {
  font-size: 0.875rem;
  color: #6c757d;
  font-weight: 500;
}

.info-value {
  font-size: 1rem;
  font-weight: 600;
  color: #212529;
  line-height: 1.4;
  word-break: break-word;
}

.category-tag {
  background: #007bff;
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 16px;
  font-size: 0.875rem;
  font-weight: 500;
  display: inline-block;
}

.description-section,
.evidence-section {
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #e9ecef;
}

.section-icon {
  width: 22px;
  height: 22px;
  color: #007bff;
}

.section-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #212529;
  margin: 0;
}

.description-content {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1.25rem;
  border-left: 4px solid #007bff;
}

.description-content p {
  margin: 0;
  line-height: 1.6;
  color: #495057;
  white-space: pre-wrap;
}

.no-data {
  color: #6c757d;
  font-style: italic;
}

.evidence-wrapper {
  display: flex;
  justify-content: center;
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
}

.image-container {
  position: relative;
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  max-width: 100%;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: 1px solid #dee2e6;
}

.image-container:hover {
  transform: scale(1.02);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.evidence-image {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
  display: block;
}

.image-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  opacity: 0;
  transition: opacity 0.2s ease;
  gap: 0.5rem;
  text-align: center;
  padding: 1rem;
}

.image-container:hover .image-overlay, .image-container:focus .image-overlay {
  opacity: 1;
}

.zoom-icon {
  width: 32px;
  height: 32px;
}

.no-evidence {
  text-align: center;
  padding: 3rem 1rem;
  color: #6c757d;
  background-color: #f8f9fa;
  border-radius: 8px;
  border: 1px dashed #dee2e6;
}

.no-evidence-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.modal-footer {
  background: #f8f9fa;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e9ecef;
}

.footer-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.95rem;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.btn:hover:not(:disabled) {
  transform: translateY(-1px);
}

.btn svg {
  width: 16px;
  height: 16px;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: 1px solid #6c757d;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
  border-color: #545b62;
}

.btn-primary {
  background: #007bff;
  color: white;
  border: 1px solid #007bff;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
  border-color: #0056b3;
}

.btn-danger {
  background: #dc3545;
  color: white;
  border: 1px solid #dc3545;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
  border-color: #bd2130;
}

.image-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  z-index: 1100;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.image-modal-content {
  position: relative;
  max-width: 95vw;
  max-height: 95vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.image-close-btn {
  position: absolute;
  top: -2.5rem;
  right: -0.5rem;
  background: rgba(255, 255, 255, 0.15);
  border: none;
  padding: 0.75rem;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  backdrop-filter: blur(5px);
  transition: background-color 0.2s ease, transform 0.2s ease;
  line-height: 0;
}

.image-close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.image-close-btn svg {
  width: 20px;
  height: 20px;
  stroke: white;
}

.full-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
  .modal-overlay {
    padding: 0.5rem;
  }

  .modal-header {
    padding: 1rem 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .footer-actions {
    flex-direction: column;
    gap: 0.75rem;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .image-modal-overlay {
    padding: 0.5rem;
  }

  .image-close-btn {
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0,0,0,0.5);
  }
}
</style>
