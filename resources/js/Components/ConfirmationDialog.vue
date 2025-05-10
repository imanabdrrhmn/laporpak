<template>
  <div v-if="visible" class="backdrop" @click.self="cancel">
    <div class="dialog">
      <h3 class="title">{{ title }}</h3>
      <p class="message">{{ message }}</p>
      <div class="actions">
        <button class="btn cancel" @click="cancel">Batal</button>
        <button class="btn confirm" @click="confirm">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visible: Boolean,
  title: String,
  message: String,
})
const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => emit('confirm')
const cancel = () => emit('cancel')
</script>

<style scoped>
.backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1100;
}
.dialog {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}
.title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
}
.message {
  font-size: 15px;
  margin-bottom: 20px;
}
.actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.cancel {
  background-color: #ccc;
  color: #333;
}
.confirm {
  background-color: #007bff;
  color: white;
}
</style>
