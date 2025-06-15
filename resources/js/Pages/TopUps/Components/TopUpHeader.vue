<template>
    <div class="top-up-header-bar">
      <div class="container d-flex justify-content-center align-items-center py-3">
        <h2 class="text-white fw-bold mb-0 h3">Top Up Credit</h2>
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
    background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  @media (max-width: 767.98px) {
    .top-up-header-bar .container {
      flex-direction: column;
      align-items: center !important;
    }
    
    .top-up-header-bar h2 {
      font-size: 2rem;
    }
  }
  </style>