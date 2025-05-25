<template>
  <nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a class="page-link" href="#" @click.prevent="goTo(currentPage - 1)">
          <span aria-hidden="true">«</span>
        </a>
      </li>
      <li v-for="page in totalPages" 
          :key="page" 
          class="page-item"
          :class="{ active: page === currentPage }">
        <a class="page-link" href="#" @click.prevent="goTo(page)">{{ page }}</a>
      </li>
      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <a class="page-link" href="#" @click.prevent="goTo(currentPage + 1)">
          <span aria-hidden="true">»</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
const props = defineProps({
  totalPages: Number,
  currentPage: Number,
});
const emit = defineEmits(['goToPage']);
function goTo(page) {
  emit('goToPage', page);
}
</script>
