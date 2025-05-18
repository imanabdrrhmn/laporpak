<template>
  <div class="d-none d-md-block">
    <div class="row mt-4 g-3">
      <div class="col-md-4">
        <div class="feature-box p-3">
          <i class="bi bi-shield-check fs-2 mb-2"></i>
          <h5>Keamanan</h5>
          <p class="small">Data terenkripsi dan terlindungi dengan baik</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box p-3">
          <i class="bi bi-lightning fs-2 mb-2"></i>
          <h5>Kecepatan</h5>
          <p class="small">Hasil verifikasi instan dalam hitungan detik</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box p-3">
          <i class="bi bi-graph-up fs-2 mb-2"></i>
          <h5>Akurasi</h5>
          <p class="small">Data terverifikasi dengan tingkat akurasi tinggi</p>
        </div>
      </div>
    </div>
  </div>
  <div class="d-block d-md-none">
    <div class="row g-2">
      <div class="col-4 text-center">
        <div class="mobile-feature p-2">
          <i class="bi bi-shield-check fs-4"></i>
          <div class="small mt-1">Keamanan</div>
        </div>
      </div>
      <div class="col-4 text-center">
        <div class="mobile-feature p-2">
          <i class="bi bi-lightning fs-4"></i>
          <div class="small mt-1">Kecepatan</div>
        </div>
      </div>
      <div class="col-4 text-center">
        <div class="mobile-feature p-2">
          <i class="bi bi-graph-up fs-4"></i>
          <div class="small mt-1">Akurasi</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.feature-box {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
  transition: transform 0.3s ease;
  text-align: left;
  padding: 1.5rem;
  min-height: 200px;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.feature-box:hover {
  transform: translateY(-5px);
}

.feature-box i {
  color: #ffffff;
}

.feature-box h5 {
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.feature-box p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.875rem;
  line-height: 1.4;
  margin-bottom: 0;
  flex-grow: 1;
}

.mobile-feature {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  backdrop-filter: blur(5px);
  padding: 0.75rem;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.mobile-feature i {
  color: #ffffff;
}

.mobile-feature .small {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.75rem;
  line-height: 1.2;
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .feature-box {
    min-height: 180px;
    padding: 1rem;
  }
  .feature-box h5 {
    font-size: 0.95rem;
  }
  .feature-box p {
    font-size: 0.8rem;
    line-height: 1.3;
  }
  .row.g-3 {
    margin-right: -0.5rem;
    margin-left: -0.5rem;
  }
  .col-md-4 {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
  }
}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .feature-box, .mobile-feature {
    backdrop-filter: blur(12px);
  }
}
</style>