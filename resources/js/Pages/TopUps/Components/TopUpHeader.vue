<template>
    <div class="top-up-header-bar">
      <div class="container d-flex justify-content-between align-items-center py-3">
        <h2 class="text-white fw-bold mb-0 h5">Top Up Credit</h2>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    currentBalance: {
      type: Number,
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
  .top-up-header-bar {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  @media (max-width: 767.98px) {
    .top-up-header-bar .container {
      flex-direction: column;
      align-items: flex-start !important;
    }
  }
  </style>