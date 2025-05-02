<template>
  <section class="hero-section py-5">
    <div class="container">
      <div class="row align-items-center g-5">
        <!-- Judul + Deskripsi -->
        <div class="col-lg-6">
          <div class="text-section px-3">
            <h1 class="display-5 fw-bold mb-3">Laporkan Penipuan</h1>
            <p class="lead fs-5 text-secondary">
              Laporkan nomor HP yang terindikasi melakukan penipuan agar orang lain lebih waspada.
            </p>
          </div>
        </div>

        <!--  Form -->
        <div class="col-lg-6 ">
          <div class="bg-white p-4 p-md-5 rounded-4 shadow">
            <h4 class="text-center fw-bold mb-4">Laporkan Masalah Anda</h4>

            <form @submit.prevent="handleSubmit">
              <!-- Pilih Layanan -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Pilih Layanan</label>
                <div class="d-flex gap-2">
                  <button
                    v-for="service in services"
                    :key="service.value"
                    type="button"
                    class="btn flex-fill"
                    :class="selectedService === service.value ? 'btn-primary text-white' : 'btn-outline-secondary'"
                    @click="selectService(service.value)"
                  >
                    {{ service.label }}
                  </button>
                </div>
              </div>

              <!-- Pilih Kategori -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Pilih Kategori</label>
                <select 
                  v-model="formData.category"
                  class="form-select"
                  required
                >
                  <option disabled value="">Pilih Kategori</option>
                  <option 
                    v-for="category in currentCategories"
                    :key="category.value"
                    :value="category.value"
                  >
                    {{ category.label }}
                  </option>
                </select>
              </div>

              <!-- Deskripsi Kejadian -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi Kejadian</label>
                <textarea
                  v-model="formData.description"
                  class="form-control"
                  placeholder="Tuliskan deskripsi kejadian"
                  rows="4"
                  required
                ></textarea>
              </div>

              <!-- Bukti Upload -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Bukti (Upload Screenshot)</label>
                <input
                  type="file"
                  class="form-control"
                  accept="image/*,.pdf"
                  @change="handleFileUpload"
                />
              </div>

              <!-- Lokasi & Google Maps -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Lokasi Kejadian</label>
                <div ref="mapRef" style="width: 100%; height: 250px;" class="mb-2 rounded border"></div>
                <button type="button" class="btn btn-outline-primary w-100" @click="getCurrentLocation">
                  📍 Lokasi Saya
                </button>
              </div>

              <!-- Tombol Laporkan -->
              <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-2"></i> Laporkan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const services = [
  { label: 'Penipuan', value: 'fraud' },
  { label: 'Infrastruktur', value: 'infrastructure' }
];

const fraudCategories = [
  { label: 'Nomer Telepon', value: 'NomerHp' },
  { label: 'Email', value: 'Email' },
  { label: 'WhatsApp', value: 'WhatsApp' }
];

const infrastructureCategories = [
  { label: 'Jalan', value: 'jalan' },
  { label: 'Lampu', value: 'lampu' },
  { label: 'Saluran', value: 'saluran' }
];

const selectedService = ref('fraud');
const formData = ref({
  category: '',
  description: '',
  evidence: null,
  location: null
});

const currentCategories = computed(() =>
  selectedService.value === 'fraud' ? fraudCategories : infrastructureCategories
);

const selectService = (value) => {
  selectedService.value = value;
  formData.value.category = '';
};

const handleFileUpload = (event) => {
  formData.value.evidence = event.target.files[0];
};

const handleSubmit = () => {
  alert('Laporan berhasil dikirim!');
  formData.value = {
    category: '',
    description: '',
    evidence: null,
    location: null
  };
  selectedService.value = 'fraud';
};

// // GOOGLE MAPS
// const mapRef = ref(null);
// let map, marker;

// onMounted(() => {
//   initMap();
// });

// function initMap() {
//   if (window.google && window.google.maps) {
//     map = new google.maps.Map(mapRef.value, {
//       center: { lat: -6.2, lng: 106.8 },
//       zoom: 12
//     });

//     map.addListener('click', (e) => {
//       placeMarker(e.latLng);
//     });
//   } else {
//     console.error('Google Maps API not loaded');
//   }
// }

// function placeMarker(latLng) {
//   if (marker) marker.setMap(null);
//   marker = new google.maps.Marker({
//     position: latLng,
//     map: map
//   });
//   formData.value.location = {
//     lat: latLng.lat(),
//     lng: latLng.lng()
//   };
// }

// function getCurrentLocation() {
//   if (navigator.geolocation) {
//     navigator.geolocation.getCurrentPosition((position) => {
//       const pos = {
//         lat: position.coords.latitude,
//         lng: position.coords.longitude
//       };
//       map.setCenter(pos);
//       placeMarker(pos);
//     }, () => {
//       alert('Gagal mendapatkan lokasi.');
//     });
//   } else {
//     alert('Browser tidak mendukung Geolocation.');
//   }
// }
// </script>

<style scoped>
.hero-section {
  background-color: #F0F6FF;
  min-height: calc(100vh - 130px); /* Subtract navbar height */
  width: 100%;
  display: flex;
  align-items: center;
}

.text-section {
  color: #000;
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .hero-section {
    min-height: auto;
    padding-top: 2rem;
    padding-bottom: 2rem;
  }
}
</style>