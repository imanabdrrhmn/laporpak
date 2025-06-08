<template>
  <div class="header-gradient text-white py-4 py-md-5 text-center">
    <h1 class="display-5 display-md-4 fw-bold">Ulasan</h1>
    <p class="lead mt-2 px-3">
      Bagikan pengalaman Anda dan lihat ulasan yang dikaitkan pengguna lain tentang fitur-fitur LaporPak
    </p>
  </div>
</template>

<style scoped>
.header-gradient {
  background: linear-gradient(135deg, #0d6efd, #0078e7, #003f8a);
}

@media (max-width: 767.98px) {
  .display-5 {
    font-size: 1.75rem;
  }

  .lead {
    font-size: 1rem;
  }

  .header-gradient {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
  }
}

@media (max-width: 575.98px) {
  h1.display-5 {
    font-size: 1.5rem;
  }

  .lead {
    font-size: 0.9rem;
  }
}
</style>