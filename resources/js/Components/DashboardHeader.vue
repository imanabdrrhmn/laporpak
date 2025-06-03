<template>
    <div class="dashboard-header mb-4">
        <h1 class="fw-bold text-dark">{{ title }}</h1>
        <h2 class="fw-light text-secondary">{{ subtitle }}</h2>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    subtitle: String,
});
</script>
