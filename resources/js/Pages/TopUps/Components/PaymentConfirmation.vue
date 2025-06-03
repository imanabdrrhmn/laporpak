<!-- File: src/components/PaymentConfirmation.vue -->
<template>
    <div>
      <h3 class="fw-bold mb-4 text-center">Pembayaran {{ selectedPaymentMethodDetails.name }}</h3>
      <div v-if="selectedPaymentMethodDetails.id === 'BCA'" class="mb-4">
        <p class="fw-bold">Intruksi Pembayaran</p>
        <ol class="mb-4 ps-4">
          <li>Buka aplikasi perbankan atau Internet Banking Anda</li>
          <li>Pilih menu "Transfer" ke rekening bank</li>
          <li>Masukkan nomor rekening tujuan dan nama bank yang sesuai</li>
          <li>Konfirmasikan jumlah transfer dan selesaikan transaksi</li>
        </ol>
        <div class="text-center bg-light p-3 rounded">
          <p class="mb-1 text-muted small">Nomir Rekening {{ selectedPaymentMethodDetails.name }}</p>
          <div class="d-flex flex-wrap justify-content-center align-items-center">
            <span class="fs-4 fw-bold me-3 mb-2 mb-sm-0">{{ bcaVirtualAccount }}</span>
            <button class="btn btn-sm btn-outline-primary" @click="$emit('copy-to-clipboard', bcaVirtualAccount)">
              <i class="bi bi-clipboard me-1"></i> copy
            </button>
          </div>
        </div>
      </div>
      <div class="border rounded p-3 mb-4">
        <h5 class="fw-bold mb-3">Ringkasan Pembayaran</h5>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Nama Penerima :</span>
          <span class="fw-bold">{{ Name }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Jumlah :</span>
          <span class="fw-bold">Rp {{ formatCurrency(finalSelectedAmount) }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Metode Pembayaran :</span>
          <span class="fw-bold">{{ selectedPaymentMethodDetails.name }} {{ selectedPaymentMethodDetails.type || '' }}</span>
        </div>
      </div>
      <div class="mb-4">
        <label for="proof" class="form-label fw-bold">Upload Bukti Pembayaran</label>
        <input
          type="file"
          id="proof"
          class="form-control"
          :class="{'is-invalid': form.errors.proof}"
          accept="image/jpeg,image/png,image/jpg"
          @change="$emit('upload-proof', $event)"
        >
        <div v-if="form.errors.proof" class="invalid-feedback">
          {{ form.errors.proof }}
        </div>
        <div v-if="proofPreview" class="mt-3 text-center">
          <img :src="proofPreview" class="img-fluid rounded shadow-sm" style="max-height: 200px; max-width: 100%;" />
        </div>
      </div>
      <div class="d-flex justify-content-between mt-5">
        <button class="btn btn-outline-secondary px-4" @click="$emit('back')" :disabled="form.processing">
          <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button class="btn btn-success px-4" @click="$emit('confirm-payment')" :disabled="form.processing || !form.proof">
          <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Sudah melakukan pembayaran
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    selectedPaymentMethodDetails: {
      type: Object,
      required: true,
    },
    bcaVirtualAccount: {
      type: String,
      required: true,
    },
    Name: {
      type: String,
      required: true,
    },
    finalSelectedAmount: {
      type: Number,
      required: true,
    },
    proofPreview: {
      type: String,
      default: null,
    },
    form: {
      type: Object,
      required: true,
    },
  });
  
  const formatCurrency = (value) => {
    if (isNaN(parseFloat(value))) return '';
    return new Intl.NumberFormat('id-ID', {
      style: 'decimal',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  };
  </script>
  
  <style scoped>
  .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
  }
  
  .btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
  }
  
  .btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    transform: translateY(-1px);
  }
  
  .btn-success {
    background: #28a745;
    border: none;
  }
  
  .btn-success:hover {
    background: #218838;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
  }
  
  .form-control {
    border-color: #e9ecef;
    border-radius: 8px;
  }
  
  .form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
  }
  
  .form-control.is-invalid {
    border-color: #dc3545;
  }
  
  .invalid-feedback {
    width: 100%;
    margin-top: 0.25rem;
    font-size: .875em;
    color: #dc3545;
  }
  </style>