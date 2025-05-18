<template>
  <div class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 hero-content">
          <h1 class="display-4 fw-bold mb-3 text-start animate__animated animate__fadeInLeft">
            Peta Interaktif Laporan Penipuan
          </h1>
          <div class="line-separator animate__animated animate__fadeInLeft animate__delay-1s"></div>
          <p class="lead mt-4 text-start animate__animated animate__fadeInLeft animate__delay-2s">
            Temukan informasi terkini tentang lokasi penipuan yang dilaporkan oleh masyarakat.
            Bantu kami melindungi komunitas dengan berbagi informasi.
          </p>
          <div class="stats mt-5 animate__animated animate__fadeIn animate__delay-4s">
            <div class="row">
              <div class="col-md-4">
                <div class="stat-item">
                  <h3 class="stat-number">{{ reportCount }}</h3>
                  <p class="stat-label">Laporan</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-item">
                  <h3 class="stat-number">{{ locationCount }}</h3>
                  <p class="stat-label">Lokasi</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-item">
                  <h3 class="stat-number">{{ userCount }}</h3>
                  <p class="stat-label">Pengguna</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 position-relative animate__animated animate__fadeInRight">
          <div class="map-container">
            <img src="/map.svg" alt="Peta Interaktif" class="img-fluid" />
            <div class="map-pin pin-1" :class="{ pulse: animatePins }">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="map-pin pin-2" :class="{ pulse: animatePins }">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="map-pin pin-3" :class="{ pulse: animatePins }">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="floating-card">
              <div class="card-body">
                <h5><i class="bi bi-shield-fill-check me-2"></i> Kawasan Aman</h5>
                <p class="mb-0">Bandung, Jawa Barat</p>
                <small class="text-success">Pelaporan menurun 23%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  reportCount: Number,
  locationCount: Number,
  userCount: Number,
  animatePins: Boolean
});
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  color: white;
  padding: 60px 0;
  min-height: 60vh;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
  opacity: 0.3;
}

.line-separator {
  height: 5px;
  width: 60px;
  background: linear-gradient(to right, #ffb74d, #f57c00);
  margin-bottom: 15px;
  border-radius: 3px;
}

.hero-content {
  padding-right: 15px;
  text-align: center;
}

.map-container {
  position: relative;
  border-radius: 15px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.map-container img {
  border-radius: 15px;
  transition: all 0.5s ease;
  width: 100%;
  height: auto;
}

.map-container:hover img {
  transform: scale(1.03);
}

.map-pin {
  position: absolute;
  color: #f44336;
  font-size: 20px;
  filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
}

.map-pin.pulse {
  animation: pulse 1s infinite;
}

.pin-1 { top: 30%; left: 35%; }
.pin-2 { top: 45%; left: 65%; }
.pin-3 { top: 65%; left: 25%; }

.floating-card {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 8px;
  padding: 10px;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  color: #333;
  max-width: 200px;
  transform: translateY(0);
  transition: transform 0.3s ease;
}

.map-container:hover .floating-card {
  transform: translateY(-8px);
}

.stat-item {
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 10px;
  margin-bottom: 15px;
  transition: all 0.3s ease;
}

.stat-item:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-5px);
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 0;
}

.stat-label {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 1px;
  margin-bottom: 0;
}

.animate__animated { animation-duration: 0.8s; animation-fill-mode: both; }
.animate__delay-1s { animation-delay: 0.1s; }
.animate__delay-2s { animation-delay: 0.2s; }
.animate__delay-4s { animation-delay: 0.4s; }

@keyframes fadeInLeft {
  from { opacity: 0; transform: translate3d(-30px, 0, 0); }
  to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate__fadeInLeft { animation-name: fadeInLeft; }

@keyframes fadeInRight {
  from { opacity: 0; transform: translate3d(30px, 0, 0); }
  to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate__fadeInRight { animation-name: fadeInRight; }

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate__fadeIn { animation-name: fadeIn; }

@media (max-width: 1200px) {
  .hero-section {
    padding: 50px 0;
    min-height: 50vh;
  }
  .stat-number {
    font-size: 1.8rem;
  }
}

@media (max-width: 992px) {
  .hero-content {
    padding-bottom: 30px;
  }
  .line-separator {
    margin: 0 auto 15px;
  }
  .stats {
    margin-bottom: 30px;
  }
  .map-container {
    margin-bottom: 20px;
  }
  .floating-card {
    max-width: 180px;
    padding: 8px;
    font-size: 0.9rem;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 40px 0;
    min-height: 40vh;
  }
  .hero-content h1 {
    font-size: 1.8rem;
  }
  .hero-content p.lead {
    font-size: 1rem;
  }
  .stat-item {
    padding: 8px;
  }
  .stat-number {
    font-size: 1.5rem;
  }
  .stat-label {
    font-size: 0.7rem;
  }
  .map-pin {
    font-size: 18px;
  }
  .floating-card {
    max-width: 150px;
    padding: 6px;
    font-size: 0.8rem;
    bottom: 10px;
    right: 10px;
  }
}

@media (max-width: 576px) {
  .hero-section {
    padding: 30px 0;
    min-height: 35vh;
  }
  .hero-content h1 {
    font-size: 1.5rem;
  }
  .hero-content p.lead {
    font-size: 0.9rem;
  }
  .line-separator {
    width: 50px;
  }
  .stat-item {
    margin-bottom: 10px;
  }
  .stat-number {
    font-size: 1.2rem;
  }
  .stat-label {
    font-size: 0.65rem;
  }
  .map-pin {
    font-size: 16px;
  }
}

@media (max-width: 320px) {
  .hero-content h1 {
    font-size: 1.3rem;
  }
  .hero-content p.lead {
    font-size: 0.8rem;
  }
  .floating-card {
    max-width: 120px;
    font-size: 0.7rem;
  }
}
</style>