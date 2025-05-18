<template>
  <div class="welcome-header mb-5">
    <div class="animate__animated animate__fadeIn">
      <h1 class="fw-bold mb-2">Selamat datang kembali, {{ namaUser }} 👋</h1>
      <p class="text-muted">Mau ngapain hari ini?</p>
    </div>
    <div class="user-info-container animate__animated animate__fadeIn">
      <div class="level-badge">
        <i class="bi bi-award"></i>
        <span>{{ levelUser }}</span>
      </div>
      <div class="date-badge">
        <span class="day">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long' }) }}</span>
        <span class="date">{{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  namaUser: String,
  levelUser: String
});
</script>

<style scoped>
.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.welcome-header h1 {
  font-size: 2rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.user-info-container {
  display: flex;
  gap: 1rem;
}

.date-badge {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 0.75rem 1rem;
  text-align: center;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}

.date-badge:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.date-badge .day {
  display: block;
  font-weight: 600;
  color: var(--primary-color);
}

.date-badge .date {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.level-badge {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: var(--box-shadow);
  color: var(--warning-color);
  font-weight: 600;
  transition: var(--transition);
}

.level-badge:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.level-badge i {
  font-size: 1.2rem;
}

@media (max-width: 992px) {
  .welcome-header {
    flex-direction: column;
  }
  
  .user-info-container {
    margin-top: 1rem;
    align-self: flex-start;
  }
}

@media (max-width: 576px) {
  .user-info-container {
    flex-direction: column;
  }
  
  .welcome-header h1 {
    font-size: 1.75rem;
  }
}
</style>