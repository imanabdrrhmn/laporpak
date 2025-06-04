<template>
  <div class="map-page">
    <AppLayout>
      <Head title="LaporMap" />
      <HeroSection
        :report-count="reportCount"
        :location-count="locationCount"
        :user-count="userCount"
        :animate-pins="animatePins"
      />
      <div class="container-fluid py-5 map-container">
        <div class="row">
          <div class="col-lg-9 map-area">
            <MapArea
              ref="mapArea"
              :loading="loading"
              :last-updated="lastUpdated"
              :location-items="filteredLocations"
              @open-location-detail="openLocationDetail"
            />
          </div>
          <div class="col-lg-3">
            <LegendCard :legend-items="legendItems" />
            <LocationList
              :location-items="filteredLocations"
              :max-reports="maxReports"
              @focus-location="focusLocation"
              @show-all-locations="showAllLocations"
              @focus-region="focusRegion"
            />
          </div>
        </div>
      </div>
      <ReportDetailModal
        :show-modal="showModal"
        :selected-location="selectedLocation"
        @close="closeModal"
      />
      <Section/>
      <Feedback :feedbacks="feedbacks" />
    </AppLayout>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Feedback from '@/Components/Feedback.vue';
import HeroSection from '@/Components/LaporMap/HeroSection.vue';
import MapArea from '@/Components/LaporMap/MapArea.vue';
import LegendCard from '@/Components/LaporMap/LegendCard.vue';
import LocationList from '@/Components/LaporMap/LocationList.vue';
import ReportDetailModal from '@/Components/LaporMap/ReportDetailModal.vue';
import Section from '@/Components/Section.vue'

const page = usePage();

const reportCount = ref(page.props.reportCount ?? 0);
const locationCount = ref(page.props.locationCount ?? 0);
const userCount = ref(page.props.userCount ?? 0);
const allLocations = ref(page.props.locationItems ?? []);
const maxReports = ref(page.props.maxReports ?? 0);
const feedbacks = ref(page.props.feedbacks ?? []);

const selectedRegion = ref(null);

const filteredLocations = computed(() => {
  if (!selectedRegion.value) return allLocations.value;
  return allLocations.value.filter(loc => loc.region === selectedRegion.value);
});

const mapArea = ref(null);
const loading = ref(true);
const showModal = ref(false);
const selectedLocation = ref(null);
const animatePins = ref(true);
const lastUpdated = ref(new Date().toLocaleString('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
}));

const legendItems = ref([
  {
    color: '#dc3545',
    label: 'Zona Pelaporan Tinggi',
    description: 'Area dengan lebih dari 500 laporan penipuan.'
  },
  {
    color: '#ffc107',
    label: 'Zona Pelaporan Menengah',
    description: 'Area dengan 100-500 laporan penipuan.'
  },
  {
    color: '#28a745',
    label: 'Zona Pelaporan Rendah',
    description: 'Area dengan kurang dari 100 laporan penipuan.'
  }
]);

onMounted(() => {
  setTimeout(() => {
    loading.value = false;
  }, 1500);

  setInterval(() => {
    animatePins.value = true;
    setTimeout(() => {
      animatePins.value = false;
    }, 3000);
  }, 5000);
});

watch(filteredLocations, async (newLocations) => {
  if (mapArea.value && newLocations.length) {
    await nextTick();
    mapArea.value.showAllLocations(newLocations);
  }
});

watch(() => page.props, (newProps) => {
  reportCount.value = newProps.reportCount ?? 0;
  locationCount.value = newProps.locationCount ?? 0;
  userCount.value = newProps.userCount ?? 0;
  allLocations.value = newProps.locationItems ?? [];
  maxReports.value = newProps.maxReports ?? 0;
  feedbacks.value = newProps.feedbacks ?? [];
});

const focusLocation = (location) => {
  mapArea.value?.focusLocation(location);
};

const showAllLocations = () => {
  selectedRegion.value = null;
  mapArea.value?.showAllLocations(allLocations.value);
};

const openLocationDetail = (location) => {
  selectedLocation.value = location;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedLocation.value = null;
};

const focusRegion = (region) => {
  selectedRegion.value = region;
};
</script>



<style scoped>
@import 'leaflet/dist/leaflet.css';

.map-page {
  background-color: #ffffff;
}

:global(.custom-marker) {
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-size: 18px;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

:global(.high-risk) { color: #dc3545; animation: pulse 1.5s infinite; }
:global(.medium-risk) { color: #ffc107; animation: pulse 2s infinite; }
:global(.low-risk) { color: #28a745; }

@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.2); opacity: 0.7; }
  100% { transform: scale(1); opacity: 1; }
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 3px 14px rgba(0, 0, 0, 0.2);
}

:deep(.leaflet-popup-content) {
  margin: 10px;
}

:deep(.leaflet-container a.leaflet-popup-close-button) {
  top: 6px;
  right: 6px;
  padding: 4px;
  color: #333;
}

:deep(.leaflet-container a.leaflet-popup-close-button:hover) {
  color: #000;
  background-color: #f8f9fa;
  border-radius: 50%;
}

@media (max-width: 992px) {
  .col-lg-9, .col-lg-3 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  .col-lg-3 {
    margin-top: 20px;
  }
}
</style>