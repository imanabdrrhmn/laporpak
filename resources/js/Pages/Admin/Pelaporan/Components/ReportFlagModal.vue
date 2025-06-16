<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content" @click.stop>
      <button class="btn-close custom-close" @click="close" aria-label="Tutup"></button>
      <h3 id="modalTitle">Ringkasan Pelaporan</h3>

      <p v-if="flags.length === 0" class="no-flags-text">Tidak ada laporan.</p>

      <ul v-else>
        <li v-for="(count, category) in categoryCounts" :key="category" class="category-count-item">
          <strong>{{ category || 'Kategori Tidak Diketahui' }}:</strong> {{ count }} laporan
        </li>
      </ul>

      <p class="total-flags">Total laporan: {{ flags.length }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ReportFlagSummaryModal",
  props: {
    isOpen: Boolean,
    flags: {
      type: Array,
      default: () => [],
    },
  },
  emits: ["close"],
  computed: {
    categoryCounts() {
      return this.flags.reduce((acc, flag) => {
        const category = flag.reason || 'Kategori Tidak Diketahui';
        if (!acc[category]) acc[category] = 0;
        acc[category]++;
        return acc;
      }, {});
    },
  },
  methods: {
    close() {
      this.$emit("close");
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 4000;
  padding: clamp(1rem, 4vw, 2rem);
  animation: fadeInBackdrop 0.3s ease forwards;
}

@keyframes fadeInBackdrop {
  from {opacity: 0;}
  to {opacity: 1;}
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 75vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  position: relative;
  font-family: 'Inter', system-ui, sans-serif;
  color: #333;
  padding: clamp(1.25rem, 3vw, 2rem);
  width: calc(100% - 2rem);
  margin: 1rem;
  transform: translateY(-20px);
  opacity: 0;
  animation: slideInModal 0.3s ease forwards;
}

@keyframes slideInModal {
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.btn-close {
  position: absolute;
  top: 20px;
  right: 20px;
  border-radius: 50%;
  padding: 6px;
  background-color: #f3f4f6;
  opacity: 1;
  transition: all 0.2s ease;
  z-index: 10;
}

.btn-close:hover {
  background-color: #e5e7eb;
  transform: rotate(90deg);
}

h3 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  font-weight: 600;
  font-size: clamp(1.25rem, 4vw, 1.5rem);
  text-align: center;
  color: #0d6efd;
}

.no-flags-text {
  font-style: italic;
  color: #666;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  text-align: center;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0 0 1.5rem 0;
}

.category-count-item {
  padding: clamp(8px, 2vw, 12px) clamp(12px, 3vw, 16px);
  margin-bottom: 8px;
  border-radius: 8px;
  background: white;
  border: 1px solid #e9ecef;
  font-size: clamp(0.875rem, 2.5vw, 0.95rem);
  color: #333;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.2s ease;
  flex-wrap: wrap;
  gap: 4px;
}

.category-count-item:hover {
  border-color: #0d6efd;
  background: #f8f9ff;
}

.category-count-item:last-child {
  margin-bottom: 0;
}

.category-count-item strong {
  color: #0d6efd;
}

.total-flags {
  font-weight: 600;
  color: #0d6efd;
  border-top: 1px solid #e9ecef;
  padding-top: 1rem;
  margin-top: 1rem;
  text-align: right;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .custom-close {
    top: 12px;
    right: 12px;
  }
}

@media (max-width: 480px) {
  .custom-close {
    top: 10px;
    right: 10px;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .modal-content {
    max-width: 90%;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .modal-content {
    max-width: 80%;
  }
}
</style>