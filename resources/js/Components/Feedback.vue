<template>
  <div class="container py-5">
    <h2 class="text-center mb-5">Apa Yang Pengguna Sampaikan Setelah Menggunakan Layanan Kami.</h2>
    
    <div class="testimonial-carousel">
      <!-- Baris Pertama -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="carousel-container overflow-hidden">
            <div class="carousel-track d-flex" :style="{ transform: `translateX(-${scrollPosition1}px)` }">
              <!-- Clone awal kartu untuk infinite scroll -->
              <div v-for="(testimonial, index) in testimonials" :key="`clone-top-start-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
              <div v-for="(testimonial, index) in testimonials" :key="`top-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
              <div v-for="(testimonial, index) in testimonials" :key="`clone-top-end-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
              <div v-for="(testimonial, index) in shuffledTestimonials" :key="`clone-bottom-start-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
              <div v-for="(testimonial, index) in shuffledTestimonials" :key="`bottom-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
              <div v-for="(testimonial, index) in shuffledTestimonials" :key="`clone-bottom-end-${testimonial.id}`" 
                   class="testimonial-card mx-3">
                <div class="card-body">
                  <div class="d-flex">
                    <div class="avatar-section">
                      <div class="avatar-container">
                        <img :src="testimonial.avatar" class="avatar" alt="user avatar">
                      </div>
                      <div class="user-info">
                        <h4 class="user-name">{{ testimonial.name }}</h4>
                        <p class="user-role">{{ testimonial.role }}</p>
                      </div>
                    </div>
                    <div class="ms-auto">
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
    
    <div class="text-center mt-5">
      <button class="btn btn-primary px-4 py-2">
        Lihat semua <i class="bi bi-arrow-right"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      scrollPosition1: 0,
      scrollPosition2: 0,
      scrollSpeed1: 0.8,
      scrollSpeed2: 0.5,
      animationFrameId: null,
      cardWidth: 0,
      cardMargin: 0,
      testimonials: [
        {
          id: 1,
          name: 'Crystal Maiden',
          role: 'UI/UX Designer',
          avatar: 'https://randomuser.me/api/portraits/women/1.jpg',
          content: "I've used other kits, but this one is the best. The attention to detail and usability are truly amazing for all designers. I highly recommend it for any type of project."
        },
        {
          id: 2,
          name: 'Dazzle Healer',
          role: 'Front End Developer',
          avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
          content: "I've used other kits, but this one is the best. The attention to detail and usability are truly amazing for all designers. I highly recommend it for any type of project."
        },
        {
          id: 3,
          name: 'Crystal Maiden',
          role: 'UI/UX Designer',
          avatar: 'https://randomuser.me/api/portraits/women/3.jpg',
          content: "This UI Kit is incredibly helpful for my design process. The icons and illustrations are clean, modern, and save me a lot of time. It's perfect for beginners and professionals alike."
        },
        {
          id: 4,
          name: 'Mirana Marci',
          role: '3D Designer',
          avatar: 'https://randomuser.me/api/portraits/women/4.jpg',
          content: "This UI Kit saved me hours of work. It's intuitive, high-quality, and totally worth the price for all design needs. My projects look more professional and appealing now."
        },
        {
          id: 5,
          name: 'Bimosaurus',
          role: 'Graphic Designer',
          avatar: 'https://randomuser.me/api/portraits/men/5.jpg',
          content: "Amazing work! The color schemes are vibrant, and the icons fit perfectly with all my projects, especially modern UI designs. It makes everything look polished and user-friendly instantly."
        },
        {
          id: 6,
          name: 'Luna Moonfang',
          role: 'UI/UX Designer',
          avatar: 'https://randomuser.me/api/portraits/women/6.jpg',
          content: "This UI Kit is incredibly helpful for my design process. The icons and illustrations are clean, modern, and save me a lot of time. It's perfect for beginners and professionals alike."
        }
      ],
      shuffledTestimonials: [],
      totalWidth: 0
    };
  },
  created() {
    // Shuffle testimonials untuk baris kedua
    this.shuffledTestimonials = [...this.testimonials].sort(() => 0.5 - Math.random());
  },
  mounted() {
    this.$nextTick(() => {
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
    });
    
    window.addEventListener('resize', this.handleResize);
  },
  beforeDestroy() {
    this.stopScrolling();
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    startScrolling() {
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
      
      this.$nextTick(() => {
        // Recalculate card widths
        const card = document.querySelector('.testimonial-card');
        if (card) {
          this.cardWidth = card.offsetWidth;
          
          const style = window.getComputedStyle(card);
          const marginLeft = parseInt(style.marginLeft.replace('px', ''));
          const marginRight = parseInt(style.marginRight.replace('px', ''));
          this.cardMargin = marginLeft + marginRight;
          
          this.totalWidth = (this.cardWidth + this.cardMargin) * this.testimonials.length;
          
          // Reset positions
          this.scrollPosition1 = this.totalWidth;
          this.scrollPosition2 = this.totalWidth;
          
          this.startScrolling();
        }
      });
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
  width: 380px;
  background-color: #fff;
  border-radius: 20px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
  padding: 20px;
  margin-bottom: 20px;
  position: relative;
}

/* Shadow effect like in the image */
.testimonial-card::after {
  content: '';
  position: absolute;
  bottom: -15px;
  left: 5%;
  width: 90%;
  height: 15px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.02));
  filter: blur(5px);
  border-radius: 50%;
  z-index: -1;
}

.avatar-section {
  display: flex;
  align-items: center;
}

.avatar-container {
  margin-right: 15px;
}

.avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  background-color: #f0f8ff;
  padding: 3px;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 20px;
  font-weight: 700;
  color: #1e1e1e;
  margin-bottom: 0;
}

.user-role {
  font-size: 16px;
  color: #6c757d;
  margin-top: 2px;
  margin-bottom: 0;
}

.rating {
  display: flex;
}

.star-icon {
  color: #ff9800;
  font-size: 22px;
  margin-left: 2px;
}

.divider {
  border-top: 1px solid #eaeaea;
  margin: 20px 0;
}

.testimonial-text {
  font-size: 16px;
  line-height: 1.5;
  color: #333;
  margin-bottom: 0;
}

.btn-primary {
  background-color: #0d6efd;
  border: none;
  border-radius: 8px;
  font-weight: 600;
}

@media (max-width: 768px) {
  .testimonial-card {
    width: 300px;
  }
  
  .user-name {
    font-size: 18px;
  }
  
  .user-role {
    font-size: 14px;
  }
  
  .testimonial-text {
    font-size: 14px;
  }
}

/* Hover effect */
.testimonial-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
  transition: all 0.3s ease;
}
</style>