<template>
    <div>
      <h3 class="fw-bold mb-4">Metode Pembayaran</h3>
      <div
        v-for="method in availablePaymentMethods"
        :key="method.id"
        class="form-check p-0 border rounded mb-3 payment-method-option"
        :class="{'selected': selectedPaymentMethodId === method.id, 'is-invalid': form.errors.payment_method && selectedPaymentMethodId === method.id}"
        @click="$emit('update:payment-method', method.id)"
      >
        <label class="form-check-label d-flex justify-content-between align-items-center w-100 p-2 p-sm-3" :for="method.id">
          <div class="d-flex align-items-center flex-wrap flex-sm-nowrap">
            <img :src="method.icon" :alt="method.name" class="payment-icon me-2 me-sm-3">
            <div class="payment-info">
              <span class="fw-bold d-block">{{ method.name }}</span>
              <div class="small text-muted">{{ method.description }}</div>
            </div>
          </div>
          <input
            class="form-check-input ms-2"
            type="radio"
            :id="method.id"
            :value="method.id"
            :checked="selectedPaymentMethodId === method.id"
            @change="$emit('update:payment-method', method.id)"
          />
        </label>
      </div>
      <div v-if="form.errors.payment_method" class="text-danger small mt-2">
        {{ form.errors.payment_method }}
      </div>
      <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mt-5">
        <button class="btn btn-outline-secondary w-100 w-sm-auto" @click="$emit('back')">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </button>
        <button
          class="btn btn-primary w-100 w-sm-auto"
          @click="$emit('proceed')"
          :disabled="!selectedPaymentMethodId"
        >
          Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    availablePaymentMethods: {
      type: Array,
      required: true,
    },
    selectedPaymentMethodId: {
      type: String,
      default: null,
    },
    form: {
      type: Object,
      required: true,
    },
  });
  </script>
  
  <style scoped>
  .payment-method-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 10px;
  }
  
  .payment-icon {
    height: 24px;
    width: auto;
    min-width: 24px;
  }
  
  .payment-info {
    flex: 1;
    min-width: 200px;
    margin: 0.5rem 0;
  }
  
  @media (max-width: 576px) {
    .payment-info {
      min-width: 150px;
      font-size: 0.9rem;
    }
    
    .payment-icon {
      height: 20px;
    }

    .form-check-input {
      width: 1em !important;
      height: 1em !important;
    }

    .btn {
      padding: 12px 20px;
      font-size: 1rem;
    }
  }

  .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 24px;
    transition: all 0.3s ease;
    min-width: 120px;
  }
  
  .btn-primary {
    background:  #0d6efd;
    border: none;
  }
  
  .btn-primary:hover {
    background: #0b5ed7;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(78,115,223,0.4);
  }
  
  .btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
  }
  
  .btn-outline-secondary:hover {
    color: white;
    background-color: #6c757d;
    border-color: #6c757d;
    transform: translateY(-1px);
  }
  </style>