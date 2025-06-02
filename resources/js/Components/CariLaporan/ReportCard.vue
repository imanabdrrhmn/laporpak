<template>
  <div v-if="showImageModal" class="image-modal-overlay" @click="closeImageModal">
      <div class="image-modal-container" @click.stop>
        <button class="image-modal__close" @click="closeImageModal" aria-label="Tutup pratinjau gambar">
          <i class="fas fa-times"></i>
        </button>
        <div class="image-modal__content">
          <img 
            :src="modalImageSrc" 
            :alt="`Gambar laporan ${report.category}`"
            class="image-modal__image"
            ref="modalImageRef"
          />
        </div>
      </div>
    </div>
  <div class="report-card" @click="openDetail" role="button" tabindex="0" :aria-label="`Lihat detail laporan ${report.category || 'Tidak Diketahui'} - ${report.description ? report.description.substring(0, 50) + '...' : 'Tanpa deskripsi'}`">
    <div class="report-card__image" @click.stop="openImageModalHandler">
      <img v-if="report.evidence" :src="report.evidence" :alt="`Gambar laporan ${report.category}`" class="card-image" loading="lazy" />
      <div v-else class="card-image-placeholder">
        <i :class="getCategoryIcon(report.category)" class="placeholder-icon"></i>
      </div>
    </div>

    <div class="user-profile">
      <div class="user-details">
        <div class="user-avatar">
          <img
            v-if="report.user && report.user.avatar_url"
            :src="report.user.avatar_url"
            :alt="`Avatar ${report.user.name || 'Pengguna Anonim'}`"
            class="avatar-image"
            loading="lazy"
          />
          <div v-else class="avatar-placeholder">
            <i class="fas fa-user"></i>
          </div>
        </div>
        <div class="user-info">
          <div class="user-name">{{ report.user?.name || 'Pengguna Anonim' }}</div>
          <div class="user-date">{{ formatDate(report.created_at) }}</div>
        </div>
        <div class="category-badge" :class="getCategoryClass(report.service)">
          {{ report.service || 'Layanan Umum' }}
        </div>
      </div>
      <div v-if="report.location" class="location-badge">
        <i class="fas fa-map-marker-alt location-icon"></i>
        <span class="location-text">{{ truncateText(report.region, 30) }}</span>
      </div>
    </div>

    <div class="report-card__content">
      <h2 class="report-card__title">
        {{ report.source }}
      </h2>
      <p class="report-card__description">
        {{ truncateText(report.description, 120) }}
      </p>
    </div>

    <div class="report-card__footer">
      <button @click.stop="openDetail" class="detail-button">
        <i class="fas fa-eye button-icon"></i>
        Lihat Detail
      </button>
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
      default: () => ({
        id: null,
        evidence: null,
        category: 'Umum',
        user: { id: null, name: 'Anonim', avatar_url: null },
        created_at: new Date().toISOString(),
        service: 'Lainnya',
        location: 'Tidak diketahui',
        title: '',
        description: 'Tidak ada deskripsi.',
      }),
    },
  },
  emits: ['open-detail', 'open-flag'],
  data() {
    return {
      showImageModal: false,
      modalImageSrc: '',
    };
  },
  methods: {
    openDetail() {
      this.$emit('open-detail', this.report);
    },
    getCategoryClass(service) {
      const SERVICE_CLASSES = {
        'Penipuan': 'category--fraud',
        'Infrastruktur': 'category--infrastructure',
      };
      return SERVICE_CLASSES[service] || 'category--default';
    },
    getCategoryIcon(category) {
      const CATEGORY_ICONS = {
        'Nomor Hp': 'fas fa-phone-alt',
        'Penipuan': 'fas fa-user-secret',
        'Email': 'fas fa-envelope',
        'Infrastruktur': 'fas fa-hard-hat',
      };
      return CATEGORY_ICONS[category] || 'fas fa-file-alt';
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length <= length ? text : text.substring(0, length) + '...';
    },
    formatDate(dateString) {
      if (!dateString) return 'Tanggal tidak tersedia';
      const date = new Date(dateString);
      const options = {
        year: 'numeric', month: 'long', day: 'numeric',
      };
      return date.toLocaleDateString('id-ID', options);
    },
    openImageModalHandler() {
      const imageSrc = this.report.evidence;
      if (imageSrc) {
        this.modalImageSrc = imageSrc;
        this.showImageModal = true;
      }
    },
    closeImageModal() {
      this.showImageModal = false;
      document.body.style.overflow = '';
    },
  },
  beforeUnmount() {
    if (this.showImageModal) {
      document.body.style.overflow = '';
    }
  },
};
</script>

<style scoped>
.report-card {
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0, 32, 64, 0.08);
  cursor: pointer;
  width: 100%;
  max-width: 380px;
  height: auto;
  min-height: 480px;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #e7edf3;
}

.report-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(0, 32, 64, 0.12);
}

.report-card__image {
  height: 220px;
  overflow: hidden;
  position: relative;
  background-color: #f0f4f8;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.report-card:hover .card-image {
  transform: scale(1.05);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}
.placeholder-icon {
  font-size: 3.5rem;
  color: #adb5bd;
}

.user-profile {
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  border-bottom: 1px solid #e7edf3;
}

.user-details {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.avatar-placeholder {
  width: 100%;
  height: 100%;
  background-color: #6c757d;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name {
  font-weight: 600;
  color: #343a40;
  font-size: 0.95rem;
  margin-bottom: 2px;
}

.user-date {
  font-size: 0.75rem;
  color: #6c757d;
}

.category-badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  color: white;
  text-transform: capitalize;
  white-space: nowrap;
  margin-left: auto;
  align-self: flex-start;
}

.category--fraud { background-color: #dc3545; }
.category--infrastructure { background-color: #ffc107; }
.category--public-service { background-color: #17a2b8; }
.category--default { background-color: #6c757d; }

.location-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8rem;
  color: #495057;
  background-color: #f8f9fa;
  padding: 4px 8px;
  border-radius: 6px;
  align-self: flex-start;
  margin-top: 4px;
}
.location-icon {
  color: #0d6efd;
}
.location-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.report-card__content {
  padding: 16px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.report-card__title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #212529;
  margin: 0 0 10px 0;
  line-height: 1.3;
}

.report-card__description {
  color: #495057;
  font-size: 0.875rem;
  line-height: 1.6;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-grow: 1;
}

.report-card__footer {
  padding: 12px 16px;
  border-top: 1px solid #e7edf3;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.detail-button, .flag-button {
  background-color: transparent;
  border: 1px solid #6c757d;
  color: #495057;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 600;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex-grow: 1;
}

.detail-button {
  border-color: #0d6efd;
  color: #0d6efd;
}

.detail-button:hover {
  background-color: #0d6efd;
  color: white;
}

.flag-button {
  border-color: #dc3545;
  color: #dc3545;
}

.flag-button:hover {
  background-color: #dc3545;
  color: white;
}

.button-icon {
  font-size: 0.9em;
}

.image-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.85);
  z-index: 5000;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
  backdrop-filter: blur(4px);
}

.image-modal-container {
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
  top: 10px;
  right: 10px;
  width: 36px;
  height: 36px;
  background-color: rgba(0,0,0,0.5);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  z-index: 10;
  transition: background-color 0.2s ease;
}
.image-modal__close:hover {
  background-color: rgba(0,0,0,0.7);
}
.image-modal__close i {
  line-height: 1;
}

.image-modal__content {
  overflow: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}

.image-modal__image {
  max-width: 100%;
  max-height: calc(90vh - 40px);
  object-fit: contain;
  border-radius: 8px;
  display: block;
}

@media (max-width: 768px) {
  .report-card {
    max-width: none;
    min-height: auto;
  }
  .report-card__image {
    height: 180px;
  }
  .report-card__title {
    font-size: 1.1rem;
  }
  .report-card__description {
    -webkit-line-clamp: 3;
    line-clamp: 3;
  }
  .user-profile {
    padding: 10px 12px;
  }
  .user-avatar {
    width: 36px;
    height: 36px;
  }
  .user-name {
    font-size: 0.85rem;
  }
  .user-date {
    font-size: 0.7rem;
  }
  .category-badge {
    font-size: 0.7rem;
    padding: 4px 10px;
  }
  .location-badge {
    font-size: 0.75rem;
    padding: 3px 7px;
  }
  .report-card__content {
    padding: 12px;
  }
  .report-card__footer {
    padding: 10px 12px;
    flex-direction: row;
  }
  .detail-button, .flag-button {
    font-size: 0.8rem;
    padding: 7px 12px;
  }
}
</style>
