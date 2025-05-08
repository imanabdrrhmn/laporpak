<template>
  <div v-if="isVisible" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <button class="close-btn" @click="closeModal">X</button>
      
      <div v-if="report && report.id">
        <h2>{{ report.service }} Report</h2>
        <p><strong>Category:</strong> {{ report.category }}</p>
        <p><strong>Status:</strong> {{ report.status }}</p>
        <p><strong>Description:</strong> {{ report.description }}</p>
        <p><strong>Evidence:</strong> <a :href="report.evidence" target="_blank">View Evidence</a></p>
        <p><strong>Location:</strong> Latitude: {{ report.latitude }}, Longitude: {{ report.longitude }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  report: Object,
  isVisible: Boolean
})

const emit = defineEmits(['close'])

const closeModal = () => {
  emit('close')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 80%;
  max-width: 600px;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: red;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.close-btn:hover {
  background-color: darkred;
}
</style>
