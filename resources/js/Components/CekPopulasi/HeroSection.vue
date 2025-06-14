<template>
  <section class="hero-section">
    <div class="container">
      <div class="row align-items-center min-vh-50">
        <div class="col-lg-8 mx-auto text-center">
          <h1 class="display-4 fw-bold text-white mb-4 animate-fade-in">
            Peta Estimasi Populasi Indonesia
          </h1>
          <p class="lead text-white-75 mb-4 animate-fade-in-delay">
            Temukan estimasi jumlah penduduk di sekitar lokasi tertentu dengan teknologi GIS dan data GHSL. 
            Klik pada peta untuk melihat populasi dalam radius yang dapat disesuaikan.
          </p>
          <div class="hero-stats row text-center mt-5">
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-number">100m</div>
                <div class="stat-label">Resolusi Grid</div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-number">Real-time</div>
                <div class="stat-label">Analisis Data</div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-number">GHSL</div>
                <div class="stat-label">Dataset Global</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="hero-overlay"></div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection'
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  position: relative;
  overflow: hidden;
  padding: 4rem 0;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
}

.text-white-75 {
  color: rgba(255, 255, 255, 0.85) !important;
}

.min-vh-50 {
  min-height: 50vh;
}

.animate-fade-in {
  animation: fadeInUp 0.8s ease-out;
}

.animate-fade-in-delay {
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

.stat-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 15px;
  padding: 1.5rem;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  background: rgba(255, 255, 255, 0.15);
}

.stat-number {
  font-size: 1.5rem;
  font-weight: bold;
  color: white;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.8);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 3rem 0;
  }
  
  .display-4 {
    font-size: 2rem;
  }
  
  .stat-card {
    padding: 1rem;
  }
}
</style>