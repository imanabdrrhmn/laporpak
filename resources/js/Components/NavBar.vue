<template>
  <div class="navbar-wrapper">
    <!-- Top Navigation Bar -->
    <div class="shadow-sm navbar-full">
      <div class="container-fluid px-0">
        <div class="d-flex align-items-center justify-content-between py-2 px-3 ">
          <Link href="/" class="d-flex align-items-center text-decoration-none ms-5">
            <img src="/logo.svg" alt="LaporPak Logo" class="logo-img me-2" />
            <span class="fw-bold text-primary fs-5 text-lp">LaporPak</span>
          </Link>

          <!-- Mobile Toggle Button -->
          <button 
            class="navbar-toggler d-lg-none" 
            type="button" 
            @click="mobileNavOpen = !mobileNavOpen"
            aria-label="Toggle navigation"
          >
            <i class="bi" :class="mobileNavOpen ? 'bi-x-lg' : 'bi-list'"></i>
          </button>

          <!-- Desktop Login Button -->
          <button
            class="btn btn-primary rounded-2 px-5 py-2 d-none d-lg-block me-5 "
            @click="$emit('trigger-login')"
          >
            Masuk
          </button>
        </div>
      </div>

      <!-- Navigation Menu - Desktop -->
      <div class="border-top border-bottom d-none d-lg-block">
        <div class="container-fluid px-0">
          <ul class="nav nav-fill w-100">
            <li class="nav-item">
              <Link href="/" class="nav-link py-3">
                <i class="bi bi-house-fill me-2"></i>
                <span>Beranda</span>
              </Link>
            </li>
            
            <!-- Dropdown Layanan -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle py-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-collection-fill me-2"></i>
                <span>Layanan</span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <Link href="/pelaporan" class="dropdown-item" :class="{ 'active': $page.url === '/pelaporan' }">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Pelaporan
                  </Link>
                </li>
                <li>
                  <Link href="/verifikasi" class="dropdown-item" :class="{ 'active': $page.url === '/verifikasi' }">
                    <i class="bi bi-check-square-fill"></i>
                    Verifikasi
                  </Link>
                </li>
                <li>
                  <Link href="/lapor-map" class="dropdown-item" :class="{ 'active': $page.url === '/lapor-map' }">
                    <i class="bi bi-geo-alt-fill"></i>
                    Lapor Map
                  </Link>
                </li>
                <li>
                  <Link href="/cari-laporan" class="dropdown-item" :class="{ 'active': $page.url === '/cari-laporan' }">
                    <i class="bi bi-search"></i>
                    Cari Laporan
                  </Link>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <Link href="/feedback" class="nav-link py-3" :class="{ 'active-feedback': $page.url === '/feedback' }">
                <i class="bi bi-chat-dots-fill me-2"></i>
                <span>Feedback</span>
              </Link>
            </li>
            <li class="nav-item">
              <Link href="/tentang-kami" class="nav-link py-3" :class="{ 'active-tentang-kami': $page.url === '/tentang-kami' }">
                <i class="bi bi-people-fill me-2"></i>
                <span>Tentang Kami</span>
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Overlay for Mobile -->
    <div 
      class="sidebar-overlay d-lg-none" 
      :class="{ 'show': mobileNavOpen }" 
      @click="closeMobileNav"
    ></div>

    <!-- Mobile Sidebar Navigation -->
    <div class="sidebar-nav d-lg-none" :class="{ 'open': mobileNavOpen }">
      <div class="sidebar-content">
        <!-- Header for Mobile Menu -->
        <div class="d-flex align-items-center justify-content-between px-4 pb-3 mb-2 border-bottom">
          <Link href="/" class="d-flex align-items-center text-decoration-none" @click="closeMobileNav">
            <img src="/logo.svg" alt="LaporPak Logo" class="logo-img-small me-2" style="height: 32px; width: auto;" />
            <span class="fw-bold text-primary fs-5 text-lp">LaporPak</span>
          </Link>
          <button 
            class="btn-close"
            type="button" 
            @click="closeMobileNav"
            aria-label="Close menu"
          ></button>
        </div>
        
        <ul class="nav flex-column">
          <li class="nav-item">
            <Link href="/beranda" class="nav-link" @click="closeMobileNav">
              <i class="bi bi-house-fill"></i>
              <span>Beranda</span>
            </Link>
          </li>
          
          <!-- Mobile Layanan Collapsible -->
          <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               @click.stop="toggleLayananMobile = !toggleLayananMobile">
              <div>
                <i class="bi bi-collection-fill"></i>
                <span>Layanan</span>
              </div>
              <i class="bi" :class="toggleLayananMobile ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
            </a>
            <div class="collapse" :class="{ 'show': toggleLayananMobile }">
              <div class="nav flex-column ms-4">
                <Link href="/pelaporan" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  <span>Pelaporan</span>
                </Link>
                <Link href="/verifikasi" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-check-square-fill"></i>
                  <span>Verifikasi</span>
                </Link>
                <Link href="/lapor-map" class="nav-link" @click="closeMobileNav">
                  <i class="bi bi-geo-alt-fill"></i>
                  <span>Lapor Map</span>
                </Link>
                <Link href="/cari-laporan" class="nav-link" @click="closeMobileNav" 
                      :class="{ 'active-cari-laporan': $page.url === '/cari-laporan' }">
                  <i class="bi bi-search"></i>
                  <span>Cari Laporan</span>
                </Link>
              </div>
            </div>
          </li>

          <li class="nav-item">
            <Link href="/feedback" class="nav-link" @click="closeMobileNav" :class="{ 'active-feedback': $page.url === '/feedback' }">
              <i class="bi bi-chat-dots-fill"></i>
              <span>Feedback</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link href="/tentang-kami" class="nav-link" @click="closeMobileNav" :class="{ 'active-tentang-kami': $page.url === '/tentang-kami' }">
              <i class="bi bi-people-fill"></i>
              <span>Tentang Kami</span>
            </Link>
          </li>
          <!-- Login Button -->
          <li class="nav-item mt-4 mb-2 d-flex justify-content-center">
            <button
              class="btn btn-primary rounded-pill px-5"
              @click="$emit('trigger-login'); closeMobileNav();"
            >
              Masuk
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  name: 'NavbarLaporPak',
  components : {
    Link
  },
  emits: ['trigger-login'],
  data() {
    return {
      mobileNavOpen: false,
      toggleLayananMobile: false,
      windowWidth: window.innerWidth,
      touchStartX: null
    };
  },
  watch: {
    '$route'() {
      this.closeMobileNav();
    }
  },
  mounted() {
    window.addEventListener('resize', this.handleResize);
    window.addEventListener('keydown', this.handleEscKey);
    document.body.addEventListener('touchstart', this.handleTouchStart);
    document.body.addEventListener('touchmove', this.handleTouchMove);
    this.handleResize();
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.handleResize);
    window.removeEventListener('keydown', this.handleEscKey);
    document.body.removeEventListener('touchstart', this.handleTouchStart);
    document.body.removeEventListener('touchmove', this.handleTouchMove);
  },
  methods: {
    closeMobileNav() {
      this.mobileNavOpen = false;
      this.toggleLayananMobile = false;
      document.body.classList.remove('sidebar-open');
    },
    handleResize() {
      this.windowWidth = window.innerWidth;
      if (this.windowWidth >= 992) {
        this.closeMobileNav();
      }
    },
    handleEscKey(event) {
      if (event.key === 'Escape' && this.mobileNavOpen) {
        this.closeMobileNav();
      }
    },
    handleTouchStart(e) {
      this.touchStartX = e.touches[0].clientX;
    },
    handleTouchMove(e) {
      if (!this.touchStartX) return;
      
      const touchEndX = e.touches[0].clientX;
      const diffX = this.touchStartX - touchEndX;
      
      if (this.mobileNavOpen && diffX > 50) {
        this.closeMobileNav();
      }
      
      if (!this.mobileNavOpen && diffX < -50) {
        this.mobileNavOpen = true;
        document.body.classList.add('sidebar-open');
      }
      
      this.touchStartX = null;
    }
  }
};
</script>

<style scoped>
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

.text-lp {
  color : #008CDB !important;
}

.navbar-wrapper {
  position: relative;
  z-index: 1030;
}

.navbar-full {
  width: 100%;
  margin-left: 0;
  position: relative;
  background-color: white;
}

.logo-img {
  height: 40px !important;
  width: auto !important;
}

.navbar-toggler {
  border: none;
  background: transparent;
  font-size: 1.5rem;
  color: #2563EB;
  padding: 0.25rem 0.5rem;
  cursor: pointer;
}

/* Desktop Navigation */
@media (min-width: 992px) {
  .nav-item {
    border-right: 1px solid #dee2e6;
    flex: 1;
  }

  .nav-item:first-child {
    border-left: 1px solid #dee2e6;
  }

  .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    color: #666;
    transition: all 0.3s;
  }

  .dropdown-menu {
    min-width: 100%;
    border-radius: 0 0 8px 8px;
    border: none;
    margin-top: 0;
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    padding: 10px 0;
    transform: translateY(5px);
    transition: transform 0.2s ease, opacity 0.2s ease;
    opacity: 0;
    display: block;
    pointer-events: none;
  }
  
  .dropdown:hover .dropdown-menu {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
  }

  .dropdown-item {
    padding: 0.8rem 1.2rem;
    display: flex;
    align-items: center;
    color: #555;
    font-weight: 500;
    border-left: 3px solid transparent;
  }

  .dropdown-item .bi {
    font-size: 16px;
    margin-right: 10px;
    transition: transform 0.2s ease;
  }

  .dropdown-item:hover .bi {
    transform: translateX(2px);
  }

  .dropdown-item.active {
    color: #0d6efd;
    font-weight: 600;
    background-color: rgba(13, 110, 253, 0.08);
    border-left: 3px solid #0d6efd;
  }

  .nav-link:hover,
  .dropdown:hover .nav-link {
    color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.03);
  }
  
  /* Dropdown arrow styling */
  .dropdown-toggle::after {
    margin-left: 8px;
    vertical-align: 0.15em;
    transition: transform 0.2s ease;
  }
  
  .dropdown:hover .dropdown-toggle::after {
    transform: rotate(180deg);
  }
}

/* Mobile Navigation */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1045;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
}

.sidebar-overlay.show {
  opacity: 1;
  visibility: visible;
}

.sidebar-nav {
  position: fixed;
  top: 0;
  right: -280px;
  width: 280px;
  height: 100%;
  background-color: white;
  z-index: 1050;
  transition: transform 0.3s ease-in-out;
  box-shadow: -4px 0 15px rgba(0, 0, 0, 0.15);
  overflow-y: auto;
  padding-top: 10px;
}

.sidebar-nav.open {
  transform: translateX(-280px);
}

.sidebar-nav .nav-item {
  margin: 5px 0;
}

.sidebar-nav .nav-link {
  padding: 12px 20px;
  font-weight: 500;
  font-size: 15px;
  border-radius: 8px;
  margin: 0 8px;
}

.nav-flex-column.ms-4 .nav-link {
  padding: 10px 15px 10px 2.5rem;
  font-size: 14px;
}

/* Mobile Navigation Color - Added to match desktop */
@media (max-width: 991px) {
  .sidebar-nav .nav-link {
    color: #666;
    display: flex;
    align-items: center;
  } 
  
  .sidebar-nav .collapse .nav-link {
    color: #666;
  }

  .sidebar-nav .bi {
    font-size: 18px;
    margin-right: 12px;
  }
  
  .sidebar-content {
    padding: 15px 0;
  }
}

/* Active State Mobile */
@media (max-width: 991px) {
  .nav-link {
    transition: all 0.25s ease-in-out;
    border-left: 4px solid transparent;
  }
  
  .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.05);
    border-left-color: rgba(13, 110, 253, 0.3);
    color: #0d6efd !important;
  }

  .nav-link.active,
  .nav-link.active-cari-laporan,
  .nav-link.active-feedback,
  .nav-link.active-tentang-kami {
    color: #0d6efd !important;
    font-weight: 600;
    background-color: rgba(13, 110, 253, 0.1);
    border-left: 4px solid #0d6efd;
    transform: translateX(3px);
  }
  
  /* Animation for dropdown */
  .collapse {
    transition: all 0.3s ease-out;
  }
  
  .collapse:not(.show) {
    max-height: 0;
    overflow: hidden;
  }
  
  .collapse.show {
    max-height: 500px;
  }
}

/* Active State Desktop */
@media (min-width: 992px) {
  .nav-link {
    transition: all 0.2s ease-in-out;
    border-bottom: 3px solid transparent;
  }

  .nav-link:hover {
    color: #0d6efd !important;
    border-bottom-color: rgba(13, 110, 253, 0.3);
  }

  .nav-link.active,
  .nav-link.active-cari-laporan,
  .nav-link.active-feedback,
  .nav-link.active-tentang-kami {
    color: #0d6efd !important;
    font-weight: bold;
    border-bottom: 3px solid #0d6efd;
  }

  /* Dropdown items */
  .dropdown-item {
    transition: all 0.2s ease-in-out;
  }

  .dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(3px);
  }

  .dropdown-item.active {
    color: #0d6efd !important;
    font-weight: bold;
    background-color: #f8f9fa;
  }
}

/* Submenu Mobile */
.nav-flex-column.ms-4 .nav-link {
  padding-left: 2.5rem;
  font-size: 14px;
  border-left: 2px solid transparent;
  margin: 2px 8px;
  border-radius: 6px;
}

.nav-flex-column.ms-4 .nav-link.active {
  border-left: 2px solid #0d6efd;
  background-color: rgba(13, 110, 253, 0.08);
}

/* Mobile menu button styling */
.sidebar-nav .btn-primary {
  padding: 10px 24px;
  font-weight: 500;
  box-shadow: 0 2px 6px rgba(13, 110, 253, 0.25);
  transition: all 0.2s ease;
}

.sidebar-nav .btn-primary:active {
  transform: translateY(1px);
  box-shadow: 0 1px 3px rgba(13, 110, 253, 0.2);
}
</style>