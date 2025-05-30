<template>
  <div class="report-card" @click="$emit('open-detail', report)" role="button" tabindex="0" :aria-label="`Lihat detail laporan ${report.category} - ${report.description}`">
    <!-- Card Image (Clickable for Evidence) -->
    <div class="report-card__image" @click.stop="openImageModal(report.evidence || report.image)">
      <img v-if="report.evidence || report.image" :src="report.evidence || report.image" :alt="`Gambar laporan ${report.category}`" class="card-image" />
      <div v-else class="card-image-placeholder">
        <i :class="getCategoryIcon(report.category)"></i>
      </div>
    </div>

    <!-- User Profile -->
    <div class="user-profile">
      <div class="user-details">
        <div class="user-avatar">
          <img
            v-if="report.user && report.user.avatar_url"
            :src="report.user.avatar_url"
            :alt="`Avatar ${report.user.name || report.reporter}`"
            class="avatar-image"
          />
        </div>
        <div class="user-info">
          <div class="user-name">{{ report.user?.name || report.reporter || 'Anonymous' }}</div>
          <div class="user-date">{{ formatDate(report.created_at) }}</div>
        </div>
        <div class="category-badge" :class="getCategoryClass(report.service)">
          {{ report.service }}
        </div>
      </div>
      <div v-if="report.location" class="location-badge">
        <i class="fas fa-map-marker-alt"></i>
        {{ report.location }}
      </div>
    </div>

    <!-- Card Content -->
    <div class="report-card__content">
      <!-- Title -->
      <h2 class="report-card__title">
        {{ report.title || 'Pelaporan ' + report.category }}
      </h2>

      <!-- Description -->
      <p class="report-card__description">
        {{ truncateText(report.description, 150) }}
      </p>
    </div>

    <!-- Card Footer -->
    <div class="report-card__footer">
      <button @click.stop="$emit('open-detail', report)" class="detail-button">
        Lihat Detail
      </button>
    </div>

    <!-- Image Modal for Zoom -->
    <div v-if="showImageModal" class="image-modal" @click="closeImageModal">
      <div class="image-modal__backdrop"></div>
      <div class="image-modal__container" @click.stop>
        <button class="image-modal__close" @click="closeImageModal">
          <i class="bi bi-x-square-fill"></i>
        </button>
        <div class="image-modal__content">
          <img 
            :src="modalImageSrc" 
            :alt="'Gambar laporan ' + report.category"
            class="image-modal__image"
            :class="{ 'zoomed': isZoomed }"
            @click="toggleZoom"
            ref="modalImage"
          />
        </div>
        <div class="image-modal__controls">
          <button @click="toggleZoom" class="zoom-button">
            <i :class="isZoomed ? 'fas fa-search-minus' : 'fas fa-search-plus'"></i>
            {{ isZoomed ? 'Zoom Out' : 'Zoom In' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ReportCard',
  props: {
    report: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ['open-detail', 'view-evidence'],
  data() {
    return {
      showImageModal: false,
      modalImageSrc: '',
      isZoomed: false,
    };
  },
  methods: {
    getCategoryClass(category) {
      const CATEGORY_CLASSES = {
        'Nomor Hp': 'category--phone',
        'Penipuan': 'category--fraud',
      };
      return CATEGORY_CLASSES[category] || 'category--default';
    },
    getCategoryIcon(category) {
      const CATEGORY_ICONS = {
        'Nomor Hp': 'fas fa-phone',
        'Penipuan': 'fas fa-exclamation-triangle',
      };
      return CATEGORY_ICONS[category] || 'fas fa-question-circle';
    },
    getUserInitial(name) {
      if (!name) return 'A';
      return name.charAt(0).toUpperCase();
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length <= length ? text : text.substring(0, length) + '...';
    },
    formatDate(date) {
      if (!date) return 'Tanggal tidak tersedia';
      const d = new Date(date);
      return d.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    openImageModal(imageSrc) {
      if (imageSrc) {
        this.modalImageSrc = imageSrc;
        this.showImageModal = true;
        this.isZoomed = false;
        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';
      }
    },
    closeImageModal() {
      this.showImageModal = false;
      this.isZoomed = false;
      // Restore body scroll
      document.body.style.overflow = '';
    },
    toggleZoom() {
      this.isZoomed = !this.isZoomed;
    },
  },
  beforeUnmount() {
    // Cleanup: restore body scroll if component is destroyed while modal is open
    if (this.showImageModal) {
      document.body.style.overflow = '';
    }
  },
};
</script>

<style scoped>
.report-card {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  width: 350px; /* Fixed width for consistency */
  height: 550px; /* Fixed height for consistency */
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  position: relative;
}

.report-card__image {
  height: 250px; /* Fixed height for image */
  overflow: hidden;
  position: relative;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.card-image:hover {
  transform: scale(1.05);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: #6c757d;
}

.user-profile {
  padding: 12px 16px; /* Reduced top/bottom padding */
  display: flex;
  flex-direction: column;
  position: relative;
  border-bottom: 1px solid #f8f9fa;
  flex-shrink: 0;
}

.category-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  color: white;
  text-transform: capitalize;
  white-space: nowrap;
  flex-shrink: 0;
}

.category--fraud {
  background: rgba(1, 128, 255, 1);
}

.category--default {
  background: rgba(92, 111, 123, 1);
}

.user-details {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: #007bff;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
}

.user-info {
  flex: 1;
  min-width: 0; /* Allow text to truncate */
}

.user-name {
  font-weight: 600;
  color: #212529;
  font-size: 0.9rem;
}

.user-date {
  font-size: 0.8rem;
  color: #6c757d;
  margin-top: 4px;
}

.location-badge {
  position: absolute;
  top: 12px;
  right: 16px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 6px 10px;
  border-radius: 15px;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

.location-badge i {
  font-size: 0.7rem;
}

.report-card__content {
  padding: 16px;
  flex: 1;
  overflow: hidden;
}

.report-card__title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #212529;
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.report-card__description {
  color: #6c757d;
  font-size: 0.9rem;
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.report-card__footer {
  padding: 12px;
  text-align: center;
  flex-shrink: 0;
}

.detail-button {
  background: #ffffff;
  color: #007bff;
  border: 2px solid #007bff;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 600;
  transition: all 0.3s ease;
  width: 100%;
  max-width: 200px;
}

.detail-button:hover {
  background: #007bff;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

/* Image Modal Styles */
.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-modal__backdrop {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(5px);
}

.image-modal__container {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.image-modal__close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 40px;
  height: 40px;
  border: none;
  color: white;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  z-index: 10;
  transition: background 0.3s ease;
}

.image-modal__content {
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  max-height: 70vh;
}

.image-modal__image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  cursor: pointer;
  transition: transform 0.3s ease;
  border-radius: 8px;
}

.image-modal__image.zoomed {
  transform: scale(2);
  cursor: grab;
}

.image-modal__image.zoomed:active {
  cursor: grabbing;
}

.image-modal__controls {
  padding: 16px;
  background: #f8f9fa;
  display: flex;
  justify-content: center;
  gap: 12px;
}

.zoom-button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background 0.3s ease;
}

.zoom-button:hover {
  background: #0056b3;
}

@media (max-width: 768px) {
  .report-card {
    width: 300px;
    height: 500px;
  }
  
  .report-card__image {
    height: 200px;
  }

  .report-card__title {
    font-size: 1.3rem;
  }

  .report-card__description {
    -webkit-line-clamp: 2;
    line-clamp: 2;
  }

  .image-modal__container {
    max-width: 95vw;
    max-height: 95vh;
  }

  .image-modal__content {
    max-height: 60vh;
  }

  .category-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
  }
}
</style>