<template>
  <footer class="dashboard-footer mt-5">
    <div>© 2025 Sistem Anti-Penipuan. Semua hak dilindungi.</div>
    <div class="footer-links">
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Syarat & Ketentuan</a>
      <a href="#">Bantuan</a>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
.dashboard-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(0,0,0,0.05);
  color: var(--text-muted);
  font-size: 0.875rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
}

.footer-links a {
  color: var(--text-muted);
  text-decoration: none;
  transition: var(--transition);
}

.footer-links a:hover {
  color: var(--primary-color);
}

@media (max-width: 768px) {
  .dashboard-footer {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .footer-links {
    justify-content: center;
  }
}
</style>