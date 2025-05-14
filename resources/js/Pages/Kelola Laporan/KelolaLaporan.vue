<template>
  <AppLayout>
    <Head title="Kelola Laporan" />

    <div class="container-fluid">
      <h3 class="text-center my-3">Kelola Laporan</h3>

      <!-- Sidebar Navigation -->
      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="list-group">
            <div class="list-group-item bg-primary text-white">
              Kelola Laporan
            </div>
            <Link href="/laporan-saya" class="list-group-item list-group-item-action">
              <i class="fas fa-file-alt me-2"></i> Laporan
            </Link>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
          <!-- Tab Switch -->
          <div class="mb-3 d-flex gap-2">
            <button
              class="btn btn-outline-primary"
              :class="{ active: selectedTab === 'reports' }"
              @click="selectedTab = 'reports'"
            >
              Laporan
            </button>
            <button
              class="btn btn-outline-primary"
              :class="{ active: selectedTab === 'verifications' }"
              @click="selectedTab = 'verifications'"
            >
              Verifikasi
            </button>
          </div>

          <div class="card">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                  <thead class="bg-primary text-white">
                    <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>Kategori</th>
                      <th>Deskripsi</th>
                      <th>Status</th>
                      <th>Detail</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in displayedData" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.created_at }}</td>
                      <td>{{ item.category }}</td>
                      <td>{{ item.description }}</td>
                      <td>
                        <span class="badge bg-success">{{ item.status }}</span>
                      </td>
                      <td>
                        <button
                          class="btn btn-sm btn-light"
                          @click="openDetailModal(item)"
                        >
                          ...
                        </button>
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
import { Head, Link } from '@inertiajs/vue3';
import DetailModal from './Partials/Modals.vue';

const props = defineProps({
  reports: Array
});

// Dummy data untuk verifications
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

// Tab control
const selectedTab = ref('reports');

const displayedData = computed(() => {
  return selectedTab.value === 'reports' ? props.reports : verifications.value;
});

// Modal logic
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
</style>
