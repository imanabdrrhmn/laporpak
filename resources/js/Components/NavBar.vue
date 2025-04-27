<template>
  <div class="navbar-wrapper">
    <!-- Top Navigation Bar -->
    <div class="shadow-sm navbar-full">
      <div class="container-fluid px-5">
        <div class="d-flex align-items-center justify-content-between py-2 px-3">
          <Link href="/" class="d-flex align-items-center text-decoration-none">
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
            class="btn btn-primary rounded-2 px-4 py-2 d-none d-lg-block"
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
            <li class="nav-item">
              <Link href="/pelaporan" class="nav-link py-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>Pelaporan</span>
              </Link>
            </li>
            <li class="nav-item">
              <Link href="/verifikasi" class="nav-link py-3">
                <i class="bi bi-check-square-fill me-2"></i>
                <span>Verifikasi</span>
              </Link>
            </li>
            <li class="nav-item">
              <Link href="/lapor-map" class="nav-link py-3">
                <i class="bi bi-geo-alt-fill me-2"></i>
                <span>Lapor Map</span>
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
        <ul class="nav flex-column">
          <li class="nav-item">
            <Link href="/" class="nav-link py-3" @click="closeMobileNav">
              <i class="bi bi-house-fill me-2"></i>
              <span>Beranda</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link href="/pelaporan" class="nav-link py-3" @click="closeMobileNav">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <span>Pelaporan</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link href="/verifikasi" class="nav-link py-3" @click="closeMobileNav">
              <i class="bi bi-check-square-fill me-2"></i>
              <span>Verifikasi</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link href="/lapor-map" class="nav-link py-3" @click="closeMobileNav">
              <i class="bi bi-geo-alt-fill me-2"></i>
              <span>Lapor Map</span>
            </Link>
          </li>
          <li class="nav-item">
            <Link href="/tentang-kami" class="nav-link py-3" @click="closeMobileNav" :class="{ 'active-tentang-kami': $page.url === '/tentang-kami' }">
              <i class="bi bi-people-fill me-2"></i>
              <span>Tentang Kami</span>
            </Link>
          </li>
          <!-- Login Button -->
          <li class="nav-item mt-3 d-flex justify-content-center">
            <button
              class="btn btn-primary btn-sm rounded-2 py-1 px-4"
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
    // Swipe functionality for mobile
    handleTouchStart(e) {
      this.touchStartX = e.touches[0].clientX;
    },
    handleTouchMove(e) {
      if (!this.touchStartX) return;
      
      const touchEndX = e.touches[0].clientX;
      const diffX = this.touchStartX - touchEndX;
      
      // Swipe left to close sidebar
      if (this.mobileNavOpen && diffX > 50) {
        this.closeMobileNav();
      }
      
      // Swipe right to open sidebar
      if (!this.mobileNavOpen && diffX < -50) {
        this.mobileNavOpen = true;
        document.body.classList.add('sidebar-open');
      }
      
      this.touchStartX = null;
    },
    // Method to navigate programmatically if needed
    navigateToTentangKami() {
      this.$router.push('/tentang-kami');
      this.closeMobileNav();
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

/* Desktop Navigation Styling */
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

  .nav-link:hover {
    color: #0d6efd;
    background-color: #f8f9fa;
  }

  .nav-link.router-link-exact-active,
  .nav-link.active-tentang-kami {
    color: #0d6efd;
    font-weight: bold;
    background-color: #f8f9fa;
    border-bottom: 3px solid #0d6efd;
  }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 0.5rem;
  }
}

/* Mobile Navigation Overlay */
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

/* Mobile Sidebar Navigation */
.sidebar-nav {
  position: fixed;
  top: 0;
  right: -240px;
  width: 240px;
  height: 100%;
  background-color: white;
  z-index: 1050;
  transition: transform 0.3s ease;
  box-shadow: -4px 0 8px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

.sidebar-nav.open {
  transform: translateX(-240px);
}

.sidebar-content {
  padding-top: 1rem;
}

/* Mobile Navigation Menu Styling */
@media (max-width: 991px) {
  .nav-link {
    color: #666;
    transition: all 0.3s;
    padding: 0.75rem 1rem;
    border-left: 4px solid transparent;
    display: flex;
    align-items: center;
  }

  .nav-link:hover {
    color: #0d6efd;
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
  }

  .nav-link.router-link-exact-active,
  .nav-link.active-tentang-kami {
    color: #0d6efd;
    font-weight: bold;
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
  }

  .nav-item {
    margin: 0.25rem 0;
  }
}

/* Prevent body scrolling when sidebar is open */
:global(body.sidebar-open) {
  overflow: hidden;
}

/* Medium screens (tablet) */
@media (min-width: 768px) and (max-width: 991px) {
  .sidebar-nav {
    right: -260px;
    width: 260px;
  }
  
  .sidebar-nav.open {
    transform: translateX(-260px);
  }
}

/* iOS safari fixes */
@supports (-webkit-touch-callout: none) {
  .sidebar-nav {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
