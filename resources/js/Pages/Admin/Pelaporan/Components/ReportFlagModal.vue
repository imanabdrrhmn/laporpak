<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content" @click.stop>
      <button class="close-btn" @click="close" aria-label="Tutup modal">×</button>
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
  background: rgba(10, 25, 47, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 4000;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 2rem;
  animation: fadeInBackdrop 0.35s ease forwards;
}

@keyframes fadeInBackdrop {
  from {opacity: 0;}
  to {opacity: 1;}
}

.modal-content {
  background: linear-gradient(145deg, #1e2a47, #16203a);
  border-radius: 20px;
  width: 100%;
  max-width: 520px;
  max-height: 75vh;
  overflow-y: auto;
  box-shadow: 0 25px 40px rgba(0, 0, 0, 0.4);
  position: relative;
  font-family: 'Inter', system-ui, sans-serif;
  color: #f0f4ff;
  padding: 2.5rem 3rem;
  transform: translateY(-30px);
  opacity: 0;
  animation: slideInModal 0.4s ease forwards;
}

@keyframes slideInModal {
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.close-btn {
  position: absolute;
  top: 18px;
  right: 18px;
  font-size: 2rem;
  background: transparent;
  border: none;
  color: #a1a9cc;
  cursor: pointer;
  transition: color 0.3s ease;
  line-height: 1;
  filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.3));
}

.close-btn:hover {
  color: #ff6b6b;
  transform: rotate(90deg);
}

h3 {
  margin-top: 0;
  margin-bottom: 2rem;
  font-weight: 800;
  font-size: 2.25rem;
  letter-spacing: 0.05em;
  text-align: center;
  color: #f9fafb;
  text-shadow: 0 0 8px #0b2545;
}

.no-flags-text {
  font-style: italic;
  color: #a1a9cc;
  margin-bottom: 2rem;
  font-size: 1.125rem;
  text-align: center;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
}

.category-count-item {
  padding: 18px 24px;
  margin-bottom: 1rem;
  border-radius: 14px;
  background: linear-gradient(135deg, #25408f, #192a5f);
  font-size: 1.125rem;
  font-weight: 700;
  color: #e0e6ff;
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.15),
    0 6px 10px rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background-color 0.3s ease;
  cursor: default;
}

.category-count-item:hover {
  background: linear-gradient(135deg, #2f54ff, #1c39bb);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.3),
    0 8px 14px rgba(0, 0, 0, 0.35);
  color: #fff;
}

.category-count-item:last-child {
  margin-bottom: 0;
}

.total-flags {
  font-weight: 700;
  color: #ced6ff;
  border-top: 2px solid #3b4cca;
  padding-top: 1.25rem;
  margin-top: 1.75rem;
  text-align: right;
  font-size: 1.25rem;
  text-shadow: 0 0 5px #556ee6;
}

</style>
