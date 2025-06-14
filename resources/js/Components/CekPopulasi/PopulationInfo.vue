<template>
  <div class="population-info">
    <!-- Radius Control Card -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light border-0">
        <h6 class="card-title mb-0">
          <i class="fas fa-sliders-h text-primary me-2"></i>
          Pengaturan Radius
        </h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label for="radiusRange" class="form-label d-flex justify-content-between">
            <span>Radius Analisis</span>
            <span class="badge bg-primary">{{ (currentRadius/1000).toFixed(1) }} km</span>
          </label>
          <input 
            type="range" 
            class="form-range" 
            id="radiusRange"
            min="500" 
            max="10000" 
            step="250"
            v-model="currentRadius"
            @input="onRadiusChange"
          >
          <div class="d-flex justify-content-between small text-muted mt-1">
            <span>0.5 km</span>
            <span>10 km</span>
          </div>
        </div>
        
        <!-- Quick Radius Buttons -->
        <div class="d-flex gap-2 flex-wrap">
          <button 
            v-for="preset in radiusPresets" 
            :key="preset.value"
            class="btn btn-sm"
            :class="currentRadius === preset.value ? 'btn-primary' : 'btn-outline-primary'"
            @click="setRadius(preset.value)"
          >
            {{ preset.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Population Data Card -->
    <div class="card shadow-lg border-0">
      <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">
          <i class="fas fa-chart-bar text-primary me-2"></i>
          Informasi Populasi
        </h5>
      </div>
      <div class="card-body">
        <div v-if="!selectedLocation" class="text-center py-5">
          <div class="mb-3">
            <i class="fas fa-mouse-pointer fa-3x text-muted"></i>
          </div>
          <h6 class="text-muted">Klik pada peta untuk memulai</h6>
          <p class="small text-muted mb-0">
            Pilih lokasi di peta untuk melihat estimasi populasi di area tersebut
          </p>
        </div>

        <div v-else-if="loading" class="text-center py-4">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <h6 class="text-muted">Menganalisis Data</h6>
          <p class="small text-muted mb-0">Memproses data GHSL...</p>
        </div>

        <div v-else-if="populationData" class="population-details">
          <!-- Main Stats -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="stat-highlight">
                <div class="stat-value">
                  {{ formatNumber(populationData.total_population) }}
                </div>
                <div class="stat-label">Estimasi Total Populasi</div>
              </div>
            </div>
          </div>

          <!-- Detail Stats -->
          <div class="row g-3 mb-4">
            <div class="col-6">
              <div class="stat-card">
                <div class="stat-number">{{ formatNumber(populationData.density_per_km2) }}</div>
                <div class="stat-desc">jiwa/km²</div>
              </div>
            </div>
            <div class="col-6">
              <div class="stat-card">
                <div class="stat-number">{{ (populationData.coverage_area).toFixed(2) }}</div>
                <div class="stat-desc">km² area</div>
              </div>
            </div>
          </div>

          <!-- Location Info -->
          <div class="location-info mb-4">
            <h6 class="mb-2">
              <i class="fas fa-map-pin text-danger me-2"></i>
              Koordinat Lokasi
            </h6>
            <div class="coordinate-display">
              <div class="coordinate-item">
                <span class="label">Latitude:</span>
                <span class="value">{{ selectedLocation.lat.toFixed(6) }}°</span>
              </div>
              <div class="coordinate-item">
                <span class="label">Longitude:</span>
                <span class="value">{{ selectedLocation.lng.toFixed(6) }}°</span>
              </div>
            </div>
          </div>

          <!-- Data Source Info -->
          <div class="data-source">
            <h6 class="mb-2">
              <i class="fas fa-database text-info me-2"></i>
              Sumber Data
            </h6>
            <div class="source-info">
              <div class="d-flex justify-content-between small mb-1">
                <span>Dataset:</span>
                <span class="fw-bold">GHSL Population Grid</span>
              </div>
              <div class="d-flex justify-content-between small mb-1">
                <span>Resolusi:</span>
                <span class="fw-bold">100m × 100m</span>
              </div>
              <div class="d-flex justify-content-between small">
                <span>Tahun Data:</span>
                <span class="fw-bold">{{ populationData.data_year || '2020' }}</span>
              </div>
            </div>
          </div>

          <!-- Export Button -->
          <div class="mt-4">
            <button class="btn btn-outline-primary btn-sm w-100" @click="exportData">
              <i class="fas fa-download me-2"></i>
              Export Data
            </button>
          </div>
        </div>

        <div v-else class="text-center py-4">
          <div class="mb-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
          </div>
          <h6 class="text-muted">Data tidak tersedia</h6>
          <p class="small text-muted mb-0">
            Tidak dapat mengambil data populasi untuk lokasi ini
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PopulationInfo',
  props: {
    populationData: {
      type: Object,
      default: null
    },
    selectedLocation: {
      type: Object,
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      currentRadius: 2000, // default 2km
      radiusPresets: [
        { label: '1 km', value: 1000 },
        { label: '2 km', value: 2000 },
        { label: '3 km', value: 3000 },
        { label: '5 km', value: 5000 }
      ]
    }
  },
  methods: {
    onRadiusChange() {
      // Debounce untuk mengurangi API calls
      clearTimeout(this.radiusTimeout)
      this.radiusTimeout = setTimeout(() => {
        this.$emit('radius-changed', this.currentRadius)
      }, 500)
    },
    
    setRadius(radius) {
      this.currentRadius = radius
      this.$emit('radius-changed', radius)
    },
    
    formatNumber(num) {
      if (!num && num !== 0) return '-'
      return num.toLocaleString('id-ID')
    },
    
    exportData() {
      if (!this.populationData) return
      
      const data = {
        location: this.selectedLocation,
        population: this.populationData,
        exported_at: new Date().toISOString()
      }
      
      const blob = new Blob([JSON.stringify(data, null, 2)], { 
        type: 'application/json' 
      })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `population_data_${Date.now()}.json`
      a.click()
      URL.revokeObjectURL(url)
    }
  },
  
  beforeUnmount() {
    clearTimeout(this.radiusTimeout)
  }
}
</script>

<style scoped>
.stat-highlight {
  background: linear-gradient(135deg, #0062cc, #0078e7);
  color: white;
  padding: 1.5rem;
  border-radius: 1rem;
  text-align: center;
  margin-bottom: 1rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

.stat-card {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 0.5rem;
  text-align: center;
  border: 1px solid #e9ecef;
}

.stat-number {
  font-size: 1.25rem;
  font-weight: bold;
  color: #0062cc;
  margin-bottom: 0.25rem;
}

.stat-desc {
  font-size: 0.8rem;
  color: #6c757d;
}

.coordinate-display {
  background: #f8f9fa;
  padding: 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e9ecef;
}

.coordinate-item {
  display: flex;
  justify-content: between;
  margin-bottom: 0.25rem;
}

.coordinate-item:last-child {
  margin-bottom: 0;
}

.coordinate-item .label {
  color: #6c757d;
  font-size: 0.875rem;
  flex: 1;
}

.coordinate-item .value {
  font-family: 'Courier New', monospace;
  font-weight: bold;
  color: #0062cc;
  font-size: 0.875rem;
}

.source-info {
  background: #f8f9fa;
  padding: 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e9ecef;
}

.form-range::-webkit-slider-thumb {
  background: #0078e7;
}

.form-range::-moz-range-thumb {
  background: #0078e7;
  border: none;
}

.card {
  border-radius: 0.75rem;
}

.btn-sm {
  font-size: 0.8rem;
  padding: 0.25rem 0.75rem;
}

@media (max-width: 768px) {
  .stat-value {
    font-size: 1.5rem;
  }
  
  .stat-number {
    font-size: 1rem;
  }
  
  .coordinate-item {
    flex-direction: column;
    gap: 0.125rem;
  }
}
</style>