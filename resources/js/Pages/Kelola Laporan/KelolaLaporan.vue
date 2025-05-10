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
                        <router-link to="/laporan" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-alt me-2"></i> Laporan
                        </router-link>
                        <router-link to="/verifikasi" class="list-group-item list-group-item-action">
                            <i class="fas fa-check-circle me-2"></i> Verifikasi
                        </router-link>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Verifikasi</th>
                                            <th>Data yang di cek</th>
                                            <th>Jenis Query</th>
                                            <th>Hasil</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in laporanData" :key="index">
                                            <td>{{ item.no }}</td>
                                            <td>{{ item.tanggal }}</td>
                                            <td>{{ item.data }}</td>
                                            <td>{{ item.jenis }}</td>
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
        
        <!-- Import Detail Modal Component -->
        <DetailModal 
            v-if="selectedItem" 
            :item="selectedItem" 
            :show="showModal" 
            @close="closeModal" 
        />
    
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DetailModal from './Partials/Modals.vue';

// Sample data to match the screenshot
const laporanData = ref([
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'NPWP Check V2',
        hasil: 'valid',
        detailData: {
            nama: 'John Doe',
            nik: '3201XXXXXXXXXXXX',
            npwp: '12.345.678.9-012.345',
            status: 'Terverifikasi',
            keterangan: 'Data valid pada sistem NPWP',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'Income Tax Grade',
        hasil: 'valid',
        detailData: {
            nama: 'John Doe',
            nik: '3201XXXXXXXXXXXX',
            status: 'Terverifikasi',
            taxGrade: 'A',
            keterangan: 'Pembayar pajak yang baik',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'Digital NIK Trace',
        hasil: 'valid',
        detailData: {
            nama: 'John Doe',
            nik: '3201XXXXXXXXXXXX',
            status: 'Terverifikasi',
            dataDigital: 'Ditemukan',
            lastUsed: '05 April 2025',
            keterangan: 'NIK terdaftar pada sistem digital',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'Phone Trace',
        hasil: 'valid',
        detailData: {
            nama: 'John Doe',
            nik: '3201XXXXXXXXXXXX',
            nomorTelp: '081234567890',
            status: 'Terverifikasi',
            provider: 'Telkomsel',
            keterangan: 'Nomor telepon terdaftar dengan NIK yang sama',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'Blacklist V1',
        hasil: 'valid',
        detailData: {
            nama: 'John Doe',
            nik: '3201XXXXXXXXXXXX',
            status: 'Bersih',
            keterangan: 'Tidak ditemukan dalam daftar hitam',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'NPWP Check V2',
        hasil: 'valid',
        detailData: {
            nama: 'Jane Doe',
            nik: '3201XXXXXXXXXXXX',
            npwp: '12.345.678.9-012.346',
            status: 'Terverifikasi',
            keterangan: 'Data valid pada sistem NPWP',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'NPWP Check V2',
        hasil: 'valid',
        detailData: {
            nama: 'Sarah Smith',
            nik: '3201XXXXXXXXXXXX',
            npwp: '12.345.678.9-012.347',
            status: 'Terverifikasi',
            keterangan: 'Data valid pada sistem NPWP',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
    { 
        no: 1, 
        tanggal: '07 April 2025', 
        data: 'NIK: 3201XXXXXXXXXXXX', 
        jenis: 'NPWP Check V2',
        hasil: 'valid',
        detailData: {
            nama: 'Robert Johnson',
            nik: '3201XXXXXXXXXXXX',
            npwp: '12.345.678.9-012.348',
            status: 'Terverifikasi',
            keterangan: 'Data valid pada sistem NPWP',
            tanggalPemeriksa: '07 April 2025',
            pemeriksa: 'System'
        }
    },
]);

// Modal state and control
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
</style>