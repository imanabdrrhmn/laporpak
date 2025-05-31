<template>
  <section class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <!-- Text Content -->
        <div class="col-lg-6 col-md-12 text-white mb-4 mb-lg-0">
          <h1 class="display-6 fw-bold mb-2">Membangun Indonesia</h1>
          <h1 class="display-6 fw-bold mb-4">Bebas Penipuan</h1>
          <div class="title-underline mb-4"></div>
          
          <!-- Button with login check logic -->
          <button 
            @click="handleReportButtonClick" 
            class="btn btn-light px-4 py-2 d-flex align-items-center shadow-button"
            :disabled="isLoading">
            <span v-if="!isLoading">Mulai Laporkan</span>
            <span v-else>Loading...</span>
            <i class="bi bi-arrow-right ms-2" v-if="!isLoading"></i>
          </button>
          
          <!-- Avatar Group with Count and Text -->
          <div class="d-flex align-items-center mt-4 flex-wrap">
            <div class="d-flex align-items-center">
              <div class="user-avatars d-flex align-items-center">
                <img 
                  src="https://randomuser.me/api/portraits/men/41.jpg" 
                  alt="User" 
                  class="avatar"
                  loading="lazy"
                  @error="handleImageError"
                >
                <img 
                  src="https://randomuser.me/api/portraits/women/42.jpg" 
                  alt="User" 
                  class="avatar"
                  loading="lazy"
                  @error="handleImageError"
                >
                <img 
                  src="https://randomuser.me/api/portraits/men/43.jpg" 
                  alt="User" 
                  class="avatar"
                  loading="lazy"
                  @error="handleImageError"
                >
                <div class="avatar-count d-flex align-items-center justify-content-center">
                  <span>15+</span>
                </div>
              </div>
              <div class="ms-3 text-white">
                <p class="mb-0 fw-semibold users-text">Bergabung bersama Ribuan</p>
                <p class="mb-0 fw-semibold users-text">Pelapor di Indonesia</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Illustration -->
        <div class="col-lg-6 col-md-12">
          <div class="illustration-container animate-float" ref="lottieContainer">
            <!-- Invisible Loading State - User won't see this -->
            <div 
              class="loading-placeholder" 
              v-if="!lottieLoaded && !lottieError"
            ></div>
            
            <!-- Error State -->
            <div v-if="lottieError" class="error-state">
              <div class="error-icon">⚠️</div>
              <p>Animation could not be loaded</p>
            </div>
            
            <!-- Lottie Animation -->
            <lottie-player
              id="lazyLottie"
              :src="lottieUrl"
              background="transparent"
              speed="1"
              loop
              style="width: 100%; height: auto; opacity: 0;"
              v-if="lottieLoaded && !lottieError"
              @ready="onLottieReady"
              @error="onLottieError"
            ></lottie-player>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { Link, router } from '@inertiajs/vue3';
import LoginModal from './modals/LoginModal.vue';

export default {
  name: 'HeroSection',
  components: {
    Link,
    LoginModal
  },
  data() {
    return {
      lottieLoaded: false,
      lottieError: false,
      isLoading: false,
      lottieUrl: 'https://assets3.lottiefiles.com/packages/lf20_fcfjwiyb.json',
      intersectionObserver: null,
      loadTimeout: null
    }
  },
  mounted() {
    this.setupIntersectionObserver();
  },
  beforeUnmount() {
    // Cleanup
    if (this.intersectionObserver) {
      this.intersectionObserver.disconnect();
    }
    if (this.loadTimeout) {
      clearTimeout(this.loadTimeout);
    }
  },
  methods: {
    handleReportButtonClick() {
      this.isLoading = true;
      
      try {
        // Check if user is logged in
        if (this.$page.props.auth && this.$page.props.auth.user) {
          // User is logged in, redirect to report page
          router.visit('/pelaporan');
        } else {
          // User is not logged in, show login modal
          this.$emit('show-login-modal');     
        }
      } catch (error) {
        console.error('Error handling report button click:', error);
      } finally {
        this.isLoading = false;
      }
    },

    handleImageError(event) {
      // Replace failed image with a placeholder
      event.target.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23ddd'/%3E%3Ctext x='50%25' y='50%25' font-family='Arial' font-size='12' fill='%23999' text-anchor='middle' dy='.3em'%3E?%3C/text%3E%3C/svg%3E";
    },

    loadLottieScript() {
      return new Promise((resolve, reject) => {
        // Check if already loaded
        if (window.LottiePlayer) {
          resolve();
          return;
        }

        const script = document.createElement("script");
        script.src = "https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js";
        
        script.onload = () => {
          resolve();
        };
        
        script.onerror = () => {
          reject(new Error('Failed to load Lottie player script'));
        };
        
        document.head.appendChild(script);
        
        // Set timeout for script loading
        this.loadTimeout = setTimeout(() => {
          reject(new Error('Lottie script loading timeout'));
        }, 10000); // 10 second timeout
      });
    },

    async setupLottie() {
      try {
        await this.loadLottieScript();
        this.lottieLoaded = true;
        
        // Clear timeout if successful
        if (this.loadTimeout) {
          clearTimeout(this.loadTimeout);
          this.loadTimeout = null;
        }
      } catch (error) {
        console.warn('Failed to load Lottie:', error);
        this.lottieError = true;
        this.lottieLoaded = false;
      }
    },

    setupIntersectionObserver() {
      if (typeof IntersectionObserver !== 'undefined' && this.$refs.lottieContainer) {
        this.intersectionObserver = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting && !this.lottieLoaded && !this.lottieError) {
              this.setupLottie();
              this.intersectionObserver.unobserve(entry.target);
            }
          });
        }, { 
          threshold: 0.3,
          rootMargin: '50px' // Load slightly before visible
        });
        
        this.intersectionObserver.observe(this.$refs.lottieContainer);
      } else {
        // Fallback for browsers without IntersectionObserver
        this.setupLottie();
      }
    },

    onLottieReady() {
      this.$nextTick(() => {
        const lottiePlayer = document.getElementById('lazyLottie');
        if (lottiePlayer) {
          // Smooth fade in
          lottiePlayer.style.transition = 'opacity 0.5s ease-in-out';
          lottiePlayer.style.opacity = '1';
          lottiePlayer.play();
        }
      });
    },

    onLottieError() {
      console.warn('Lottie animation failed to load');
      this.lottieError = true;
      this.lottieLoaded = false;
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  padding: 80px 0;
  min-height: 80vh;
  display: flex;
  align-items: center;
  will-change: transform; /* Optimize for animations */
}

.title-underline {
  height: 4px;
  width: 70%;
  max-width: 400px;
  background-color: #FFC107;
  border-radius: 2px;
}

.shadow-button {
  font-weight: bold;
  color: #377CF4;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  border: none;
  background-color: white;
  will-change: transform; /* Optimize for hover animations */
}

.shadow-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
  color: #377CF4;
  background-color: white;
}

.shadow-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Avatar Styles */
.user-avatars {
  display: flex;
  align-items: center;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid white;
  object-fit: cover;
  transform: translateX(-10px);
  margin-left: 0;
  z-index: 1;
  transition: transform 0.2s ease;
}

.avatar:hover {
  transform: translateX(-10px) scale(1.05);
}

.avatar:first-child {
  margin-left: 0;
  z-index: 3;
}
.avatar:nth-child(2) {
  z-index: 2;
}
.avatar:nth-child(3) {
  z-index: 1;
}

.avatar-count {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  color: #2563EB;
  font-weight: bold;
  font-size: 14px;
  transform: translateX(-10px);
  margin-left: 0;
  z-index: 0;
}

/* Illustration */
.illustration-container {
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
  min-height: 300px; /* Prevent layout shift */
}

.loading-placeholder {
  width: 100%;
  height: 300px;
  opacity: 0;
  pointer-events: none;
  /* Completely invisible to user */
}

.fallback-image,
.error-state {
  width: 100%;
  height: auto;
  border-radius: 10px;
}

.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: white;
  text-align: center;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

/* Floating animation - optimized */
.animate-float {
  animation: float 6s ease-in-out infinite;
  will-change: transform;
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0px);
  }
  50% { 
    transform: translateY(-10px);
  }
}

/* Reduce animations on mobile for better performance */
@media (prefers-reduced-motion: reduce) {
  .animate-float {
    animation: none;
  }
  
  .shadow-button {
    transition: none;
  }
  
  .avatar {
    transition: none;
  }
}

/* Responsive Styles */
@media (max-width: 1200px) {
  .hero-section {
    padding: 70px 0;
  }
  
  .loading-placeholder {
    height: 220px;
  }
  
  .loading-placeholder {
    height: 200px;
  }
  
  .loading-placeholder {
    height: 180px;
  }
  
  .loading-placeholder {
    height: 150px;
  }
  
  .illustration-container {
    max-width: 450px;
  }
}

/* iPad Air and tablet landscape (1024px - 1180px) */
@media (min-width: 993px) and (max-width: 1180px) {
  .hero-section {
    text-align: center;
    padding: 70px 0;
  }
  
  .title-underline {
    margin-left: auto;
    margin-right: auto;
    width: 60%;
  }
  
  .shadow-button {
    margin-left: auto;
    margin-right: auto;
  }
  
  .d-flex.align-items-center.mt-4 {
    justify-content: center;
  }
  
  .illustration-container {
    margin: 40px auto 0;
    max-width: 400px;
    min-height: 250px;
  }
}

@media (max-width: 992px) {
  .hero-section {
    padding: 60px 0;
    min-height: auto;
    text-align: center;
  }
  
  .illustration-container {
    margin: 40px auto 0;
    max-width: 400px;
    min-height: 250px;
  }
  
  .title-underline {
    width: 60%;
    margin-left: auto;
    margin-right: auto;
  }
  
  .shadow-button {
    margin-left: auto;
    margin-right: auto;
  }
  
  .d-flex.align-items-center.mt-4 {
    justify-content: center;
  }
}

/* iPad portrait and smaller tablets (768px - 992px) */
@media (min-width: 768px) and (max-width: 992px) {
  .hero-section {
    text-align: center;
    padding: 65px 0;
  }
  
  .title-underline {
    margin-left: auto;
    margin-right: auto;
    width: 50%;
  }
  
  .shadow-button {
    margin-left: auto;
    margin-right: auto;
  }
  
  .d-flex.align-items-center.mt-4 {
    justify-content: center;
    margin-top: 2rem;
  }
  
  .illustration-container {
    margin-top: 35px;
    max-width: 380px;
    min-height: 220px;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 50px 0;
    text-align: center;
  }
  
  .title-underline {
    margin-left: auto;
    margin-right: auto;
  }
  
  .shadow-button {
    margin-left: auto;
    margin-right: auto;
  }
  
  .d-flex.align-items-center.mt-4 {
    justify-content: center;
    margin-top: 2rem;
  }
  
  .illustration-container {
    margin-top: 30px;
    max-width: 350px;
    min-height: 200px;
  }
}

@media (max-width: 576px) {
  .hero-section {
    padding: 40px 0;
  }
  
  h1.display-6 {
    font-size: calc(1.275rem + 0.3vw);
  }
  
  .avatar {
    width: 35px;
    height: 35px;
  }
  
  .avatar-count {
    width: 35px;
    height: 35px;
    font-size: 12px;
  }
  
  .users-text {
    font-size: 0.9rem;
  }
  
  .illustration-container {
    max-width: 280px;
    min-height: 180px;
  }
}

@media (max-width: 400px) {
  .hero-section {
    padding: 30px 0;
  }
  
  .title-underline {
    width: 80%;
  }
  
  .illustration-container {
    min-height: 150px;
  }
}

/* Performance optimizations */
@media (max-width: 768px) {
  /* Reduce gradient complexity on mobile */
  .hero-section {
    background: linear-gradient(135deg, #0062cc, #0078e7);
  }
}
</style>