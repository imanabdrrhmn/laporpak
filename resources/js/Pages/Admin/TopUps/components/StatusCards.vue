<template>
    <div class="status-cards mb-4">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card status-card shadow-sm border-warning">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1 text-muted">Pending</h6>
                  <h3 class="card-title mb-0">{{ pendingCount }}</h3>
                </div>
                <div class="status-icon bg-warning-light text-warning">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card status-card shadow-sm border-success">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1 text-muted">Verified</h6>
                  <h3 class="card-title mb-0">{{ verifiedCount }}</h3>
                </div>
                <div class="status-icon bg-success-light text-success">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card status-card shadow-sm border-danger">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="card-subtitle mb-1 text-muted">Rejected</h6>
                  <h3 class="card-title mb-0">{{ rejectedCount }}</h3>
                </div>
                <div class="status-icon bg-danger-light text-danger">
                  <i class="fas fa-times-circle"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from "vue";
  
  defineProps({
    pendingCount: Number,
    verifiedCount: Number,
    rejectedCount: Number,
  });
  </script>
  
  <style scoped>
  .status-cards .card {
    border-left-width: 4px;
    transition: transform 0.2s;
  }
  
  .status-cards .card:hover {
    transform: translateY(-3px);
  }
  
  .status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }
  
  .bg-warning-light {
    background-color: rgba(255, 193, 7, 0.15);
  }
  
  .bg-success-light {
    background-color: rgba(40, 167, 69, 0.15);
  }
  
  .bg-danger-light {
    background-color: rgba(220, 53, 69, 0.15);
  }
  </style>