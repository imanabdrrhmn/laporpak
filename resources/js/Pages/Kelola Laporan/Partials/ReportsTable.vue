<template>
  <div class="table-responsive ">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light">
        <tr v-if="selectedTab === 'penipuan'">
          <th scope="col" class="ps-4" width="5%">No</th>
          <th scope="col" width="12%">Tanggal</th>
          <th scope="col" width="12%">Kategori</th>
          <th scope="col" width="15%">Sumber Penipuan</th>
          <th scope="col">Deskripsi</th>
          <th scope="col" width="10%">Status</th>
          <th scope="col" width="8%" class="text-center">Detail</th>
        </tr>
        <tr v-else-if="selectedTab === 'infrastruktur'">
          <th scope="col" class="ps-4" width="5%">No</th>
          <th scope="col" width="12%">Tanggal</th>
          <th scope="col" width="12%">Kategori</th>
          <th scope="col">Deskripsi</th>
          <th scope="col" width="10%">Status</th>
          <th scope="col" width="8%" class="text-center">Detail</th>
        </tr>
        <tr v-else>
          <th scope="col" class="ps-4" width="5%">No</th>
          <th scope="col" width="12%">Tanggal</th>
          <th scope="col">Data</th>
          <th scope="col">Query</th>
          <th scope="col" width="10%">Status</th>
          <th scope="col" width="8%" class="text-center">Detail</th>
        </tr>
      </thead>
      <tbody v-if="selectedTab === 'penipuan'">
        <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.created_at) }}</td>
          <td>
            <span class="badge bg-light text-dark border">{{ item.category }}</span>
          </td>
          <td>{{ item.source || 'SMS' }}</td>
          <td class="text-truncate" style="max-width: 250px;">
            {{ truncateText(item.description, 150) }}
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="status-indicator" :class="getStatusClass(item.status)"></div>
              <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
            </div>
          </td>
          <td class="text-center">
            <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="$emit('openDetailModal', item)">
              <i class="fas fa-eye"></i>
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="7" class="text-center py-4 text-muted">
            <i class="fas fa-folder-open mb-2 fa-2x"></i>
            <p>Tidak ada laporan yang tersedia</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else-if="selectedTab === 'infrastruktur'">
        <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.created_at) }}</td>
          <td>
            <span class="badge bg-light text-dark border">{{ item.category }}</span>
          </td>
          <td class="text-truncate" style="max-width: 250px;">
            {{ truncateText(item.description, 150) }}
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="status-indicator" :class="getStatusClass(item.status)"></div>
              <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
            </div>
          </td>
          <td class="text-center">
            <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="$emit('openDetailModal', item)">
              <i class="fas fa-eye"></i>
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-folder-open mb-2 fa-2x"></i>
            <p>Tidak ada laporan yang tersedia</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.tanggal) }}</td>
          <td class="text-truncate" style="max-width: 250px;">
            {{ truncateText(item.data, 150) }}
          </td>
          <td class="text-truncate" style="max-width: 250px;">
            {{ truncateText(item.query, 150) }}
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="status-indicator" :class="getStatusClass(item.hasil)"></div>
              <span :class="getStatusTextClass(item.hasil)">{{ item.hasil }}</span>
            </div>
          </td>
          <td class="text-center">
            <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="$emit('openDetailModal', item)">
              <i class="fas fa-eye"></i>
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-folder-open mb-2 fa-2x"></i>
            <p>Tidak ada verifikasi yang tersedia</p>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  selectedTab: String,
  displayedData: Array,
  formatDate: Function,
  truncateText: Function,
  getStatusClass: Function,
  getStatusTextClass: Function,
});
</script>
