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
              :location-items="locationItems"
              @open-location-detail="openLocationDetail"
            />
          </div>
          <div class="col-lg-3">
            <LegendCard :legend-items="legendItems" />
            <LocationList
              :location-items="locationItems"
              :max-reports="maxReports"
              @focus-location="focusLocation"
              @show-all-locations="showAllLocations"
            />
          </div>
        </div>
      </div>
      <ReportDetailModal
        :show-modal="showModal"
        :selected-location="selectedLocation"
        @close="closeModal"
      />
      <Feedback :feedbacks="feedbacks" />
    </AppLayout>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Feedback from '@/Components/Feedback.vue';
import HeroSection from '@/Components/LaporMap/HeroSection.vue';
import MapArea from '@/Components/LaporMap/MapArea.vue';
import LegendCard from '@/Components/LaporMap/LegendCard.vue';
import LocationList from '@/Components/LaporMap/LocationList.vue';
import ReportDetailModal from '@/Components/LaporMap/ReportDetailModal.vue';
import L from 'leaflet';
import 'leaflet.markercluster/dist/leaflet.markercluster.js';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const page = usePage();
const feedbacks = page.props.feedbacks;

const mapArea = ref(null);
const loading = ref(true);
const lastUpdated = new Date().toLocaleString('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
});
const showModal = ref(false);
const selectedLocation = ref(null);
const maxReports = ref(843);
const reportCount = ref(1458);
const locationCount = ref(237);
const userCount = ref(3672);
const animatePins = ref(true);
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
const locationItems = ref([
  {
    id: 1,
    name: 'Jakarta Pusat',
    coordinates: [-6.1753942, 106.8271832],
    reports: 843,
    type: 'high',
    lastReport: '23 Apr 2025',
    address: 'Jakarta Pusat, DKI Jakarta, Indonesia',
    fraudTypes: ['Penipuan Online', 'Investasi Palsu', 'Penipuan Telepon'],
    victims: 1230,
    totalLoss: 3500000000,
    status: 'Area Waspada Tinggi',
    recentReports: [
      { date: '23 Apr 2025', type: 'Investasi Palsu', reporter: 'Anonym', status: 'Diproses' },
      { date: '22 Apr 2025', type: 'Penipuan Online', reporter: 'Anonym', status: 'Selesai' },
      { date: '21 Apr 2025', type: 'Penipuan Telepon', reporter: 'Anonym', status: 'Diverifikasi' }
    ]
  },
  {
    id: 2,
    name: 'Semarang',
    coordinates: [-7.0051453, 110.4381254],
    reports: 450,
    type: 'medium',
    lastReport: '22 Apr 2025',
    address: 'Semarang, Jawa Tengah, Indonesia',
    fraudTypes: ['Penipuan Online', 'Skema Ponzi'],
    victims: 672,
    totalLoss: 1800000000,
    status: 'Area Waspada Menengah',
    recentReports: [
      { date: '22 Apr 2025', type: 'Skema Ponzi', reporter: 'Anonym', status: 'Diproses' },
      { date: '20 Apr 2025', type: 'Penipuan Online', reporter: 'Anonym', status: 'Selesai' }
    ]
  },
  {
    id: 3,
    name: 'Jakarta Barat',
    coordinates: [-6.1683295, 106.7588465],
    reports: 95,
    type: 'low',
    lastReport: '21 Apr 2025',
    address: 'Jakarta Barat, DKI Jakarta, Indonesia',
    fraudTypes: ['Penipuan Email', 'Penipuan Telepon'],
    victims: 120,
    totalLoss: 450000000,
    status: 'Area Waspada Rendah',
    recentReports: [
      { date: '21 Apr 2025', type: 'Penipuan Email', reporter: 'Anonym', status: 'Diproses' }
    ]
  },
  {
    id: 4,
    name: 'Surabaya',
    coordinates: [-7.2574719, 112.7520883],
    reports: 367,
    type: 'medium',
    lastReport: '20 Apr 2025',
    address: 'Surabaya, Jawa Timur, Indonesia',
    fraudTypes: ['Penipuan Kartu Kredit', 'Investasi Palsu', 'Penipuan Online'],
    victims: 520,
    totalLoss: 2100000000,
    status: 'Area Waspada Menengah',
    recentReports: [
      { date: '20 Apr 2025', type: 'Penipuan Kartu Kredit', reporter: 'Anonym', status: 'Diproses' },
      { date: '19 Apr 2025', type: 'Investasi Palsu', reporter: 'Anonym', status: 'Selesai' }
    ]
  },
  {
    id: 5,
    name: 'Bandung',
    coordinates: [-6.9174639, 107.6191228],
    reports: 215,
    type: 'medium',
    lastReport: '19 Apr 2025',
    address: 'Bandung, Jawa Barat, Indonesia',
    fraudTypes: ['Penipuan SMS', 'Skema Ponzi', 'Penipuan Media Sosial'],
    victims: 310,
    totalLoss: 950000000,
    status: 'Area Waspada Menengah',
    recentReports: [
      { date: '19 Apr 2025', type: 'Penipuan Media Sosial', reporter: 'Anonym', status: 'Diproses' },
      { date: '18 Apr 2025', type: 'Penipuan SMS', reporter: 'Anonym', status: 'Diverifikasi' }
    ]
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

const focusLocation = (location) => {
  mapArea.value.focusLocation(location);
};

const showAllLocations = () => {
  mapArea.value.showAllLocations(locationItems.value);
};

const openLocationDetail = (location) => {
  selectedLocation.value = location;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedLocation.value = null;
};
</script>

<style scoped>
@import 'leaflet/dist/leaflet.css';

.map-page {
  background-color: #f8f9fa;
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