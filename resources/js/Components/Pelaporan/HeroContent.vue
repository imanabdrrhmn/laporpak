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
  width: 80vw; /* Base width using viewport units for responsiveness */
  max-width: 450px; /* Maximum width to prevent it from getting too wide */
  min-width: 150px; /* Minimum width for very small screens */
  margin-left: auto;
  margin-right: auto;
}

/* Ensure highlight-bar is responsive across all devices */
@media (max-width: 320px) {
  .highlight-bar {
    width: 90vw; /* Slightly wider on very small screens */
    min-width: 120px;
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .highlight-bar {
    width: 85vw;
    min-width: 140px;
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .highlight-bar {
    width: 80vw;
    min-width: 160px;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .highlight-bar {
    width: 75vw;
    max-width: 350px;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .highlight-bar {
    width: 70vw;
    max-width: 400px;
  }
}

@media (min-width: 992px) {
  .highlight-bar {
    width: 60vw;
    max-width: 450px;
  }
  .hero-content {
    padding: 4rem !important;
    text-align: left;
  }
}

@media (min-width: 1200px) {
  .highlight-bar {
    width: 50vw;
    max-width: 450px;
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
    padding: 2rem 3rem !important;
    margin-bottom: 1rem;
  }
}

@media (max-width: 320px) {
  .responsive-title {
    font-size: 1.5rem !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: 1.3rem;
  }
  .hero-content {
    padding: 1rem !important;
  }
  .display-4 {
    font-size: 1.8rem !important;
  }
  .lead {
    font-size: 0.9rem;
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .responsive-title {
    font-size: 1.7rem !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: 1.5rem;
  }
  .hero-content {
    padding: 2rem 1.5rem !important;
  }
  .display-4 {
    font-size: 2.2rem !important;
  }
  .lead {
    font-size: 1rem;
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .responsive-title {
    font-size: 1.9rem !important;
  }
  .hero-content {
    padding: 2rem 1.5rem !important;
  }
  .display-4 {
    font-size: 2.2rem !important;
  }
  .lead {
    font-size: 1rem;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .hero-content {
    padding: 2.5rem 2rem !important;
  }
  .display-4 {
    font-size: 2.5rem !important;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .hero-content {
    padding: 3rem 2.5rem !important;
  }
  .display-4 {
    font-size: 3rem !important;
  }
}

@media (min-width: 1200px) {
  .display-4 {
    font-size: 2.5rem !important;
  }
}
</style>