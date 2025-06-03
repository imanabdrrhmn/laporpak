<!-- File: src/components/PaymentMethodSelection.vue -->
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
        <label class="form-check-label d-flex justify-content-between align-items-center w-100 p-3" :for="method.id">
          <div class="d-flex align-items-center">
            <img :src="method.icon" :alt="method.name" style="height: 24px; width: auto;" class="me-3 d-none d-sm-inline">
            <div>
              <span class="fw-bold">{{ method.name }}</span>
              <div class="small text-muted">{{ method.description }}</div>
            </div>
          </div>
          <input
            class="form-check-input"
            type="radio"
            :id="method.id"
            :value="method.id"
            :checked="selectedPaymentMethodId === method.id"
            @change="$emit('update:payment-method', method.id)"
            style="float: right; margin-left: auto; margin-right: 0.5rem; width: 1.2em; height: 1.2em;"
          />
        </label>
      </div>
      <div v-if="form.errors.payment_method" class="text-danger small mt-2">
        {{ form.errors.payment_method }}
      </div>
      <div class="d-flex justify-content-between mt-5">
        <button class="btn btn-outline-secondary px-4" @click="$emit('back')">
          <i class="bi bi-arrow-left me-1"></i> Back
        </button>
        <button
          class="btn btn-primary px-4"
          @click="$emit('proceed')"
          :disabled="!selectedPaymentMethodId"
        >
          Continue <i class="bi bi-arrow-right ms-1"></i>
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
  
  .payment-method-option:hover {
    border-color: #4e73df !important;
    box-shadow: 0 4px 15px rgba(78,115,223,0.2);
  }
  
  .payment-method-option.selected {
    border-color: #4e73df !important;
    box-shadow: 0 4px 15px rgba(78,115,223,0.3);
    background-color: #f8f9fa;
  }
  
  .form-check-input[type="radio"] {
    border-color: #adb5bd;
  }
  
  .form-check-input[type="radio"]:checked {
    background-color: #4e73df;
    border-color: #4e73df;
  }
  
  .form-check-input.is-invalid ~ .form-check-label {
    border-color: #dc3545;
  }
  
  .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
  }
  
  .btn-primary:hover {
    background: linear-gradient(135deg, #224abe 0%, #1e3a8a 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(78,115,223,0.4);
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
  </style>