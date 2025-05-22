<template>
  <div class="modal-backdrop">
    <div class="modal-content">
      <button class="close-btn" @click="$emit('close')">×</button>
      <h3><slot name="title" /></h3>
      <div class="modal-body">
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  padding: 1rem;
  border-radius: 10px;
  width: 400px;
}
.close-btn {
  float: right;
  background: none;
  border: none;
  font-size: 1.5rem;
}
</style>
