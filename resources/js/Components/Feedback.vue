<template>
  <div class="container py-5">
    <h2 class="text-center mb-4 mb-md-5">Apa Yang Pengguna Sampaikan Setelah Menggunakan Layanan Kami.</h2>
    
    <div class="testimonial-carousel">
      <!-- Baris Pertama -->
      <div class="row mb-3 mb-md-4">
        <div class="col-12">
          <div class="carousel-container overflow-hidden">
            <div class="carousel-track d-flex" :style="{ transform: `translateX(-${scrollPosition1}px)` }">
              <!-- Clone awal kartu untuk infinite scroll -->
              <div v-for="testimonial in testimonials" :key="`clone-top-start-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
              
              <!-- Kartu asli -->
              <div v-for="testimonial in testimonials" :key="`top-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
              
              <!-- Clone akhir kartu untuk infinite scroll -->
              <div v-for="testimonial in testimonials" :key="`clone-top-end-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Baris Kedua -->
      <div class="row">
        <div class="col-12">
          <div class="carousel-container overflow-hidden">
            <div class="carousel-track d-flex" :style="{ transform: `translateX(-${scrollPosition2}px)` }">
              <!-- Clone awal kartu untuk baris kedua -->
              <div v-for="testimonial in shuffledTestimonials" :key="`clone-bottom-start-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
              
              <!-- Kartu asli baris kedua -->
              <div v-for="testimonial in shuffledTestimonials" :key="`bottom-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
              
              <!-- Clone akhir kartu untuk baris kedua -->
              <div v-for="testimonial in shuffledTestimonials" :key="`clone-bottom-end-${testimonial.id}`" 
                   class="testimonial-card mx-2 mx-md-3">
                <div class="card-body">
                  <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-category" :class="getCategoryClassMethod(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon" :style="{ color: star <= testimonial.rating ? '#ff9800' : '#ccc' }">
                        ★
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <hr class="divider">
                  
                  <p class="testimonial-text">{{ testimonial.content }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-4 mt-md-5">
      <Link href="/feedback">
        <button class="btn btn-primary px-4 py-2 mt-2">
          Lihat semua <i class="bi bi-arrow-right ms-1"></i>
        </button>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link
  },
  props: {
    feedbacks: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      scrollPosition1: 0,
      scrollPosition2: 0,
      scrollSpeed1: 0.5,
      scrollSpeed2: 0.3,
      animationFrameId: null,
      cardWidth: 0,
      cardMargin: 0,
      totalWidth: 0,
      isMobile: false
    };
  },
  computed: {
    testimonials() {
      if (!Array.isArray(this.feedbacks)) {
        return [];
      }
      return this.feedbacks.map(f => ({
        id: f.id,
        name: f.user?.name || 'Anonim',
        avatar: this.resolveAvatar(f.user?.avatar),
        rating: f.rating || 5,
        kategori: f.kategori || 'Umum',
        content: f.message || null,
      }));
    },
    shuffledTestimonials() {
      return [...this.testimonials].sort(() => 0.5 - Math.random());
    }
  },
  mounted() {
    this.checkMobile();
    this.$nextTick(() => {
      this.initCarousel();
    });

    window.addEventListener('resize', this.handleResize);
    window.addEventListener('visibilitychange', this.handleVisibilityChange);
    window.addEventListener('orientationchange', this.handleResize);
  },
  beforeDestroy() {
    this.stopScrolling();
    window.removeEventListener('resize', this.handleResize);
    window.removeEventListener('visibilitychange', this.handleVisibilityChange);
    window.removeEventListener('orientationchange', this.handleResize);
  },
  methods: {
    resolveAvatar(avatarPath) {
      if (!avatarPath) {
        return 'https://placehold.co/50x50?text=?';
      }
      if (avatarPath.startsWith('http://') || avatarPath.startsWith('https://')) {
        return avatarPath;
      }
      return `/storage/${avatarPath}`;
    },
    checkMobile() {
      this.isMobile = window.innerWidth < 768;
      this.scrollSpeed1 = this.isMobile ? 0.3 : 0.5;
      this.scrollSpeed2 = this.isMobile ? 0.2 : 0.3;
    },
    getCategoryClassMethod(kategori) {
      switch (kategori) {
        case 'Pelaporan':
          return 'category-pelaporan';
        case 'Verifikasi':
          return 'category-verifikasi';
        case 'Umum':
          return 'category-umum';
        default:
          return 'category-umum';
      }
    },
    initCarousel() {
      const card = document.querySelector('.testimonial-card');
      if (card) {
        const style = window.getComputedStyle(card);
        const marginLeft = parseInt(style.marginLeft.replace('px', ''));
        const marginRight = parseInt(style.marginRight.replace('px', ''));
        this.cardWidth = card.offsetWidth;
        this.cardMargin = marginLeft + marginRight;
        this.totalWidth = (this.cardWidth + this.cardMargin) * this.testimonials.length;
        this.scrollPosition1 = this.totalWidth;
        this.scrollPosition2 = this.totalWidth;
        this.startScrolling();
      }
    },
    startScrolling() {
      if (window.innerWidth < 320) return;

      const animate = () => {
        this.scrollPosition1 += this.scrollSpeed1;
        if (this.scrollPosition1 >= this.totalWidth * 2) this.scrollPosition1 = this.totalWidth;

        this.scrollPosition2 += this.scrollSpeed2;
        if (this.scrollPosition2 >= this.totalWidth * 2) this.scrollPosition2 = this.totalWidth;

        this.animationFrameId = requestAnimationFrame(animate);
      };

      this.animationFrameId = requestAnimationFrame(animate);
    },
    stopScrolling() {
      if (this.animationFrameId) cancelAnimationFrame(this.animationFrameId);
    },
    handleResize() {
      this.stopScrolling();
      this.checkMobile();
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = setTimeout(() => {
        this.$nextTick(() => {
          this.initCarousel();
        });
      }, 250);
    },
    handleVisibilityChange() {
      document.hidden ? this.stopScrolling() : this.startScrolling();
    }
  }
};
</script>

<style scoped>
.testimonial-carousel {
  overflow: hidden;
}

.carousel-container {
  position: relative;
}

.carousel-track {
  transition: transform 0.1s linear;
  will-change: transform;
}

.testimonial-card {
  flex: 0 0 auto;
  width: 280px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  padding: 15px;
  margin-bottom: 15px;
  position: relative;
}

.testimonial-card::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 10%;
  width: 80%;
  height: 10px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.02));
  filter: blur(4px);
  border-radius: 50%;
  z-index: -1;
}

.avatar-section {
  display: flex;
  align-items: center;
}

.avatar-container {
  margin-right: 12px;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  background-color: #f0f8ff;
  padding: 2px;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 16px;
  font-weight: 700;
  color: #1e1e1e;
  margin-bottom: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 120px;
}

.user-category {
  font-size: 12px;
  font-weight: 600;
  margin-top: 2px;
  margin-bottom: 0;
  padding: 2px 0;
  border-radius: 10px;
  display: inline-block;
  text-align: center;
  width: 80px;
}

.category-pelaporan {
  background-color: #D4EDFF;
  color: #004085;
}

.category-verifikasi {
  background-color: #FFF3CD;
  color: #856404;
}

.category-umum {
  background-color: #D1FAE5;
  color: #065F46;
}

.rating {
  display: flex;
}

.star-icon {
  color: #ccc;
  font-size: 18px;
  margin-left: 1px;
}

.divider {
  border-top: 1px solid #eaeaea;
  margin: 15px 0;
}

.testimonial-text {
  font-size: 14px;
  line-height: 1.4;
  color: #333;
  margin-bottom: 0;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btn-primary {
  background-color: #0d6efd;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
}

@media (min-width: 576px) {
  .testimonial-card {
    width: 320px;
    padding: 18px;
  }
  
  .user-name {
    font-size: 18px;
    max-width: 150px;
  }
  
  .user-category {
    font-size: 13px;
    width: 90px;
  }
  
  .avatar {
    width: 55px;
    height: 55px;
  }
  
  .testimonial-text {
    font-size: 15px;
    line-height: 1.45;
  }
  
  .star-icon {
    font-size: 20px;
  }
  
  .btn-primary {
    font-size: 15px;
  }
}

@media (min-width: 768px) {
  .testimonial-card {
    width: 350px;
    padding: 20px;
  }
  
  .user-name {
    font-size: 20px;
    max-width: 180px;
  }
  
  .user-category {
    font-size: 14px;
    width: 100px;
    padding: 3px 0;
  }
  
  .avatar {
    width: 60px;
    height: 60px;
    padding: 3px;
  }
  
  .testimonial-text {
    font-size: 16px;
    line-height: 1.5;
    -webkit-line-clamp: 5;
  }
  
  .star-icon {
    font-size: 22px;
    margin-left: 2px;
  }
  
  .btn-primary {
    font-size: 16px;
    font-weight: lighter;
  }
}

@media (min-width: 992px) {
  .testimonial-card {
    width: 380px;
  }
}

@media (min-width: 1200px) {
  .testimonial-card {
    width: 400px;
  }
}

@media (hover: hover) {
  .testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    transition: all 0.3s ease;
  }
}

@media (max-width: 320px) {
  .testimonial-card {
    width: 240px;
    padding: 12px;
  }
  
  .avatar {
    width: 40px;
    height: 40px;
  }
  
  .user-name {
    font-size: 14px;
    max-width: 100px;
  }
  
  .user-category {
    font-size: 11px;
    width: 70px;
  }
  
  .star-icon {
    font-size: 16px;
  }
  
  .testimonial-text {
    font-size: 12px;
    line-height: 1.3;
    -webkit-line-clamp: 3;
    line-clamp: 3;
  }
}
</style>