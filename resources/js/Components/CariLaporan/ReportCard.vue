<template>
  <div class="report-card" @click="$emit('open-detail', report)" role="button" tabindex="0" :aria-label="`Lihat detail laporan ${report.category} - ${report.description}`">
    <!-- Card Image (Clickable for Evidence) -->
    <div class="report-card__image" @click.stop="report.image && $emit('view-evidence', report.image)">
      <img v-if="report.evidence" :src="report.evidence" :alt="`Gambar laporan ${report.category}`" class="card-image" />
      <div v-else class="card-image-placeholder">
        <i :class="getCategoryIcon(report.category)"></i>
      </div>
    </div>

    <!-- User Profile -->
    <div class="user-profile">
      <div class="category-badge" :class="getCategoryClass(report.category)">
        {{ report.category }}
      </div>
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
        {{ report.title || 'Laporan ' + report.category }}
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
}

.report-card__image {
  height: 250px; /* Fixed height for image */
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
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
  padding: 16px;
  display: flex;
  flex-direction: column;
  position: relative;
  border-bottom: 1px solid #f8f9fa;
  flex-shrink: 0;
}

.category-badge {
  position: absolute;
  top: 12px;
  left: 16px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  color: white;
  text-transform: capitalize;
  z-index: 1;
}

.category--phone {
  background: #007bff;
}

.category--fraud {
  background: #dc3545;
}

.category--default {
  background: #6c757d;
}

.user-details {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 40px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
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
}

.user-name {
  font-weight: 600;
  color: #212529;
  font-size: 0.9rem;
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
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.report-card__footer {
  padding: 12px;
  text-align: center;
  flex-shrink: 0;
}

.detail-button {
  background: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  font-size: 0.9rem;
}

.detail-button:hover {
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
  }
}
</style>