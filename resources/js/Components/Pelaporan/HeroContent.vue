<template>
  <div class="col-lg-6 d-flex flex-column justify-content text-white hero-content p-4 p-sm-5 ms-auto">
    <div class="text-section">
      <h1 class="display-4 fw-bold mb-3 d-flex align-items-center responsive-title">
        <i :class="serviceInfo[selectedService].titleIcon + ' me-2'"></i>
        <span>{{ serviceInfo[selectedService].title }}</span>
      </h1>
      <div class="highlight-bar mb-4"></div>
      <p class="lead mb-4">{{ serviceInfo[selectedService].description }}</p>
      <FeatureCards />
    </div>
  </div>
</template>

<script setup>
import FeatureCards from './FeatureCards.vue';

defineProps({
  selectedService: String,
  serviceInfo: Object
});
</script>

<style scoped>
.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
}

.text-section {
  color: #fff;
  position: relative;
  z-index: 1;
  margin-top: 0;
  padding: 1.5rem;
}

.highlight-bar {
  height: 4px;
  background-color: #ffc107;
  border-radius: 2px;
  width: clamp(120px, 60vw, 400px); /* Responsive width with clamp */
  margin-left: auto;
  margin-right: auto;
}

/* Ensure FeatureCards content is contained */
.feature-cards-container {
  overflow: hidden; /* Prevent content overflow */
  word-break: break-word; /* Break long words */
  hyphens: auto;
  box-sizing: border-box; /* Ensure padding/margins are included in width */
}

/* Responsive media queries */
@media (max-width: 320px) {
  .highlight-bar {
    width: clamp(100px, 80vw, 300px);
  }
  .responsive-title {
    font-size: clamp(1.4rem, 4vw, 1.6rem) !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: clamp(1.2rem, 3.5vw, 1.4rem);
  }
  .hero-content {
    padding: 1rem !important;
  }
  .display-4 {
    font-size: clamp(1.6rem, 5vw, 1.8rem) !important;
  }
  .lead {
    font-size: clamp(0.8rem, 2.5vw, 0.9rem);
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .highlight-bar {
    width: clamp(120px, 75vw, 320px);
  }
  .responsive-title {
    font-size: clamp(1.6rem, 4.5vw, 1.8rem) !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: clamp(1.3rem, 4vw, 1.5rem);
  }
  .hero-content {
    padding: 1.5rem !important;
  }
  .display-4 {
    font-size: clamp(1.8rem, 5.5vw, 2.2rem) !important;
  }
  .lead {
    font-size: clamp(0.9rem, 2.5vw, 1rem);
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .highlight-bar {
    width: clamp(140px, 70vw, 340px);
  }
  .responsive-title {
    font-size: clamp(1.8rem, 4.5vw, 2rem) !important;
  }
  .hero-content {
    padding: 1.5rem !important;
  }
  .display-4 {
    font-size: clamp(2rem, 5.5vw, 2.4rem) !important;
  }
  .lead {
    font-size: clamp(0.9rem, 2.5vw, 1rem);
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .highlight-bar {
    width: clamp(160px, 65vw, 360px);
  }
  .hero-content {
    padding: 2rem !important;
  }
  .display-4 {
    font-size: clamp(2.2rem, 5.5vw, 2.6rem) !important;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .highlight-bar {
    width: clamp(180px, 60vw, 380px);
  }
  .hero-content {
    padding: 2.5rem !important;
  }
  .display-4 {
    font-size: clamp(2.6rem, 5vw, 3rem) !important;
  }
}

@media (min-width: 992px) {
  .highlight-bar {
    width: clamp(200px, 55vw, 400px);
  }
  .hero-content {
    padding: 3rem !important;
    text-align: left;
  }
  .display-4 {
    font-size: clamp(2.4rem, 4.5vw, 2.8rem) !important;
  }
}

@media (min-width: 1200px) {
  .highlight-bar {
    width: clamp(220px, 50vw, 420px);
  }
  .display-4 {
    font-size: clamp(2.3rem, 4vw, 2.7rem) !important;
  }
}

.responsive-title {
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
  line-height: 1.2;
}

.responsive-title span {
  display: inline;
}

@media (min-width: 600px) and (max-width: 1199px) {
  .text-section {
    padding: 2rem 2.5rem !important;
    margin-bottom: 1rem;
  }
}

/* Zoom-specific adjustments */
@media (min--moz-device-pixel-ratio: 1.5),
(-webkit-min-device-pixel-ratio: 1.5),
(min-resolution: 1.5dppx) {
  .highlight-bar {
    width: clamp(110px, 50vw, 360px); /* Adjusted for 150% zoom */
  }
  .responsive-title {
    font-size: clamp(1.8rem, 4vw, 2.2rem) !important;
  }
  .display-4 {
    font-size: clamp(2rem, 4.5vw, 2.5rem) !important;
  }
  .lead {
    font-size: clamp(0.9rem, 2.2vw, 1.1rem);
  }
  .feature-cards-container {
    font-size: clamp(0.9rem, 2vw, 1rem); /* Ensure card content scales */
    padding: clamp(0.8rem, 2vw, 1rem);
  }
}

/* Ensure FeatureCards content is responsive */
@media (max-width: 767.98px) {
  .feature-cards-container {
    font-size: clamp(0.8rem, 2.5vw, 0.9rem);
    padding: clamp(0.5rem, 2vw, 0.8rem);
  }
}

@media (min-width: 768px) {
  .feature-cards-container {
    font-size: clamp(0.9rem, 2vw, 1rem);
    padding: clamp(0.8rem, 2vw, 1.2rem);
  }
}
</style>