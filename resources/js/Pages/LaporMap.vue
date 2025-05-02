<template>
  <div class="map-page">
    <AppLayout>
      <Head title="LaporMap" />
      <!-- Hero Section -->
      <div class="hero-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
              <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInLeft">Peta Interaktif Laporan Penipuan</h1>
              <div class="line-separator animate__animated animate__fadeInLeft animate__delay-1s"></div>
              <p class="lead mt-4 animate__animated animate__fadeInLeft animate__delay-2s">
                Temukan informasi terkini tentang lokasi penipuan yang dilaporkan oleh masyarakat.
                Bantu kami melindungi komunitas dengan berbagi informasi.
              </p>
              <div class="stats mt-5 animate__animated animate__fadeIn animate__delay-4s">
                <div class="row">
                  <div class="col-md-4">
                    <div class="stat-item">
                      <h3 class="stat-number">{{ reportCount }}</h3>
                      <p class="stat-label">Laporan</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="stat-item">
                      <h3 class="stat-number">{{ locationCount }}</h3>
                      <p class="stat-label">Lokasi</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="stat-item">
                      <h3 class="stat-number">{{ userCount }}</h3>
                      <p class="stat-label">Pengguna</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6 position-relative animate__animated animate__fadeInRight">
              <div class="map-container">
                <img src="/map.svg" alt="Peta Interaktif" class="img-fluid" />
                <div class="map-pin pin-1" :class="{ pulse: animatePins }">
                  <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="map-pin pin-2" :class="{ pulse: animatePins }">
                  <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="map-pin pin-3" :class="{ pulse: animatePins }">
                  <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="floating-card">
                  <div class="card-body">
                    <h5><i class="bi bi-shield-fill-check me-2"></i> Kawasan Aman</h5>
                    <p class="mb-0">Bandung, Jawa Barat</p>
                    <small class="text-success">Pelaporan menurun 23%</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Map Section -->
      <div class="container-fluid py-5 map-container">
        <div class="row">
          <div class="col-lg-9 map-area">
            <div class="card shadow-lg">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Peta Interaktif Laporan Penipuan</h3>
                <div class="d-flex">
                  <button class="btn btn-light btn-sm me-2" @click="zoomIn" aria-label="Zoom In">
                    <i class="bi bi-zoom-in"></i>
                  </button>
                  <button class="btn btn-light btn-sm me-2" @click="zoomOut" aria-label="Zoom Out">
                    <i class="bi bi-zoom-out"></i>
                  </button>
                  <button class="btn btn-light btn-sm" @click="resetView" aria-label="Reset View">
                    <i class="bi bi-arrow-repeat"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <div id="map" ref="mapContainer" class="map"></div>
                
                <!-- Loading Indicator -->
                <div class="map-loading" v-if="loading">
                  <div class="spinner-grow text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Alert Notification -->
            <div class="alert alert-info mt-3 shadow-sm" role="alert">
              <div class="d-flex">
                <div class="me-3">
                  <i class="bi bi-info-circle-fill fs-4"></i>
                </div>
                <div>
                  <h5 class="alert-heading">Peta Terupdate!</h5>
                  <p class="mb-0">Data pada peta ini diperbarui secara real-time berdasarkan laporan pengguna. Terakhir diperbarui pada {{ lastUpdated }}.</p>
                </div>
              </div>
            </div>

            <!-- LaporMap Description -->
            <div class="card mt-3 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"><i class="bi bi-question-circle-fill me-2"></i>Apa itu LaporMap?</h5>
                <p class="card-text">
                  LaporMap adalah platform interaktif yang memungkinkan masyarakat untuk melaporkan dan memantau kasus penipuan di berbagai lokasi. Dengan peta real-time, pengguna dapat melihat zona risiko penipuan, detail laporan, dan berkontribusi untuk menciptakan komunitas yang lebih aman.
                </p>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3">
            <!-- Legend Card -->
            <div class="card mb-4 shadow">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Legenda Peta</h5>
              </div>
              <div class="card-body">
                <div v-for="(item, index) in legendItems" :key="index" class="legend-item mb-3">
                  <div class="d-flex align-items-center">
                    <div class="legend-marker" :style="{ backgroundColor: item.color }">
                      <i class="bi bi-geo-alt-fill text-white"></i>
                    </div>
                    <div class="ms-3">
                      <p class="mb-0 fw-bold">{{ item.label }}</p>
                      <small class="text-muted">{{ item.description }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Location List Card -->
            <div class="card mb-4 shadow">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pin-map-fill me-2"></i>Lokasi Pelaporan</h5>
                <span class="badge bg-light text-dark">{{ locationItems.length }}</span>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush location-list">
                  <a href="#" 
                     v-for="(location, index) in locationItems" 
                     :key="index" 
                     class="list-group-item list-group-item-action" 
                     @click.prevent="focusLocation(location)"
                  >
                    <div class="d-flex w-100 justify-content-between">
                      <div>
                        <span class="location-marker" :class="'marker-' + location.type"></span>
                        <h6 class="mb-1">{{ location.name }}</h6>
                      </div>
                      <small class="text-muted">{{ location.reports }} laporan</small>
                    </div>
                    <small class="d-block text-muted mb-1">
                      <i class="bi bi-calendar3 me-1"></i> Terbaru: {{ location.lastReport }}
                    </small>
                    <div class="progress" style="height: 5px;">
                      <div class="progress-bar" :class="'bg-' + getProgressColor(location.type)" role="progressbar"
                           :style="{ width: (location.reports / maxReports * 100) + '%' }"
                           :aria-valuenow="location.reports" aria-valuemin="0" :aria-valuemax="maxReports"></div>
                    </div>
                  </a>
                </div>
              </div>
              <div class="card-footer bg-light">
                <button class="btn btn-sm btn-primary w-100" @click="showAllLocations">
                  <i class="bi bi-eye me-1"></i> Tampilkan Semua Lokasi
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Report Detail Modal -->
      <div class="modal fade" :class="{ 'show': showModal }" tabindex="-1" 
           :style="{ display: showModal ? 'block' : 'none' }" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                {{ selectedLocation ? selectedLocation.name : 'Detail Lokasi' }}
              </h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body" v-if="selectedLocation">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <h6 class="text-muted">Alamat Lengkap</h6>
                    <p>{{ selectedLocation.address }}</p>
                  </div>
                  <div class="mb-3">
                    <h6 class="text-muted">Jenis Penipuan</h6>
                    <div class="d-flex flex-wrap">
                      <span v-for="(type, i) in selectedLocation.fraudTypes" :key="i" 
                            class="badge bg-secondary me-1 mb-1">{{ type }}</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <h6 class="text-muted">Total Korban</h6>
                    <p>{{ selectedLocation.victims }} orang</p>
                  </div>
                  <div class="mb-3">
                    <h6 class="text-muted">Estimasi Kerugian</h6>
                    <p>Rp {{ formatNumber(selectedLocation.totalLoss) }}</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Status:</strong> {{ selectedLocation.status }}
                  </div>
                </div>
              </div>
              
              <hr />
              
              <h6 class="mb-3">Laporan Terbaru</h6>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Jenis</th>
                      <th>Pelapor</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(report, i) in selectedLocation.recentReports" :key="i">
                      <td>{{ report.date }}</td>
                      <td>{{ report.type }}</td>
                      <td>{{ report.reporter }}</td>
                      <td>
                        <span class="badge" :class="'bg-' + getStatusColor(report.status)">
                          {{ report.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import L from 'leaflet';
import 'leaflet.markercluster/dist/leaflet.markercluster.js';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

export default {
  components: {
    AppLayout,
    Head,
  },
  data() {
    return {
      map: null,
      markers: [],
      markerGroups: {},
      lastUpdated: new Date().toLocaleString('id-ID', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }),
      loading: true,
      showModal: false,
      selectedLocation: null,
      maxReports: 843,
      reportCount: 1458,
      locationCount: 237,
      userCount: 3672,
      animatePins: true,
      legendItems: [
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
      ],
      locationItems: [
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
      ]
    };
  },
  mounted() {
    this.initMap();
    this.fetchLocations();
    
    // Simulate loading state
    setTimeout(() => {
      this.loading = false;
    }, 1500);

    // Animate pins
    setInterval(() => {
      this.animatePins = true;
      setTimeout(() => {
        this.animatePins = false;
      }, 3000);
    }, 5000);
  },
  methods: {
    initMap() {
      this.map = L.map(this.$refs.mapContainer).setView([-2.5489, 118.0149], 5);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
      }).addTo(this.map);
      L.control.scale({imperial: false}).addTo(this.map);
      this.markerGroups = {
        high: L.layerGroup().addTo(this.map),
        medium: L.layerGroup().addTo(this.map),
        low: L.layerGroup().addTo(this.map)
      };
    },
    async fetchLocations() {
      try {
        this.loading = true;
        // Simulasi pengambilan data dari API (ganti dengan endpoint API Anda)
        // const response = await fetch('/api/locations');
        // this.locationItems = await response.json();
        this.initMarkers();
      } catch (error) {
        console.error('Error fetching locations:', error);
        alert('Gagal memuat data lokasi.');
      } finally {
        this.loading = false;
      }
    },
    initMarkers() {
      this.markers = [];
      for (const key in this.markerGroups) {
        this.markerGroups[key].clearLayers();
      }
      const clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        disableClusteringAtZoom: 12,
      });
      const icons = {
        high: L.divIcon({
          className: 'custom-marker high-risk',
          html: '<i class="bi bi-geo-alt-fill"></i>',
          iconSize: [30, 30],
          iconAnchor: [15, 30]
        }),
        medium: L.divIcon({
          className: 'custom-marker medium-risk',
          html: '<i class="bi bi-geo-alt-fill"></i>',
          iconSize: [30, 30],
          iconAnchor: [15, 30]
        }),
        low: L.divIcon({
          className: 'custom-marker low-risk',
          html: '<i class="bi bi-geo-alt-fill"></i>',
          iconSize: [30, 30],
          iconAnchor: [15, 30]
        })
      };
      this.locationItems.forEach(location => {
        const marker = L.marker(location.coordinates, {
          icon: icons[location.type]
        }).bindPopup(`
          <div class="map-popup">
            <h6>${location.name}</h6>
            <p class="mb-1"><strong>${location.reports}</strong> laporan penipuan</p>
            <p class="mb-2">Laporan terakhir: ${location.lastReport}</p>
            <button class="btn btn-sm btn-primary w-100 view-details" data-id="${location.id}">
              Lihat Detail
            </button>
          </div>
        `);
        clusterGroup.addLayer(marker);
        this.markers.push({
          id: location.id,
          marker: marker
        });
        marker.on('popupopen', () => {
          setTimeout(() => {
            document.querySelector(`.view-details[data-id="${location.id}"]`)?.addEventListener('click', () => {
              this.openLocationDetail(location);
            });
          }, 100);
        });
      });
      this.map.addLayer(clusterGroup);
    },
    zoomIn() { this.map.zoomIn(); },
    zoomOut() { this.map.zoomOut(); },
    resetView() { this.map.setView([-2.5489, 118.0149], 5); },
    focusLocation(location) {
      this.map.setView(location.coordinates, 12);
      const markerObj = this.markers.find(m => m.id === location.id);
      if (markerObj) markerObj.marker.openPopup();
    },
    showAllLocations() {
      const bounds = [];
      this.locationItems.forEach(loc => bounds.push(loc.coordinates));
      if (bounds.length > 0) this.map.fitBounds(bounds);
    },
    openLocationDetail(location) {
      this.selectedLocation = location;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedLocation = null;
    },
    getProgressColor(type) {
      const colors = { high: 'danger', medium: 'warning', low: 'success' };
      return colors[type] || 'primary';
    },
    getStatusColor(status) {
      const colors = { 'Diproses': 'warning', 'Selesai': 'success', 'Diverifikasi': 'info' };
      return colors[status] || 'secondary';
    },
    formatNumber(num) {
      return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
  }
};
</script>

<style scoped>
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #4285f4 0%, #0d47a1 100%);
  color: white;
  padding: 100px 0;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
  opacity: 0.3;
}

.line-separator {
  height: 5px;
  width: 80px;
  background: linear-gradient(to right, #ffb74d, #f57c00);
  margin-bottom: 20px;
  border-radius: 3px;
}

.hero-content {
  padding-right: 30px;
}

.map-container {
  position: relative;
  border-radius: 20px;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.map-container img {
  border-radius: 20px;
  transition: all 0.5s ease;
}

.map-container:hover img {
  transform: scale(1.03);
}

.map-pin {
  position: absolute;
  color: #f44336;
  font-size: 24px;
  filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
}

.map-pin.pulse {
  animation: pulse 1s infinite;
}

.pin-1 { top: 30%; left: 35%; }
.pin-2 { top: 45%; left: 65%; }
.pin-3 { top: 65%; left: 25%; }

.floating-card {
  position: absolute;
  bottom: 30px;
  right: 30px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 10px;
  padding: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  color: #333;
  max-width: 250px;
  transform: translateY(0);
  transition: transform 0.3s ease;
}

.map-container:hover .floating-card {
  transform: translateY(-10px);
}

.stat-item {
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  padding: 15px;
  transition: all 0.3s ease;
}

.stat-item:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-5px);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 0;
}

.stat-label {
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 1px;
  margin-bottom: 0;
}

/* Map Page */
.map-page {
  background-color: #f8f9fa;
}

.map {
  height: 600px;
  width: 100%;
  z-index: 1;
}

.map-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

:global(.custom-marker) {
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-size: 20px;
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

.legend-marker {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 16px;
}

.location-list {
  max-height: 300px;
  overflow-y: auto;
}

.location-marker {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 8px;
}

.marker-high { background-color: #dc3545; }
.marker-medium { background-color: #ffc107; }
.marker-low { background-color: #28a745; }

:global(.map-popup) { text-align: center; padding: 5px; }

.alert { animation: slideIn 0.5s ease-out; }

@keyframes slideIn {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.location-list::-webkit-scrollbar { width: 8px; }
.location-list::-webkit-scrollbar-track { background: #f1f1f1; }
.location-list::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
.location-list::-webkit-scrollbar-thumb:hover { background: #555; }

/* Animations */
.animate__animated { animation-duration: 1s; animation-fill-mode: both; }
.animate__delay-1s { animation-delay: 0.2s; }
.animate__delay-2s { animation-delay: 0.4s; }
.animate__delay-3s { animation-delay: 0.6s; }
.animate__delay-4s { animation-delay: 0.8s; }

@keyframes fadeInLeft {
  from { opacity: 0; transform: translate3d(-50px, 0, 0); }
  to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate__fadeInLeft { animation-name: fadeInLeft; }

@keyframes fadeInRight {
  from { opacity: 0; transform: translate3d(50px, 0, 0); }
  to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate__fadeInRight { animation-name: fadeInRight; }

@keyframes fadeInUp {
  from { opacity: 0; transform: translate3d(0, 30px, 0); }
  to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate__fadeInUp { animation-name: fadeInUp; }

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate__fadeIn { animation-name: fadeIn; }

/* Responsive adjustments */
@media (max-width: 992px) {
  .hero-content { padding-right: 0; padding-bottom: 50px; text-align: center; }
  .line-separator { margin: 0 auto 20px; }
  .stats { margin-bottom: 50px; }
  .map { height: 400px; }
}

/* Deep selector untuk Leaflet */
:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 3px 14px rgba(0, 0, 0, 0.2);
}

:deep(.leaflet-popup-content) {
  margin: 15px;
}

:deep(.leaflet-container a.leaflet-popup-close-button) {
  top: 8px;
  right: 8px;
  padding: 4px;
  color: #333;
}

:deep(.leaflet-container a.leaflet-popup-close-button:hover) {
  color: #000;
  background-color: #f8f9fa;
  border-radius: 50%;
}
</style>

<style>
@import 'leaflet/dist/leaflet.css';
</style>