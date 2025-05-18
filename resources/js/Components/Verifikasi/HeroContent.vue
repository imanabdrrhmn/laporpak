<template>
  <div class="col-lg-6 d-flex flex-column justify-content-center text-white hero-content p-4 p-sm-5">
    <h1 class="display-4 fw-bold mb-3">
      <i class="bi bi-check-circle me-2"></i> Verifikasi Data
    </h1>
    <div class="highlight-bar mb-4"></div>
    <p class="lead mb-4">
      Akses layanan verifikasi data premium untuk memastikan keabsahan dan menjaga integritas informasi bisnis Anda. Solusi cepat dan akurat untuk kebutuhan verifikasi.
    </p>
    <!-- Features for desktop -->
    <div class="d-none d-lg-block">
      <div class="row mt-4 g-3">
        <div class="col-md-4">
          <div class="feature-box p-3">
            <i class="bi bi-shield-check fs-2 mb-2"></i>
            <h5>Keamanan</h5>
            <p class="small">Data terenkripsi dan terlindungi dengan standar tinggi</p>
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
    <!-- Features for mobile/tablet -->
    <div class="d-block d-lg-none mt-4">
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
  </div>
</template>

<script setup>
// No script logic needed, as this is a presentational component
</script>

<style scoped>
.hero-content {
  position: relative;
  z-index: 1;
}

.highlight-bar {
  height: 4px;
  width: 100px;
  background-color: #ffc107;
  border-radius: 2px;
  margin-top: 0.5rem;
}

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

.mobile-feature {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  backdrop-filter: blur(5px);
  padding: 0.75rem !important;
  min-height: 60px;
}

.display-4 i {
  font-size: 0.8em;
  vertical-align: middle;
}

.small {
  font-size: 0.875rem;
}

@media (max-width: 576px) {
  .hero-content {
    padding: 2rem 1rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0.5rem auto;
  }
  
  .display-4 {
    font-size: 2rem;
  }
  
  .lead {
    font-size: 1rem;
  }
}

@media (max-width: 360px) {
  .lead,
  .display-4 {
    word-break: break-word;
    hyphens: auto;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .hero-content {
    padding: 2.5rem 1.5rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0.5rem auto;
  }
  
  .display-4 {
    font-size: 2.5rem;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .hero-content {
    padding: 3rem 2rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0.5rem auto;
  }
}

@media (min-width: 992px) {
  .hero-content {
    padding-left: 4rem !important;
  }
}
</style>