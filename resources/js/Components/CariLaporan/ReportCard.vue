<template>
  <div
    class="report-card"
    :class="[
      getCategoryClass(report.category),
      { 'report-card--featured': report.priority === 'high' },
      { 'report-card--resolved': report.status === 'resolved' },
      { 'report-card--compact': compact }
    ]"
    @click="$emit('open-detail', report)"
    @keyup.enter="$emit('open-detail', report)"
    role="button"
    tabindex="0"
    :aria-label="`Lihat detail laporan ${report.category} - ${report.description}`"
  >
    <!-- Card Header -->
    <div class="report-card__header">
      <div class="report-card__type">
        <div class="report-card__icon-wrapper" :class="getCategoryClass(report.category)">
          <i :class="getCategoryIcon(report.category)" aria-hidden="true"></i>
        </div>
        <div class="report-card__type-info">
          <span class="report-card__category">{{ report.category }}</span>
          <span v-if="report.subcategory && !compact" class="report-card__subcategory">{{ report.subcategory }}</span>
        </div>
      </div>
      <div v-if="report.urgency" class="report-card__urgency" :class="getUrgencyClass(report.urgency)">
        {{ report.urgency }}
      </div>
    </div>

    <!-- Card Body -->
    <div class="report-card__body">
      <!-- Source/Location -->
      <h5
        v-if="report.category !== 'Infrastruktur' && report.source && !compact"
        class="report-card__title"
        :title="report.source"
      >
        <i class="fas fa-map-marker-alt me-2 text-muted"></i>
        <span class="text-truncate">{{ report.source }}</span>
      </h5>

      <!-- Description -->
      <p class="report-card__description">
        {{ truncateText(report.description, compact ? 80 : 120) }}
      </p>

      <!-- Evidence -->
      <div v-if="report.evidence && !compact" class="report-card__evidence">
        <a
          :href="report.evidence"
          target="_blank"
          rel="noopener noreferrer"
          class="report-card__evidence-link"
          :aria-label="`Lihat bukti untuk laporan ${report.category}`"
          @click.stop
        >
          <i class="fas fa-paperclip me-2"></i>
          Lihat Bukti
        </a>
      </div>

      <!-- Tags -->
      <div v-if="report.tags && report.tags.length && !compact" class="report-card__tags">
        <span
          v-for="tag in report.tags.slice(0, compact ? 2 : 3)"
          :key="tag"
          class="report-card__tag"
          :style="{ backgroundColor: getTagColor(tag) }"
        >
          {{ tag }}
        </span>
        <span v-if="report.tags.length > (compact ? 2 : 3)" class="report-card__tag-more">
          +{{ report.tags.length - (compact ? 2 : 3) }} more
        </span>
      </div>

      <!-- Progress Bar -->
      <div v-if="report.progress !== undefined" class="report-card__progress">
        <div class="progress-bar-wrapper">
          <div class="progress-bar" :style="{ width: `${report.progress}%` }"></div>
        </div>
        <small class="progress-text">{{ report.progress }}% selesai</small>
      </div>
    </div>

    <!-- Card Footer -->
    <div class="report-card__footer">
      <div class="report-card__meta">
        <small class="report-card__date">
          <i class="far fa-calendar-alt me-1" aria-hidden="true"></i>
          {{ formatDate(report.created_at) }}
        </small>
        <small v-if="report.reporter && !compact" class="report-card__reporter">
          <i class="far fa-user me-1" aria-hidden="true"></i>
          {{ report.reporter }}
        </small>
      </div>
      <div v-if="report.views && !compact" class="report-card__stats">
        <small class="report-card__views">
          <i class="far fa-eye me-1" aria-hidden="true"></i>
          {{ report.views }}
        </small>
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
    compact: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['open-detail'],
  methods: {
    getCategoryClass(category) {
      const CATEGORY_CLASSES = {
        'Nomor Hp': 'report-card--phone',
        Email: 'report-card--email',
        Infrastruktur: 'report-card--infra',
        Keamanan: 'report-card--security',
        Lingkungan: 'report-card--environment',
        Layanan: 'report-card--service',
      };
      return CATEGORY_CLASSES[category] || 'report-card--default';
    },
    getCategoryIcon(category) {
      const CATEGORY_ICONS = {
        'Nomor Hp': 'fas fa-phone',
        Email: 'fas fa-envelope',
        Infrastruktur: 'fas fa-network-wired',
        Keamanan: 'fas fa-shield-alt',
        Lingkungan: 'fas fa-leaf',
        Layanan: 'fas fa-cogs',
      };
      return CATEGORY_ICONS[category] || 'fas fa-question-circle';
    },
    getUrgencyClass(urgency) {
      const URGENCY_CLASSES = {
        low: 'urgency--low',
        medium: 'urgency--medium',
        high: 'urgency--high',
        critical: 'urgency--critical',
      };
      return URGENCY_CLASSES[urgency] || '';
    },
    getTagColor(tag) {
      // Simple hash function to generate consistent colors
      let hash = 0;
      for (let i = 0; i < tag.length; i++) {
        hash = tag.charCodeAt(i) + ((hash << 5) - hash);
      }
      const hue = hash % 360;
      return `hsl(${hue}, 70%, 90%)`;
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length <= length ? text : text.substring(0, length) + '...';
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffHours <= 24) {
        return `Hari ini, ${date.toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit',
        })}`;
      }
      if (diffDays === 1) return 'Kemarin';
      if (diffDays < 7) return `${diffDays} hari lalu`;

      return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      });
    },
  },
};
</script>

<style scoped>
.report-card {
  position: relative;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
  border: 1px solid #e9ecef !important;
  border-radius: 16px;
  padding: 0;
  margin-bottom: 24px;
  width: 100%;
  height: 300px; /* Slightly taller for better content fit */
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
}

.report-card--compact {
  height: 180px; /* Compact height */
}

.report-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
}

.report-card:focus {
  outline: 3px solid #007bff;
  outline-offset: 2px;
}

.report-card--featured {
  background: linear-gradient(135deg, #fff8e1 0%, #ffffff 100%) !important;
  border-color: #ffb300 !important;
}

.report-card--resolved {
  background: linear-gradient(135deg, #e6ffed 0%, #ffffff 100%) !important;
  border-color: #00c853 !important;
}

/* Header */
.report-card__header {
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #f5f5f5;
}

/* Icon Wrapper */
.report-card__icon-wrapper {
  width: 36px;
  height: 36px;
  background: #f5f5f5;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: #37474f;
}

/* Body */
.report-card__body {
  flex: 1;
  padding: 0 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.report-card__title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #263238;
  margin: 0;
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.report-card__description {
  margin: 0;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
  height: 4.5em;
  color: #546e7a !important;
}

.report-card--compact .report-card__description {
  -webkit-line-clamp: 2;
  line-clamp: 2;
  height: 3em;
}

/* Tags */
.report-card__tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.report-card__tag {
  color: #37474f;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
}

.report-card__tag-more {
  background-color: #eceff1;
  color: #546e7a;
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 0.8rem;
}

/* Evidence */
.report-card__evidence-link {
  display: inline-flex;
  align-items: center;
  color: #0288d1;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.2s ease;
}

.report-card__evidence-link:hover {
  color: #01579b;
  text-decoration: underline;
}

/* Progress */
.report-card__progress {
  margin-bottom: 12px;
}

.progress-bar-wrapper {
  width: 100%;
  height: 8px;
  background-color: #eceff1;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 6px;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #0288d1, #4fc3f7);
  transition: width 0.4s ease;
}

.progress-text {
  color: #546e7a;
  font-size: 0.8rem;
}

/* Footer */
.report-card__footer {
  padding: 12px 16px;
  border-top: 1px solid #f5f5f5;
  background: #ffffff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.report-card--compact .report-card__footer {
  padding: 8px 16px;
}

.report-card__meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.report-card__date,
.report-card__reporter,
.report-card__views {
  font-size: 0.85rem;
  color: #546e7a !important;
  display: flex;
  align-items: center;
}

/* Responsive */
@media (max-width: 768px) {
  .report-card {
    height: 260px;
  }

  .report-card--compact {
    height: 160px;
  }

  .report-card__header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .report-card__footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .report-card__meta {
    flex-direction: row;
    gap: 20px;
  }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .report-card,
  .report-card__overlay,
  .progress-bar {
    transition: none;
  }

  .report-card:hover {
    transform: none;
  }
}
</style>