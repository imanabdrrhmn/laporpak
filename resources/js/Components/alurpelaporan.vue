<!-- Reporting Flow Component -->
<template>
  <section class="flow-section py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-5">Alur Pelaporan</h2>
      
      <div class="row justify-content-center">
        <!-- Step 1: Masuk -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="flow-card h-100 p-4 text-center position-relative">
            <div class="flow-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-person-circle fs-4"></i>
            </div>
            <h5 class="fw-bold">Masuk</h5>
            <p class="text-muted mb-0">
              Masuk terlebih dahulu, jika belum memiliki akun silahkan mendaftar dahulu menggunakan nomor hp atau email.
            </p>
            <div class="flow-arrow d-none d-lg-block"></div>
          </div>
        </div>
        
        <!-- Step 2: Tulis Laporan -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="flow-card h-100 p-4 text-center position-relative">
            <div class="flow-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-pencil-square fs-4"></i>
            </div>
            <h5 class="fw-bold">Tulis Laporan</h5>
            <p class="text-muted mb-0">
              Laporan harus disampaikan dengan jelas dan lengkap, mencakup informasi terkait modus penipuan serta bukti pendukung.
            </p>
            <div class="flow-arrow d-none d-lg-block"></div>
          </div>
        </div>
        
        <!-- Step 3: Proses Verifikasi -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="flow-card h-100 p-4 text-center position-relative">
            <div class="flow-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-shield-check fs-4"></i>
            </div>
            <h5 class="fw-bold">Proses Verifikasi</h5>
            <p class="text-muted mb-0">
              Tim verifikasi akan memastikan validitas laporan. Setelah diverifikasi, laporan akan diteruskan ke instansi berwenang untuk tindak lanjut.
            </p>
            <div class="flow-arrow d-none d-lg-block"></div>
          </div>
        </div>
        
        <!-- Step 4: Selesai -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="flow-card h-100 p-4 text-center">
            <div class="flow-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
              <i class="bi bi-check-circle fs-4"></i>
            </div>
            <h5 class="fw-bold">Selesai</h5>
            <p class="text-muted mb-0">
              Masuk terlebih dahulu, jika belum memiliki akun silahkan mendaftar terlebih dahulu menggunakan nomor hp atau email.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.flow-section {
  background-color: #f8f9fa;
}

.flow-card {
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border-top: 3px solid #0d6efd;
}

.flow-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.flow-icon {
  width: 70px;
  height: 70px;
  font-size: 24px;
  transition: all 0.3s ease;
}

.flow-card:hover .flow-icon {
  transform: scale(1.1);
}

.flow-arrow {
  position: absolute;
  right: -30px;
  top: 50%;
  transform: translateY(-50%);
  width: 60px;
  height: 20px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='20' viewBox='0 0 60 20'%3E%3Cpath d='M0 10 L50 10 L40 5 L50 10 L40 15 Z' fill='%230d6efd' /%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: center;
  z-index: 10;
}

/* Responsive fixes */
@media (max-width: 991.98px) {
  .flow-arrow {
    display: none;
  }
  
  .flow-section {
    padding: 3rem 0;
  }
}

@media (max-width: 767.98px) {
  .flow-card {
    margin-bottom: 1.5rem;
  }
}
</style>