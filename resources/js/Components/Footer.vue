
<template>
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-5">
          <div class="logo d-flex align-items-center">
            <img src="/logo.svg" alt="LaporPak Logo" class="me-2">
            <span>LaporPak</span>
            <div class="social-icons ms-3">
              <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
              <a href="#" class="me-2"><i class="bi bi-linkedin"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
          <p>
            LaporPak hadir untuk memberdayakan masyarakat agar saling menjaga dan
            berbagi informasi demi mencegah penipuan di era digital.
          </p>
          <button class="btn btn-hubungi mt-2">Hubungi Kami</button>
        </div>

        <div class="col-md-3 offset-md-1">
          <h5 class="mb-4">Fitur</h5>
          <div v-for="feature in features" :key="feature.name" class="feature-item">
            <Link >
              <i class="bi bi-chevron-right me-2"></i>{{ feature.name }}
            </Link>
          </div>
        </div>

        <div class="col-md-3">
          <h5 class="mb-4">Contact</h5>
          <div class="mb-3">
            <i class="bi bi-telephone-fill "></i>
            (406) 555-0120
          </div>
          <div class="mb-3">
            <i class="bi bi-envelope-fill "></i>
            LaporPak@gmail.com
          </div>
          <div>
            <i class="bi bi-geo-alt-fill "></i>
            Jakarta Barat, Jl.Ahmad Yani
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
  { name: 'Lapor Map', route: '/lapor-map' }
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

.contact-icon {
  margin-right: 10px;
  width: 20px;
}

.feature-item {
  margin-bottom: 10px;
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

.social-icons {
  display: flex;
  align-items: center;
}

.social-icons a {
  font-size: 18px;
}
</style>