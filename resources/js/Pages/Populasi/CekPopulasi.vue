<template>
  <AppLayout>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Peta Estimasi Populasi Indonesia</h5>
            </div>
            <div class="card-body p-0">
              <div id="map" style="height: 600px;"></div>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Pengaturan & Hasil</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label for="radius" class="form-label">Radius (km)</label>
                <select v-model="selectedRadius" class="form-select" id="radius">
                  <option value="1">1 km</option>
                  <option value="2">2 km</option>
                  <option value="3">3 km</option>
                  <option value="5">5 km</option>
                </select>
              </div>
              
              <div class="alert alert-info" v-if="!selectedPoint">
                <i class="bi bi-info-circle"></i>
                Klik pada peta untuk memilih lokasi
              </div>
              
              <div v-if="selectedPoint" class="mb-3">
                <h6>Koordinat Terpilih:</h6>
                <p class="mb-1"><strong>Latitude:</strong> {{ selectedPoint.lat.toFixed(6) }}</p>
                <p class="mb-1"><strong>Longitude:</strong> {{ selectedPoint.lng.toFixed(6) }}</p>
                <p class="mb-0"><strong>Radius:</strong> {{ selectedRadius }} km</p>
              </div>
              
              <div v-if="loading" class="text-center mb-3">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Menghitung estimasi populasi...</p>
              </div>
              
              <div v-if="populationData" class="alert alert-success">
                <h6 class="alert-heading">
                  <i class="bi bi-people-fill"></i>
                  Hasil Estimasi Populasi
                </h6>
                <hr>
                <p class="mb-1"><strong>Total Populasi:</strong></p>
                <h4 class="text-primary">{{ formatNumber(populationData.total_population) }} jiwa</h4>
                <small class="text-muted">
                  Berdasarkan data GHSL dalam radius {{ populationData.radius }} km
                </small>
                
                <div class="mt-3" v-if="populationData.details">
                  <h6>Detail Analisis:</h6>
                  <ul class="list-unstyled small">
                    <li><strong>Grid yang dianalisis:</strong> {{ populationData.details.grid_count }}</li>
                    <li><strong>Area total:</strong> {{ populationData.details.area_km2 }} km²</li>
                    <li><strong>Kepadatan rata-rata:</strong> {{ populationData.details.density_per_km2 }} jiwa/km²</li>
                  </ul>
                </div>
              </div>
              
              <div v-if="error" class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                {{ error }}
              </div>
              
              <button 
                v-if="selectedPoint" 
                @click="clearSelection" 
                class="btn btn-outline-secondary w-100"
              >
                <i class="bi bi-arrow-clockwise"></i>
                Reset Peta
              </button>
            </div>
          </div>
          
          <!-- History Panel -->
          <div class="card mt-3" v-if="searchHistory.length > 0">
            <div class="card-header">
              <h6 class="mb-0">Riwayat Pencarian</h6>
            </div>
            <div class="card-body">
              <div class="list-group list-group-flush">
                <div 
                  v-for="(item, index) in searchHistory" 
                  :key="index"
                  class="list-group-item px-0 py-2 border-0 border-bottom"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <small class="text-muted">
                        {{ item.lat.toFixed(4) }}, {{ item.lng.toFixed(4) }}
                      </small>
                      <br>
                      <strong class="text-primary">{{ formatNumber(item.population) }} jiwa</strong>
                      <small class="text-muted">({{ item.radius }}km)</small>
                    </div>
                    <button 
                      @click="goToHistoryPoint(item)" 
                      class="btn btn-sm btn-outline-primary"
                    >
                      <i class="bi bi-geo-alt"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  name: 'PopulationMap',
  components: {
    AppLayout
  },
  data() {
    return {
      map: null,
      selectedPoint: null,
      selectedRadius: 2,
      currentMarker: null,
      currentCircle: null,
      loading: false,
      populationData: null,
      error: null,
      searchHistory: []
    }
  },
  mounted() {
    this.initMap();
  },
  watch: {
    selectedRadius() {
      if (this.selectedPoint) {
        this.updateCircle();
        this.fetchPopulationData();
      }
    }
  },
  methods: {
    initMap() {
      // Initialize map centered on Indonesia
      this.map = L.map('map').setView([-2.5489, 118.0149], 5);
      
      // Add tile layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(this.map);
      
      // Add click event listener
      this.map.on('click', this.onMapClick);
      
      // Fix marker icons
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
      });
    },
    
    onMapClick(e) {
      this.selectedPoint = e.latlng;
      this.error = null;
      this.populationData = null;
      
      // Clear existing marker and circle
      this.clearMapElements();
      
      // Add new marker
      this.currentMarker = L.marker([e.latlng.lat, e.latlng.lng])
        .addTo(this.map)
        .bindPopup('Menghitung populasi...')
        .openPopup();
      
      // Add circle
      this.updateCircle();
      
      // Fetch population data
      this.fetchPopulationData();
    },
    
    updateCircle() {
      if (this.currentCircle) {
        this.map.removeLayer(this.currentCircle);
      }
      
      if (this.selectedPoint) {
        this.currentCircle = L.circle([this.selectedPoint.lat, this.selectedPoint.lng], {
          color: 'red',
          fillColor: '#f03',
          fillOpacity: 0.2,
          radius: this.selectedRadius * 1000 // Convert km to meters
        }).addTo(this.map);
      }
    },
    
    async fetchPopulationData() {
      if (!this.selectedPoint) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await fetch('/api/population', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            latitude: this.selectedPoint.lat,
            longitude: this.selectedPoint.lng,
            radius: this.selectedRadius
          })
        });
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
          this.populationData = data.data;
          
          // Update marker popup
          if (this.currentMarker) {
            this.currentMarker.setPopupContent(`
              <div class="text-center">
                <strong>Estimasi Populasi</strong><br>
                <span class="text-primary fs-5">${this.formatNumber(data.data.total_population)} jiwa</span><br>
                <small>Radius: ${data.data.radius} km</small>
              </div>
            `);
          }
          
          // Add to history
          this.addToHistory({
            lat: this.selectedPoint.lat,
            lng: this.selectedPoint.lng,
            radius: this.selectedRadius,
            population: data.data.total_population,
            timestamp: new Date()
          });
        } else {
          throw new Error(data.message || 'Gagal mengambil data populasi');
        }
      } catch (error) {
        console.error('Error fetching population data:', error);
        this.error = 'Gagal mengambil data populasi. Silakan coba lagi.';
        
        if (this.currentMarker) {
          this.currentMarker.setPopupContent('Error: Gagal mengambil data populasi');
        }
      } finally {
        this.loading = false;
      }
    },
    
    clearMapElements() {
      if (this.currentMarker) {
        this.map.removeLayer(this.currentMarker);
        this.currentMarker = null;
      }
      if (this.currentCircle) {
        this.map.removeLayer(this.currentCircle);
        this.currentCircle = null;
      }
    },
    
    clearSelection() {
      this.selectedPoint = null;
      this.populationData = null;
      this.error = null;
      this.clearMapElements();
    },
    
    formatNumber(num) {
      if (!num) return '0';
      return new Intl.NumberFormat('id-ID').format(Math.round(num));
    },
    
    addToHistory(item) {
      this.searchHistory.unshift(item);
      // Keep only last 5 searches
      if (this.searchHistory.length > 5) {
        this.searchHistory = this.searchHistory.slice(0, 5);
      }
    },
    
    goToHistoryPoint(item) {
      this.map.setView([item.lat, item.lng], 12);
      // Simulate clicking on that point
      this.selectedPoint = { lat: item.lat, lng: item.lng };
      this.selectedRadius = item.radius;
      
      this.clearMapElements();
      
      this.currentMarker = L.marker([item.lat, item.lng])
        .addTo(this.map)
        .bindPopup(`
          <div class="text-center">
            <strong>Estimasi Populasi</strong><br>
            <span class="text-primary fs-5">${this.formatNumber(item.population)} jiwa</span><br>
            <small>Radius: ${item.radius} km</small>
          </div>
        `)
        .openPopup();
      
      this.updateCircle();
      this.populationData = {
        total_population: item.population,
        radius: item.radius
      };
    }
  }
}
</script>

<style scoped>
.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.list-group-item:hover {
  background-color: #f8f9fa;
}

#map {
  border-radius: 0 0 0.375rem 0.375rem;
}

.spinner-border {
  width: 1.5rem;
  height: 1.5rem;
}
</style>