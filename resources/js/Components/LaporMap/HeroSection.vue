<template>
  <div class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 hero-content">
          <h1 class="display-4 fw-bold mb-3 text-start hero-title">
            Peta Interaktif Laporan Penipuan
          </h1>
          <div class="line-separator"></div>
          <p class="lead mt-4 text-start hero-description">
            Temukan informasi terkini tentang lokasi penipuan yang dilaporkan oleh masyarakat.
            Bantu kami melindungi komunitas dengan berbagi informasi.
          </p>
          <div class="stats mt-5 hero-stats">
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
        <div class="col-lg-6 position-relative hero-map">
          <div class="map-container">
            <img 
              src="/map.svg" 
              alt="Peta Interaktif" 
              class="img-fluid map-image"
              loading="lazy"
              decoding="async"
            />
            <div 
              v-for="(pin, index) in mapPins" 
              :key="index"
              class="map-pin"
              :class="[`pin-${index + 1}`, { pulse: animatePins }]"
            >
              <i class="bi bi-geo-alt-fill"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  reportCount: {
    type: Number,
    default: 0
  },
  locationCount: {
    type: Number,
    default: 0
  },
  userCount: {
    type: Number,
    default: 0
  },
  animatePins: {
    type: Boolean,
    default: false
  }
})

// Computed untuk optimasi re-render
const mapPins = computed(() => [
  { id: 1, top: '30%', left: '35%' },
  { id: 2, top: '45%', left: '65%' },
  { id: 3, top: '65%', left: '25%' }
])
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

/* CSS Variables untuk konsistensi dan kemudahan maintenance */
.hero-section {
  --hero-primary: #0062cc;
  --hero-secondary: #0078e7;
  --hero-dark: #003f8a;
  --hero-accent: #ffb74d;
  --hero-accent-dark: #f57c00;
  --hero-danger: #f44336;
  --hero-success: #4caf50;
  --shadow-light: rgba(0, 0, 0, 0.1);
  --shadow-medium: rgba(0, 0, 0, 0.15);
  --shadow-dark: rgba(0, 0, 0, 0.3);
  --white-overlay: rgba(255, 255, 255, 0.1);
  --white-overlay-hover: rgba(255, 255, 255, 0.2);
  --border-radius: 8px;
  --border-radius-large: 15px;
  --transition: all 0.3s ease;
  --animation-duration: 0.8s;
}

/* Optimized pseudo-element dengan will-change */
.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
  opacity: 0.3;
  will-change: transform;
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
  border-radius: var(--border-radius-large);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  will-change: transform;
}

.map-image {
  border-radius: var(--border-radius-large);
  transition: var(--transition);
  width: 100%;
  height: auto;
  will-change: transform;
}

.map-container:hover .map-image {
  transform: scale(1.03);
}

.map-pin {
  position: absolute;
  color: #f44336;
  font-size: 20px;
  filter: drop-shadow(0 2px 5px var(--shadow-dark));
  will-change: transform;
}

.map-pin.pulse {
  animation: pulse 1s infinite;
}

.pin-1 { top: 30%; left: 35%; }
.pin-2 { top: 45%; left: 65%; }
.pin-3 { top: 65%; left: 25%; }

.stat-item {
  text-align: center;
  background: var(--white-overlay);
  border-radius: var(--border-radius);
  padding: 10px;
  margin-bottom: 15px;
  transition: var(--transition);
  will-change: transform;
}

.stat-item:hover {
  background: var(--white-overlay-hover);
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

/* Optimized animations dengan transform3d untuk hardware acceleration */
.hero-title {
  animation: fadeInLeft var(--animation-duration) ease forwards;
}

.line-separator {
  animation: fadeInLeft var(--animation-duration) 0.1s ease forwards;
  opacity: 0;
}

.hero-description {
  animation: fadeInLeft var(--animation-duration) 0.2s ease forwards;
  opacity: 0;
}

.hero-stats {
  animation: fadeIn var(--animation-duration) 0.4s ease forwards;
  opacity: 0;
}

.hero-map {
  animation: fadeInRight var(--animation-duration) ease forwards;
}

@keyframes fadeInLeft {
  from { 
    opacity: 0; 
    transform: translate3d(-30px, 0, 0); 
  }
  to { 
    opacity: 1; 
    transform: translate3d(0, 0, 0); 
  }
}

@keyframes fadeInRight {
  from { 
    opacity: 0; 
    transform: translate3d(30px, 0, 0); 
  }
  to { 
    opacity: 1; 
    transform: translate3d(0, 0, 0); 
  }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes pulse {
  0%, 100% { 
    transform: scale(1); 
  }
  50% { 
    transform: scale(1.1); 
  }
}

/* Container queries untuk responsive yang lebih efisien */
@container (max-width: 1200px) {
  .hero-section {
    padding: 50px 0;
    min-height: 50vh;
  }
  .stat-number {
    font-size: 1.8rem;
  }
}

/* Media queries yang dioptimalkan */
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

/* Reduced motion untuk accessibility */
@media (prefers-reduced-motion: reduce) {
  .hero-title,
  .line-separator,
  .hero-description,
  .hero-stats,
  .hero-map {
    animation: none;
    opacity: 1;
  }
  
  .map-pin.pulse {
    animation: none;
  }
  
  .stat-item:hover,
  .map-container:hover .map-image {
    transform: none;
  }
}

/* Print styles */
@media print {
  .hero-section {
    background: white !important;
    color: black !important;
    padding: 20px 0;
  }
  
  .hero-section::before {
    display: none;
  }
  
  .map-pin.pulse {
    animation: none;
  }
}
</style>