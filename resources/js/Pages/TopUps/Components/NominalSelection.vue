<!-- File: src/components/NominalSelection.vue -->
<template>
    <div>
      <h3 class="fw-bold mb-4">Pilih Nominal</h3>
      <div class="row g-3 mb-4">
        <div v-for="option in nominalOptions" :key="option.idr" class="col-6 col-sm-4">
          <button
            type="button"
            class="btn btn-outline-primary w-100 p-3 nominal-option"
            :class="{ 'active': selectedNominalPackage && selectedNominalPackage.idr === option.idr }"
            @click="$emit('select-nominal', option)"
          >
            <div class="fw-bold fs-6">Rp {{ formatCurrency(option.idr) }}</div>
          </button>
        </div>
      </div>
      <p class="text-muted mb-2 small">atau masukan jumlah kelipatan Rp 100.000 (min. Rp 100.000)</p>
      <div class="input-group mb-3">
        <span class="input-group-text">Rp</span>
        <input
          type="tel"
          inputmode="numeric"
          pattern="[0-9]*"
          class="form-control"
          :class="{'is-invalid': (form.errors.amount && customAmountRaw > 0) || customAmountErrorInternal}"
          placeholder="Contoh: 200000"
          :value="customAmountInput"
          @input="handleNumericInput"
          @keypress="validateNumericKeypress"
          @blur="$emit('blur:custom-amount')"
        >
      </div>
      <div v-if="(form.errors.amount && customAmountRaw > 0) || customAmountErrorInternal" class="invalid-feedback d-block mb-3">
        {{ form.errors.amount || customAmountErrorInternal }}
      </div>
      <div class="d-flex justify-content-between mt-4">
        <button class="btn btn-outline-secondary px-4" @click="$emit('back')">
          <i class="bi bi-arrow-left me-1"></i> Kembali
        </button>
        <button
          class="btn btn-primary px-4"
          @click="$emit('proceed')"
          :disabled="!isNominalValid"
        >
          Lanjutkan<i class="bi bi-arrow-right ms-1"></i>
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, computed } from 'vue';
  
  const props = defineProps({
    nominalOptions: {
      type: Array,
      required: true,
    },
    selectedNominalPackage: {
      type: Object,
      default: null,
    },
    customAmountInput: {
      type: String,
      default: '',
    },
    customAmountErrorInternal: {
      type: String,
      default: '',
    },
    form: {
      type: Object,
      required: true,
    },
  });
  
  const emit = defineEmits(['update:custom-amount', 'blur:custom-amount', 'select-nominal', 'back', 'proceed']);
  
  const customAmountRaw = computed(() => {
    const value = props.customAmountInput.replace(/[^0-9]/g, '');
    return parseInt(value) || 0;
  });
  
  const isNominalValid = computed(() => {
    if (props.selectedNominalPackage) return true;
    if (customAmountRaw.value > 0) {
      return customAmountRaw.value >= 100000 && customAmountRaw.value % 100000 === 0;
    }
    return false;
  });
  
  const formatCurrency = (value) => {
    if (isNaN(parseFloat(value))) return '';
    return new Intl.NumberFormat('id-ID', {
      style: 'decimal',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  };
  
  const handleNumericInput = (event) => {
    // Hanya izinkan angka
    const value = event.target.value.replace(/[^0-9]/g, '');
    event.target.value = value;
    emit('update:custom-amount', event);
  };
  
  const validateNumericKeypress = (event) => {
    // Izinkan: backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].indexOf(event.keyCode) !== -1 ||
        // Izinkan: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (event.keyCode === 65 && event.ctrlKey === true) ||
        (event.keyCode === 67 && event.ctrlKey === true) ||
        (event.keyCode === 86 && event.ctrlKey === true) ||
        (event.keyCode === 88 && event.ctrlKey === true)) {
      return;
    }
    // Pastikan hanya angka (0-9)
    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
      event.preventDefault();
    }
  };
  </script>
  
  <style scoped>
  .nominal-option {
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    background: white;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  
  .nominal-option:hover {
    background-color:  #0b5ed7;;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #4e73df;
  }
  
  .nominal-option.active {
    background: #0d6efd;
    color: white;
    border-color: #4e73df;
    box-shadow: 0 8px 25px rgba(78,115,223,0.3);
  }
  
  .nominal-option:not(.active) {
    color: #495057;
  }
  
  .nominal-option:not(.active):hover {
    background-color: #f8f9fa;
  }
  
  .input-group-text {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    color: #495057;
    font-weight: 600;
  }
  
  .form-control {
    border-color: #e9ecef;
    border-radius: 8px;
  }
  
  .form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
  }
  
  .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
  }
  
  .btn-primary {
    background: #0d6efd;
    border: none;
  }
  
  .btn-primary:hover {
    background:  #0b5ed7;
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
  
  .form-control.is-invalid {
    border-color: #dc3545;
  }
  
  .invalid-feedback {
    width: 100%;
    margin-top: 0.25rem;
    font-size: .875em;
    color: #dc3545;
  }
  
  .invalid-feedback.d-block {
    display: block !important;
  }
  
  /* Media queries untuk mobile */
  @media (max-width: 767.98px) {
    .nominal-option {
      min-height: 70px;
      padding: 12px 8px !important; /* Memberikan padding yang cukup */
    }
    
    .nominal-option .fw-bold {
      font-size: 0.9rem !important;
      line-height: 1.3;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }
    
    /* Pastikan button navigation memiliki jarak yang cukup */
    .btn {
      padding: 12px 16px !important;
      min-height: 44px; /* Minimum touch target untuk mobile */
    }
    
    .px-4 {
      padding-left: 16px !important;
      padding-right: 16px !important;
    }
  }
  
  @media (max-width: 575.98px) {
    .nominal-option {
      min-height: 75px; /* Sedikit lebih tinggi untuk teks yang panjang */
      padding: 8px 4px !important; /* Padding yang proporsional untuk layar kecil */
    }
    
    .nominal-option .fs-6 {
      font-size: 0.8rem !important;
      line-height: 1.2;
      word-break: break-word;
      hyphens: auto;
    }
    
    .nominal-option .fw-bold {
      white-space: normal !important; /* Izinkan wrap untuk layar kecil */
      overflow: visible !important;
      text-overflow: unset !important;
    }
    
    /* Pastikan button navigation tetap memiliki ukuran yang baik */
    .btn {
      padding: 10px 14px !important;
      font-size: 0.9rem;
      min-height: 42px;
    }
    
    .px-4 {
      padding-left: 14px !important;
      padding-right: 14px !important;
    }
    
    /* Tambahkan margin untuk button container di mobile */
    .d-flex.justify-content-between {
      gap: 12px;
    }
  }
  
  /* Extra small devices */
  @media (max-width: 400px) {
    .btn {
      padding: 8px 12px !important;
      font-size: 0.85rem;
    }
    
    .px-4 {
      padding-left: 12px !important;
      padding-right: 12px !important;
    }
  }
  </style>