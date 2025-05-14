<template>
  <AppLayout>
    <Head title="Kelola Laporan" />

    <div class="container-fluid py-3">
      <h3 class="text-center mb-4">Kelola Laporan</h3>

      <div class="row">
        <!-- Sidebar -->
        <div class="col-12 col-md-3 mb-3">
          <div class="list-group shadow-sm">
            <div class="list-group-item bg-primary text-white text-center fw-bold">
              Kelola Laporan
            </div>
            <button
              class="list-group-item list-group-item-action rounded-0"
              :class="{ active: selectedTab === 'reports' }"
              @click="selectedTab = 'reports'"
            >
              <i class="fas fa-file-alt me-2"></i> Laporan
            </button>
            <button
              class="list-group-item list-group-item-action rounded-0"
              :class="{ active: selectedTab === 'verifications' }"
              @click="selectedTab = 'verifications'"
            >
              <i class="fas fa-check-circle me-2"></i> Verifikasi
            </button>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-9">
          <div class="card shadow-sm">
            <div class="card-body p-0">
              <div class="table-responsive overflow-auto">
                <table class="table table-striped table-hover mb-0">
                  <thead class="bg-primary text-white text-nowrap">
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Tanggal</th>
                      <th scope="col">Kategori</th>
                      <th scope="col">Deskripsi</th>
                      <th scope="col">Status</th>
                      <th scope="col">Detail</th>
                    </tr>
                  </thead>
                  <tbody v-if="selectedTab === 'reports'">
                    <tr v-for="(item, index) in displayedData" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td class="text-muted small">{{ formatDate(item.created_at) }}</td>
                      <td>{{ item.category }}</td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.description, 200) }}
                      </td>
                      <td>
                        <span class="badge bg-success">{{ item.status }}</span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-light" @click="openDetailModal(item)">...</button>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr v-for="(item, index) in displayedData" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td class="text-muted small">{{ item.tanggal }}</td>
                      <td>{{ item.jenis }}</td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.data, 200) }}
                      </td>
                      <td>
                        <span class="badge bg-success">{{ item.hasil }}</span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-light" @click="openDetailModal(item)">...</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <DetailModal
      v-if="selectedItem"
      :item="selectedItem"
      :show="showModal"
      @close="closeModal"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DetailModal from './Partials/Modals.vue';

const props = defineProps({
  reports: Array
});

const verifications = ref([
  {
    tanggal: '07 April 2025',
    data: 'NIK: 3201XXXXXXXXXXXX',
    jenis: 'Phone Trace',
    hasil: 'valid',
    detailData: {
      nama: 'Dummy User',
      nik: '3201XXXXXXXXXXXX',
      provider: 'Indosat',
      status: 'Terverifikasi',
      keterangan: 'Nomor terdaftar',
      tanggalPemeriksa: '07 April 2025',
      pemeriksa: 'System'
    }
  },
  {
    tanggal: '08 April 2025',
    data: 'NIK: 3201XXXXXXXXXXXX',
    jenis: 'Blacklist Check',
    hasil: 'valid',
    detailData: {
      nama: 'Dummy User 2',
      nik: '3201XXXXXXXXXXXX',
      status: 'Bersih',
      keterangan: 'Tidak ditemukan dalam blacklist',
      tanggalPemeriksa: '08 April 2025',
      pemeriksa: 'System'
    }
  }
]);

const selectedTab = ref('reports');

const displayedData = computed(() => {
  return selectedTab.value === 'reports' ? props.reports : verifications.value;
});

const showModal = ref(false);
const selectedItem = ref(null);

const openDetailModal = (item) => {
  selectedItem.value = item;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  setTimeout(() => {
    selectedItem.value = null;
  }, 300);
};

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
};

const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};
</script>

<style scoped>
.badge {
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.btn.active {
  background-color: #0d6efd;
  color: white;
}

.list-group-item {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
}

.list-group-item.active {
  background-color: #0d6efd !important;
  color: #fff !important;
  font-weight: normal !important;
}

.table td {
  vertical-align: middle;
  word-break: break-word;
}
</style>
