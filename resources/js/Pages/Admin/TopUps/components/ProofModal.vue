<template>
    <div
      class="modal fade"
      id="proofModal"
      tabindex="-1"
      aria-labelledby="proofModalLabel"
      aria-hidden="true"
      ref="modalRef"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="proofModalLabel">Bukti Pembayaran</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="emit('close-proof-modal')"
            ></button>
          </div>
          <div class="modal-body p-0">
            <img :src="proofModalUrl" alt="Bukti Pembayaran" class="img-fluid w-100" />
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from "vue";
  
  defineProps({
    proofModalUrl: String,
  });
  
  const emit = defineEmits(["close-proof-modal"]);
  </script>