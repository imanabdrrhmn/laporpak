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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
                        <p class="user-category" :class="getCategoryClass(testimonial.kategori)">
                          {{ testimonial.kategori }}
                        </p>
                      </div>
                    </div>
                    <div class="ms-auto mt-2 mt-sm-0">
                      <div class="rating">
                        <span v-for="star in 5" :key="star" class="star-icon">★</span>
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
      <button class="btn btn-primary px-3 px-md-4 py-2">
        Lihat semua <i class="bi bi-arrow-right ms-1"></i>
      </button>
    </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components : {
    Link
  },
  data() {
    return {
      scrollPosition1: 0,
      scrollPosition2: 0,
      scrollSpeed1: 0.5, // Reduced speed for better mobile experience
      scrollSpeed2: 0.3, // Reduced speed for better mobile experience
      animationFrameId: null,
      cardWidth: 0,
      cardMargin: 0,
      testimonials: [
        {
          id: 1,
          name: 'Crystal Maiden',
          kategori: 'Pelaporan',
          avatar: 'https://randomuser.me/api/portraits/women/1.jpg',
          content: "Sistem pelaporan sangat mudah digunakan. Saya bisa melacak status laporan saya secara real-time dan mendapat tanggapan yang cepat dari petugas terkait."
        },
        {
          id: 2,
          name: 'Dazzle Healer',
          kategori: 'Verifikasi',
          avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
          content: "Proses verifikasi sangat transparan. Semua tahapan dijelaskan dengan baik dan saya merasa aman dengan prosedur yang diterapkan oleh tim LaporPak."
        },
        {
          id: 3,
          name: 'Luna Moonfang',
          kategori: 'Pelaporan',
          avatar: 'https://randomuser.me/api/portraits/women/3.jpg',
          content: "Fitur pelacakan status laporan sangat membantu. Saya tidak perlu berulang kali menghubungi petugas untuk mengetahui perkembangan laporan saya."
        },
        {
          id: 4,
          name: 'Mirana Marci',
          kategori: 'Verifikasi',
          avatar: 'https://randomuser.me/api/portraits/women/4.jpg',
          content: "Sistem verifikasi yang aman dan cepat. Dokumen saya diverifikasi dalam waktu kurang dari 24 jam. Tim verifikasi sangat profesional."
        },
        {
          id: 5,
          name: 'Bimosaurus',
          kategori: 'Pelaporan',
          avatar: 'https://randomuser.me/api/portraits/men/5.jpg',
          content: "Aplikasi yang sangat responsif dan intuitif. Saya bisa melaporkan masalah di lingkungan saya dengan cepat dan mudah. Terima kasih LaporPak!"
        },
        {
          id: 6,
          name: 'Lina Inverse',
          kategori: 'Verifikasi',
          avatar: 'https://randomuser.me/api/portraits/women/6.jpg',
          content: "Proses verifikasi yang terstruktur dengan baik. Notifikasi update status dikirimkan secara berkala, sehingga saya selalu mengetahui tahap verifikasi saya."
        }
      ],
      shuffledTestimonials: [],
      totalWidth: 0,
      isMobile: false
    };
  },
  created() {
    // Shuffle testimonials untuk baris kedua
    this.shuffledTestimonials = [...this.testimonials].sort(() => 0.5 - Math.random());
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
    checkMobile() {
      this.isMobile = window.innerWidth < 768;
      // Adjust scroll speed based on device
      if (this.isMobile) {
        this.scrollSpeed1 = 0.3;
        this.scrollSpeed2 = 0.2;
      } else {
        this.scrollSpeed1 = 0.5;
        this.scrollSpeed2 = 0.3;
      }
    },
    initCarousel() {
      // Mendapatkan lebar kartu untuk kalkulasi
      const card = document.querySelector('.testimonial-card');
      if (card) {
        // Mendapatkan lebar kartu aktual termasuk padding
        this.cardWidth = card.offsetWidth;
        
        // Mendapatkan margin
        const style = window.getComputedStyle(card);
        const marginLeft = parseInt(style.marginLeft.replace('px', ''));
        const marginRight = parseInt(style.marginRight.replace('px', ''));
        this.cardMargin = marginLeft + marginRight;
        
        // Menghitung total lebar satu set kartu
        this.totalWidth = (this.cardWidth + this.cardMargin) * this.testimonials.length;
        
        // Mengatur posisi awal untuk mulai dari kartu asli (bukan clone)
        this.scrollPosition1 = this.totalWidth;
        this.scrollPosition2 = this.totalWidth;
        
        // Mulai animasi
        this.startScrolling();
      }
    },
    getCategoryClass(kategori) {
      return kategori === 'Pelaporan' ? 'category-pelaporan' : 'category-verifikasi';
    },
    startScrolling() {
      // Pause animation on very small screens
      if (window.innerWidth < 320) {
        return;
      }
      
      const animate = () => {
        // Increment scroll position untuk baris atas
        this.scrollPosition1 += this.scrollSpeed1;
        
        // Reset ketika sudah mencapai akhir set tengah
        if (this.scrollPosition1 >= this.totalWidth * 2) {
          this.scrollPosition1 = this.totalWidth;
        }
        
        // Increment scroll position untuk baris bawah
        this.scrollPosition2 += this.scrollSpeed2;
        
        // Reset ketika sudah mencapai akhir set tengah
        if (this.scrollPosition2 >= this.totalWidth * 2) {
          this.scrollPosition2 = this.totalWidth;
        }
        
        this.animationFrameId = requestAnimationFrame(animate);
      };
      
      this.animationFrameId = requestAnimationFrame(animate);
    },
    stopScrolling() {
      if (this.animationFrameId) {
        cancelAnimationFrame(this.animationFrameId);
      }
    },
    handleResize() {
      this.stopScrolling();
      this.checkMobile();
      
      // Add debounce to avoid too many calculations during resize
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = setTimeout(() => {
        this.$nextTick(() => {
          this.initCarousel();
        });
      }, 250);
    },
    handleVisibilityChange() {
      if (document.hidden) {
        this.stopScrolling();
      } else {
        this.startScrolling();
      }
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
  width: 280px; /* Base size for mobile */
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  padding: 15px;
  margin-bottom: 15px;
  position: relative;
}

/* Shadow effect like in the image */
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
  width: 50px;
  height: 50px;
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
  width: 80px; /* Smaller width for mobile */
}

.category-pelaporan {
  color: #fff;
  background-color: #0d6efd;
}

.category-verifikasi {
  color: #fff;
  background-color: #198754;
}

.rating {
  display: flex;
}

.star-icon {
  color: #ff9800;
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
  /* Handle overflow text */
  display: -webkit-box;
  -line-clamp: 4;
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

/* Tablet breakpoint */
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

/* Desktop breakpoint */
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
    -line-clamp: 5;
  }
  
  .star-icon {
    font-size: 22px;
    margin-left: 2px;
  }
  
  .btn-primary {
    font-size: 16px;
  }
}

/* Large screens */
@media (min-width: 992px) {
  .testimonial-card {
    width: 380px;
  }
}

/* Extra large screens */
@media (min-width: 1200px) {
  .testimonial-card {
    width: 400px;
  }
}

/* Hover effect - only on non-touch devices */
@media (hover: hover) {
  .testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    transition: all 0.3s ease;
  }
}

/* Ensure proper display on very small screens */
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
    -line-clamp: 3;
  }
}
</style>