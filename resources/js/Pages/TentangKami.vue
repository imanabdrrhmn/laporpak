<template>
  <Head title="Tentang Kami" />
  <AppLayout>
    <div class="overflow-hidden bg-light">
      <div class="container py-5">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <!-- Header Section -->
            <header class="text-center mb-5 reveal-section">
              <h1 class="display-4 fw-bold text-dark animate__animated" data-animation="animate__fadeIn">Tentang Kami</h1>
              <p class="lead text-muted animate__animated" data-animation="animate__fadeIn" data-delay="0.2s">
                Membangun kepercayaan melalui transparansi dan partisipasi masyarakat
              </p>
            </header>

            <!-- About Section -->
            <section class="row mb-5 reveal-section">
              <div class="col-md-4 mb-4 mb-md-0 text-center">
                <img
                  src="/tentangkami.svg"
                  alt="LaporPak Illustration"
                  class="img-fluid mb-3 hover-scale animate__animated"
                  data-animation="animate__bounceIn"
                  style="max-height: 250px;"
                />
              </div>
              <div class="col-md-8">
                <div class="animate__animated" data-animation="animate__fadeInRight">
                  <p class="text-muted mb-3">
                    <strong>LaporPak</strong> adalah platform digital inovatif yang memungkinkan masyarakat melaporkan masalah infrastruktur, layanan publik, dan penipuan dengan mudah melalui nomor HP. Kami mengubah laporan masyarakat menjadi data berharga untuk meningkatkan kinerja pemerintah dan perusahaan.
                  </p>
                  <p class="text-muted">
                    Kami berkomitmen menciptakan lingkungan yang aman, baik online maupun offline. LaporPak menyediakan platform transparan untuk berbagi informasi, meningkatkan kesadaran tentang penipuan, dan membangun komunitas yang saling mendukung.
                  </p>
                  <a
                    href="/pelaporan"
                    class="btn btn-primary btn-lg mt-3 animate__animated"
                    data-animation="animate__pulse animate__infinite animate__slower"
                  >
                    Lapor Sekarang
                  </a>
                </div>
              </div>
            </section>

            <!-- Vision Section -->
            <section class="mb-5 reveal-section">
              <h2 class="text-center mb-4 fw-bold text-dark animate__animated" data-animation="animate__fadeInUp">Visi Kami</h2>
              <div class="card bg-primary text-white shadow-lg rounded-3 text-center vision-card p-4 animate__animated" data-animation="animate__zoomIn" data-delay="0.2s">
                <p class="h5 mb-0">Menjadi platform pelaporan terpercaya yang melindungi masyarakat dari kejahatan digital dan offline.</p>
              </div>
            </section>

            <!-- Mission Section -->
            <section class="mb-5 reveal-section">
              <h2 class="text-center mb-4 fw-bold text-dark animate__animated" data-animation="animate__fadeInUp">Misi Kami</h2>
              <div class="row justify-content-center g-4">
                <div v-for="(mission, index) in missions" :key="index" class="col-md-3 col-sm-6">
                  <div
                    class="card border-0 shadow-sm text-center p-4 mission-card h-100 animate__animated"
                    data-animation="animate__zoomIn"
                    :data-delay="`${index * 0.15}s`"
                  >
                    <div
                      class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center mx-auto mb-3 animate__animated"
                      data-animation="animate__bounceIn"
                      :data-delay="`${index * 0.15 + 0.3}s`"
                      style="width: 50px; height: 50px;"
                    >
                      <span class="h5 mb-0">{{ index + 1 }}</span>
                    </div>
                    <p class="mb-0 text-muted">{{ mission }}</p>
                  </div>
                </div>
              </div>
            </section>

            <!-- Why Choose Us & What We Do Section -->
            <section class="row mb-5 reveal-section">
              <div class="col-md-6 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm h-100 card-hover animate__animated" data-animation="animate__fadeInUp" data-delay="0.1s">
                  <div class="card-body p-4">
                    <h3 class="mb-4 fw-bold">
                      <i class="bi bi-lightbulb text-warning me-2 floating"></i>
                      Mengapa Memilih Kami
                    </h3>
                    <p class="text-muted">
                      Kami menawarkan keamanan, kecepatan, dan transparansi dalam setiap laporan. Didukung teknologi AI dan tim verifikasi profesional, kami memastikan laporan Anda diproses dengan akurat dan efisien.
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 card-hover animate__animated" data-animation="animate__fadeInUp" data-delay="0.3s">
                  <div class="card-body p-4">
                    <h3 class="mb-4 fw-bold">
                      <i class="bi bi-gem text-warning me-2 rotating"></i>
                      Apa yang Kami Lakukan
                    </h3>
                    <p class="text-muted">
                      Kami memproses laporan penipuan, mendidik masyarakat, dan berkolaborasi dengan pihak berwenang untuk menciptakan ekosistem yang lebih aman dan terpercaya.
                    </p>
                  </div>
                </div>
              </div>
            </section>

            <!-- Services Section -->
            <section class="mb-5 reveal-section">
              <h2 class="text-center mb-5 fw-bold text-dark animate__animated" data-animation="animate__fadeInUp">Layanan Kami</h2>
              <div class="row g-4">
                <div v-for="(service, index) in services" :key="index" :class="service.colClass">
                  <div
                    class="card border-0 shadow-sm h-100 service-card animate__animated"
                    data-animation="animate__zoomIn"
                    :data-delay="`${index * 0.15}s`"
                  >
                    <div class="card-body p-4 text-center">
                      <div class="mb-3">
                        <div
                          class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center service-icon animate__animated"
                          data-animation="animate__bounceIn"
                          :data-delay="`${index * 0.15 + 0.3}s`"
                          style="width: 70px; height: 70px;"
                        >
                          <i :class="service.icon" class="fs-2"></i>
                        </div>
                      </div>
                      <h4 class="fw-semibold">{{ service.title }}</h4>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- Contact Section -->
            <section class="reveal-section">
              <h2 class="text-center mb-4 fw-bold text-dark animate__animated" data-animation="animate__fadeInUp">Hubungi Kami</h2>
              <div class="row justify-content-center">
                <div class="col-md-8">
                  <div class="card border-0 shadow-sm contact-card animate__animated" data-animation="animate__fadeInUp" data-delay="0.2s">
                    <div class="card-body p-4">
                      <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-envelope me-3 contact-icon fs-4 animate__animated" data-animation="animate__bounceIn" data-delay="0.3s"></i>
                        <span>info@laporpak.id</span>
                      </div>
                      <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-telephone me-3 contact-icon fs-4 animate__animated" data-animation="animate__bounceIn" data-delay="0.5s"></i>
                        <span>+62 812 3456 7890</span>
                      </div>
                      <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-geo-alt me-3 contact-icon fs-4 animate__animated" data-animation="animate__bounceIn" data-delay="0.7s"></i>
                        <span>Jakarta Barat, Jl. Ahmad Yani</span>
                      </div>
                      <div class="d-flex justify-content-start gap-3 mt-4">
                        <a
                          href="https://twitter.com/laporpak"
                          class="text-muted animate__animated"
                          data-animation="animate__bounceIn"
                          data-delay="0.9s"
                        >
                          <i class="bi bi-twitter fs-4"></i>
                        </a>
                        <a
                          href="https://instagram.com/laporpak"
                          class="text-muted animate__animated"
                          data-animation="animate__bounceIn"
                          data-delay="1.1s"
                        >
                          <i class="bi bi-instagram fs-4"></i>
                        </a>
                        <a
                          href="https://linkedin.com/company/laporpak"
                          class="text-muted animate__animated"
                          data-animation="animate__bounceIn"
                          data-delay="1.3s"
                        >
                          <i class="bi bi-linkedin fs-4"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
page.layout = AppLayout;

// Mission data
const missions = ref([
  'Menyediakan platform pelaporan penipuan yang cepat, aman, dan terpercaya.',
  'Mengedukasi masyarakat tentang berbagai bentuk penipuan.',
  'Membangun komunitas yang peduli dan saling menjaga dari kejahatan digital.',
  'Bekerja sama dengan pihak berwenang untuk menindaklanjuti laporan.',
]);

// Services data
const services = ref([
  { title: 'Pelaporan Penipuan Online & Offline', icon: 'bi bi-exclamation-circle', colClass: 'col-md-4' },
  { title: 'Verifikasi Laporan dengan AI dan Tim Khusus', icon: 'bi bi-shield-check', colClass: 'col-md-4' },
  { title: 'Pencarian Database Pelaku Terlapor', icon: 'bi bi-search', colClass: 'col-md-4' },
  { title: 'Edukasi dan Tips Mencegah Penipuan', icon: 'bi bi-lightbulb', colClass: 'col-md-6' },
  { title: 'Dukungan Komunitas dan Kolaborasi', icon: 'bi bi-people', colClass: 'col-md-6' },
]);

// Intersection Observer untuk animasi scroll
const initScrollAnimations = () => {
  if (typeof window !== 'undefined') {
    // Mencari semua elemen dengan kelas .animate__animated
    const animateElements = document.querySelectorAll('.animate__animated');
    
    // Mengaktifkan animasi pada elemen
    animateElements.forEach((element) => {
      // Mendapatkan nama animasi dan delay dari data attributes
      const animation = element.getAttribute('data-animation');
      const delay = element.getAttribute('data-delay');
      
      if (animation) {
        element.classList.remove(animation); // Hapus dulu kelas animasi agar tidak langsung aktif
      }
      
      if (delay) {
        element.style.animationDelay = delay;
      }
    });
    
    // IntersectionObserver untuk memicu animasi saat elemen masuk ke viewport
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          // Animasi untuk section
          if (entry.target.classList.contains('reveal-section')) {
            const animatedElements = entry.target.querySelectorAll('.animate__animated');
            
            // Aktifkan animasi untuk setiap elemen dalam section
            animatedElements.forEach((element) => {
              const animation = element.getAttribute('data-animation');
              if (animation) {
                animation.split(' ').forEach(className => {
                  element.classList.add(className);
                });
              }
            });
          }
          
          // Hapus observer setelah elemen dianimasi
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 }); // Elemen harus terlihat minimal 15% untuk memicu animasi
    
    // Mengamati semua section dengan kelas reveal-section
    document.querySelectorAll('.reveal-section').forEach((section) => {
      observer.observe(section);
    });
  }
};

// Jalankan fungsi animasi setelah komponen dimount
onMounted(() => {
  // Berikan waktu untuk render DOM sebelum menginisialisasi animasi
  setTimeout(() => {
    initScrollAnimations();
  }, 100);
});
</script>

<style scoped>
/* Import Bootstrap Icons dan Animate.css */
@import 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css';
@import 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';

html,
body {
  overflow-x: hidden;
  width: 100%;
  max-width: 100%;
}

/* Color Palette */
:root {
  --primary: #007bff;
  --secondary: #6c757d;
  --light: #f8f9fa;
  --dark: #343a40;
  --warning: #ffc107;
}

/* Typography */
h1,
h2,
h3,
h4,
h5,
h6 {

  color: var(--dark);
}

p,
span {
  line-height: 1.6;
}

/* Backgrounds */
.bg-light {
  background-color: var(--light);
}

/* Card Styles */
.card {
  border-radius: 10px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.mission-card,
.service-card,
.contact-card {
  min-height: 150px;
}

/* Vision Card */
.vision-card {
  background: linear-gradient(135deg, var(--primary) 0%, #0056b3 100%);
  border-radius: 20px;
  position: relative;
  overflow: hidden;
}

.vision-card::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.2), transparent);
  transform: rotate(45deg);
  animation: shine 6s ease-in-out infinite;
}

@keyframes shine {
  0% {
    left: -50%;
    top: -50%;
  }
  100% {
    left: 150%;
    top: 150%;
  }
}

/* Animations */
.floating {
  animation: floating 3s ease-in-out infinite;
}

@keyframes floating {
  0% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
  100% {
    transform: translateY(0);
  }
}

.rotating {
  animation: rotating 6s linear infinite;
}

@keyframes rotating {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.service-icon,
.contact-icon {
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.service-card:hover .service-icon {
  transform: scale(1.15);
  background-color: #0056b3;
}

.contact-card:hover .contact-icon {
  transform: scale(1.2);
  color: var(--primary);
}

.hover-scale {
  transition: transform 0.5s ease;
}

.hover-scale:hover {
  transform: scale(1.05);
}

/* Buttons */
.btn-primary {
  border-radius: 50px;
  padding: 10px 30px;
  font-weight: 500;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-primary:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}

/* Social Icons */
.text-muted:hover {
  color: var(--primary) !important;
}

/* Customized Animation Durations */
.animate__animated {
  --animate-duration: 0.8s;
}

.animate__slower {
  --animate-duration: 3s;
}

/* Responsive Design */
@media (max-width: 768px) {
  .mission-card,
  .service-card,
  .contact-card {
    min-height: auto;
  }

  .display-4 {
    font-size: 2.5rem;
  }

  .lead {
    font-size: 1rem;
  }

  .card-body {
    padding: 1rem;
  }
}

@media (max-width: 576px) {
  .btn-lg {
    padding: 8px 20px;
    font-size: 0.9rem;
  }
}
</style>