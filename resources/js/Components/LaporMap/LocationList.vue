<template>
  <div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-pin-map-fill me-2"></i>Region Pelaporan</h5>
    </div>
    <div class="card-body p-0">
      <div class="list-group list-group-flush location-list">
        <a
          href="#"
          v-for="(region, index) in regionsWithCount"
          :key="index"
          class="list-group-item list-group-item-action"
          @click.prevent="$emit('focus-region', region.name)"
        >
          <div class="d-flex w-100 justify-content-between">
            <div>
              <h6 class="mb-1">{{ region.name }}</h6>
            </div>
            <small class="text-muted">{{ region.count }} lokasi</small>
          </div>
        </a>
      </div>
    </div>
    <div class="card-footer bg-light">
      <button class="btn btn-sm btn-primary w-100" @click="$emit('show-all-locations')">
        <i class="bi bi-eye me-1"></i> Tampilkan Semua Lokasi
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';


const props = defineProps({
  locationItems: {
    type: Array,
    required: true,
  }
});

defineEmits(['focus-region', 'show-all-locations']);

const regionsWithCount = computed(() => {
  const map = {};
  for (const loc of props.locationItems) {
    const region = loc.region ;
    if (!map[region]) map[region] = 0;
    map[region]++;
  }
  return Object.entries(map).map(([name, count]) => ({ name, count }));
});

</script>

<style scoped>
.location-list {
  max-height: 250px;
  overflow-y: auto;
}

.location-list::-webkit-scrollbar { width: 6px; }
.location-list::-webkit-scrollbar-track { background: #f1f1f1; }
.location-list::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
.location-list::-webkit-scrollbar-thumb:hover { background: #555; }

@media (max-width: 768px) {
  .location-list {
    max-height: 200px;
  }
}

@media (max-width: 576px) {
  .location-list {
    max-height: 180px;
  }
  .btn-sm {
    font-size: 0.8rem;
    padding: 5px 10px;
  }
}
</style>
