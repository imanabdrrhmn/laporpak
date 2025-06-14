<template>
  <AppLayout>
    <Head title="Cek Populasi" />
    <HeroSection />
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-8">
          <MapContainer 
            @location-selected="handleLocationSelected"
            :population-data="populationData"
            :loading="loading"
          />
        </div>
        <div class="col-lg-4">
          <PopulationInfo 
            :population-data="populationData"
            :selected-location="selectedLocation"
            :loading="loading"
            @radius-changed="handleRadiusChanged"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import HeroSection from '@/Components/CekPopulasi/HeroSection.vue'
import MapContainer from '@/Components/CekPopulasi//MapContainer.vue'
import PopulationInfo from '@/Components/CekPopulasi/PopulationInfo.vue'
import axios from 'axios'

export default {
  name: 'PopulationMapMain',
  components: {
    AppLayout,
    Head,
    HeroSection,
    MapContainer,
    PopulationInfo
  },
  data() {
    return {
      selectedLocation: null,
      populationData: null,
      loading: false,
      currentRadius: 2000 // default 2km in meters
    }
  },
  methods: {
    async handleLocationSelected(location) {
      this.selectedLocation = location
      await this.fetchPopulationData(location, this.currentRadius)
    },
    
    async handleRadiusChanged(radius) {
      this.currentRadius = radius
      if (this.selectedLocation) {
        await this.fetchPopulationData(this.selectedLocation, radius)
      }
    },
    
    async fetchPopulationData(location, radius) {
      this.loading = true
      try {
        const response = await axios.post('/api/population', {
          latitude: location.lat,
          longitude: location.lng,
          radius: radius
        })
        
        this.populationData = {
          ...response.data,
          location: location,
          radius: radius
        }
      } catch (error) {
        console.error('Error fetching population data:', error)
        this.$toast.error('Gagal mengambil data populasi')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>