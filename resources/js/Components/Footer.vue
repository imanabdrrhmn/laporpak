<template>
  <footer class="footer">
    <div class="container">
      <div class="row footer-content">
        <!-- Company Info Column -->
        <div class="col-lg-5 col-md-6 col-sm-12 mb-4 mb-md-0">
          <div class="logo d-flex align-items-center flex-wrap">
            <img src="/logo.svg" alt="LaporPak Logo" class="me-2">
            <span>LaporPak</span>
            <div class="social-icons ms-auto ms-sm-3 mt-2 mt-sm-0">
              <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
              <a href="#" class="me-2"><i class="bi bi-linkedin"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
          <p class="company-desc">
            LaporPak hadir untuk memberdayakan masyarakat agar saling menjaga dan
            berbagi informasi demi mencegah penipuan di era digital.
          </p>
          <button class="btn btn-hubungi mt-2">Hubungi Kami</button>
        </div>

        <!-- Features Column -->
        <div class="col-lg-3 col-md-3 col-sm-6 col-6 offset-lg-1 mb-4 mb-md-0">
          <h5 class="mb-3 mb-md-4">Fitur</h5>
          <div v-for="feature in features" :key="feature.name" class="feature-item">
            <Link :href="feature.route">
              <i class="bi bi-chevron-right me-2"></i>{{ feature.name }}
            </Link>
          </div>
        </div>

        <!-- Contact Column -->
        <div class="col-lg-3 col-md-3 col-sm-6 col-6 mb-4 mb-md-0">
          <h5 class="mb-3 mb-md-4">Contact</h5>
          <div class="contact-item mb-3">
            <i class="bi bi-telephone-fill me-2"></i>
            <span>(406) 555-0120</span>
          </div>
          <div class="contact-item mb-3">
            <i class="bi bi-envelope-fill me-2"></i>
            <span class="contact-text">LaporPak@gmail.com</span>
          </div>
          <div class="contact-item">
            <i class="bi bi-geo-alt-fill me-2"></i>
            <span class="contact-text">Jakarta Barat, Jl.Ahmad Yani</span>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <div class="footer-bottom text-center">
    <div class="container">
      <p class="mb-0">© 2025 All Rights Reserved</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const features = ref([
  { name: 'Pelaporan', route: '/pelaporan' },
  { name: 'Verifikasi', route: '/verifikasi' },
  { name: 'Lapor Map', route: '/LaporMap' },
  { name: 'Cari Laporan', route: '/CariLaporan' }
])
</script>

<style scoped>
.footer {
  background-color: #1a2642;
  color: white;
  padding: 2rem 0;
}

.footer a {
  color: white;
  text-decoration: none;
}

.footer a:hover {
  color: #ccc;
}

.footer-bottom {
  background-color: #1a2642;
  border-top: 1px solid #2c3855;
  padding: 1rem 0;
  color: white;
}

.feature-item {
  margin-bottom: 10px;
  white-space: nowrap;
}

.btn-hubungi {
  background-color: white;
  color: #1a2642;
  border-radius: 20px;
  padding: 8px 20px;
  border: none;
  font-weight: bold;
}

.logo {
  font-weight: bold;
  font-size: 24px;
  margin-bottom: 15px;
}

.company-desc {
  font-size: 14px;
  line-height: 1.5;
}

.social-icons {
  display: flex;
  align-items: center;
}

.social-icons a {
  font-size: 18px;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  font-size: 14px;
}

.contact-text {
  word-break: break-word;
}

/* Responsive styles */
@media (max-width: 991.98px) {
  .footer-content {
    text-align: left;
  }
}

@media (max-width: 767.98px) {
  .footer {
    padding: 1.5rem 0;
  }
  
  h5 {
    font-size: 1rem;
  }
  
  .feature-item, .contact-item {
    font-size: 0.9rem;
  }
}

@media (max-width: 575.98px) {
  /* Extra small devices - phones like iPhone SE */
  .footer {
    padding: 1.25rem 0;
  }
  
  .logo {
    font-size: 20px;
    margin-bottom: 10px;
  }
  
  .company-desc {
    font-size: 0.85rem;
  }
  
  .btn-hubungi {
    padding: 6px 16px;
    font-size: 0.85rem;
  }
  
  h5 {
    font-size: 0.95rem;
    margin-bottom: 12px;
  }
  
  .feature-item, .contact-item {
    margin-bottom: 8px;
    font-size: 0.85rem;
  }
  
  .social-icons a {
    font-size: 16px;
  }
}

/* Foldable devices like Galaxy Z Fold 5 and Surface Duo */
@media (max-width: 320px) {
  .logo {
    font-size: 18px;
  }
  
  .company-desc {
    font-size: 0.8rem;
  }
  
  .btn-hubungi {
    font-size: 0.8rem;
    padding: 5px 12px;
  }
  
  .feature-item, .contact-item {
    font-size: 0.8rem;
  }
}

/* For larger tablets in landscape mode */
@media (min-width: 992px) and (max-width: 1199.98px) {
  .contact-text {
    font-size: 0.9rem;
  }
}

/* For very small height devices */
@media (max-height: 600px) {
  .footer {
    padding: 1rem 0;
  }
  
  .footer-bottom {
    padding: 0.5rem 0;
  }
}
</style>