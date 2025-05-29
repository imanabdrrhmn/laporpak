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
        <!-- Header -->
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
              <p class="modal-subtitle">Informasi lengkap laporan</p>
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

        <!-- Body -->
        <div class="modal-body">
          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-card">
              <div class="info-header">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="info-label">Tanggal Laporan</span>
              </div>
              <div class="info-value">{{ formatDate(report.created_at) }}</div>
            </div>

            <div class="info-card">
              <div class="info-header">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7 7-7z"/>
                </svg>
                <span class="info-label">Kategori</span>
              </div>
              <div class="info-value">
                <span class="category-tag">{{ report.category }}</span>
              </div>
            </div>

            <div class="info-card" v-if="report.source">
              <div class="info-header">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="info-label">Sumber</span>
              </div>
              <div class="info-value">{{ report.source }}</div>
            </div>

            <div class="info-card" v-if="report.address">
              <div class="info-header">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                <span class="info-label">Alamat</span>
              </div>
              <div class="info-value">{{ report.address }}</div>
            </div>
          </div>

          <!-- Description Section -->
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

          <!-- Evidence Section -->
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
                <div class="image-container" @click="openImageModal">
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

          <!-- Tombol buka modal laporan hoax -->
          <div style="margin-top: 1rem;">
            <button class="btn btn-primary" @click="flagModalOpen = true" :disabled="isLoading">
              Laporkan
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <div class="footer-actions">
            <button class="btn btn-secondary" @click="handleClose" :disabled="isLoading || flagModalOpen">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 11l3-3 3 3"/>
                <path d="M22 12h-20"/>
              </svg>
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal laporkan hoax terpisah -->
    <ReportFlagModal
      :isOpen="flagModalOpen"
      :reportId="report.id"
      @close="flagModalOpen = false"
      @reported="onFlagReported"
    />

    <!-- Image Modal -->
    <div v-if="imageModalOpen" class="image-modal-overlay" @click="closeImageModal">
      <div class="image-modal-content" @click.stop>
        <button class="image-close-btn" @click="closeImageModal">
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
      this.imageModalOpen = true;
      document.body.style.overflow = 'hidden';
    },
    closeImageModal() {
      this.imageModalOpen = false;
      document.body.style.overflow = 'auto';
    },
    handleClose() {
      if (this.isLoading || this.flagModalOpen) return;
      this.flagModalOpen = false;
      this.$emit('close');
      document.body.style.overflow = 'auto';
    },
    onFlagReported() {
      alert('Terima kasih, laporan berhasil dikirim.');
      this.flagModalOpen = false;
      this.handleClose();
    }
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        document.body.style.overflow = 'hidden';
      } else {
        this.flagModalOpen = false;
        this.closeImageModal();
        document.body.style.overflow = 'auto';
      }
    },
  },
  beforeUnmount() {
    document.body.style.overflow = 'auto';
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

/* Header */
.modal-header {
  background: #007bff; /* Change to solid blue */
  color: white;
  padding: 2rem;
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
  backdrop-filter: blur(10px);
}

.report-icon {
  width: 24px;
  height: 24px;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

.modal-subtitle {
  font-size: 0.9rem;
  opacity: 0.8;
  margin: 0.25rem 0 0 0;
}

.close-button {
  border: none;
  padding: 0.5rem;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  transform-origin: center;
  background: transparent;  /* Remove background */
}

.close-button:hover {
  transform: rotate(90deg);
}

.close-button svg {
  width: 24px;
  height: 24px;
  stroke-width: 2;
  transition: stroke-width 0.3s ease;
}

.close-button:hover svg {
  stroke-width: 2.5;
}

/* Body */
.modal-body {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}

.modal-body::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

/* Info Grid */
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
}

.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
  color: #667eea;
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
}

.category-tag {
  background: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Sections */
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
  color: #667eea;
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
  border-left: 4px solid #667eea;
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

/* Evidence */
.evidence-wrapper {
  display: flex;
  justify-content: center;
}

.image-container {
  position: relative;
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  max-width: 100%;
  transition: transform 0.2s ease;
}

.image-container:hover {
  transform: scale(1.02);
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
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  opacity: 0;
  transition: opacity 0.2s ease;
  gap: 0.5rem;
}

.image-container:hover .image-overlay {
  opacity: 1;
}

.zoom-icon {
  width: 32px;
  height: 32px;
}

.no-evidence {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

.no-evidence-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 1rem;
  opacity: 0.5;
}

/* Footer */
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
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn svg {
  width: 16px;
  height: 16px;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-1px);
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5a6fd8;
  transform: translateY(-1px);
}

/* Image Modal */
.image-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  z-index: 1100;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.image-modal-content {
  position: relative;
  max-width: 95vw;
  max-height: 95vh;
}

.image-close-btn {
  position: absolute;
  top: -3rem;
  right: 0;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  backdrop-filter: blur(10px);
}

.image-close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.image-close-btn svg {
  width: 20px;
  height: 20px;
}

.full-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-overlay {
    padding: 0.5rem;
  }
  
  .modal-header {
    padding: 1.5rem;
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
  }
  
  .btn {
    justify-content: center;
  }
}
</style>