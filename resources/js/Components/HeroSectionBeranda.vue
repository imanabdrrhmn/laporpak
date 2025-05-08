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
            class="btn btn-light px-4 py-2 d-flex align-items-center shadow-button">
            Mulai Laporkan
            <i class="bi bi-arrow-right ms-2"></i>
          </button>
          
          <!-- Avatar Group with Count and Text -->
          <div class="d-flex align-items-center mt-4 flex-wrap">
            <div class="d-flex align-items-center">
              <div class="user-avatars d-flex align-items-center">
                <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User" class="avatar">
                <img src="https://randomuser.me/api/portraits/women/42.jpg" alt="User" class="avatar">
                <img src="https://randomuser.me/api/portraits/men/43.jpg" alt="User" class="avatar">
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
            <img 
              src="/hero-illustration.svg" 
              alt="Illustration" 
              class="img-fluid" 
              v-if="!lottieLoaded"
            >
            <lottie-player
              id="lazyLottie"
              src="https://assets3.lottiefiles.com/packages/lf20_fcfjwiyb.json"
              background="transparent"
              speed="1"
              loop
              style="width: 100%; height: auto;"
              v-if="lottieLoaded"
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
    }
  },
  mounted() {
    const script = document.createElement("script");
    script.src = "https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js";
    script.onload = () => {
      this.lottieLoaded = true;
    };
    document.head.appendChild(script);
  },
  methods: {
    handleReportButtonClick() {
      // Check if user is logged in
      // Assuming you have some global user state or auth check function
      // This might vary depending on how authentication is implemented in your app
      if (this.$page.props.auth && this.$page.props.auth.user) {
        // User is logged in, redirect to report page
        router.visit('/pelaporan');
      } else {
        // User is not logged in, show login modal
        this.$emit('show-login-modal');     
      }
    },
    setupLottie() {
      this.lottieLoaded = true
      this.$nextTick(() => {
        const lottiePlayer = document.getElementById('lazyLottie')
        if (lottiePlayer) {
          lottiePlayer.style.opacity = '0.4'
        }
      })
    },
    setupIntersectionObserver() {
      if (typeof IntersectionObserver !== 'undefined' && this.$refs.lottieContainer) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              this.lottieLoaded = true
              this.$nextTick(() => {
                const lottiePlayer = document.getElementById('lazyLottie')
                if (lottiePlayer) {
                  lottiePlayer.play()
                  lottiePlayer.style.opacity = '1'
                }
              })
              observer.unobserve(entry.target)
            }
          })
        }, { threshold: 0.5 })
        observer.observe(this.$refs.lottieContainer)
      } else {
        // Fallback for browsers without IntersectionObserver
        this.lottieLoaded = true
      }
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
}

.shadow-button:hover {
  transform: translateY(-2px);
  box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
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
}

/* Floating animation */
.animate-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0px); }
}

/* Responsive Styles */
@media (max-width: 1200px) {
  .hero-section {
    padding: 70px 0;
  }
  
  .illustration-container {
    max-width: 450px;
  }
}

@media (max-width: 992px) {
  .hero-section {
    padding: 60px 0;
    min-height: auto;
  }
  
  .illustration-container {
    margin: 40px auto 0;
    max-width: 400px;
  }
  
  .title-underline {
    width: 60%;
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
  }
}

@media (max-width: 400px) {
  .hero-section {
    padding: 30px 0;
  }
  
  .title-underline {
    width: 80%;
  }
}
</style>