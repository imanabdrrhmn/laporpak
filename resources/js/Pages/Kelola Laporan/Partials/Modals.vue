<template>
    <Teleport to="body">
        <transition name="modal-fade">
            <div v-if="show" class="modal-backdrop" @click.self="closeModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Detail Pelaporan {{ getReportService(item) }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <!-- Penipuan Report -->
                                    <template v-if="isPenipuanReport">
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(item.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td><span class="badge bg-light text-dark border">{{ item.category }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Sumber Penipuan</th>
                                            <td>{{ item.source || 'SMS' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="status-badge" :class="getStatusBadgeClass(item.status)">
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <td>{{ item.description }}</td>
                                        </tr>
                                    </template>

                                    <!-- Infrastruktur Report -->
                                    <template v-else-if="isInfrastrukturReport">
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(item.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td><span class="badge bg-light text-dark border">{{ item.category }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="status-badge" :class="getStatusBadgeClass(item.status)">
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <td>{{ item.description }}</td>
                                        </tr>
                                    </template>

                                    <!-- Verification Report -->
                                    <template v-else>
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(item.tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="status-badge" :class="getStatusBadgeClass(item.hasil)">
                                                    {{ item.hasil }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data</th>
                                            <td>{{ item.data }}</td>
                                        </tr>
                                        <tr>
                                            <th>Query</th>
                                            <td>{{ item.query }}</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Tutup</button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    item: Object,
    formatDate: Function,
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};

const isPenipuanReport = computed(() => {
    return props.item && 'source' in props.item;
});

const isInfrastrukturReport = computed(() => {
    return props.item && 'category' in props.item && !('source' in props.item);
});

const getReportService = (item) => {
    console.log('Modal item:', item); // Debug log
    console.log('isPenipuanReport:', isPenipuanReport.value);
    console.log('isInfrastrukturReport:', isInfrastrukturReport.value);
    
    if (isPenipuanReport.value) return 'Penipuan';
    if (isInfrastrukturReport.value) return 'Infrastruktur';
    return 'Verifikasi';
};

// Keep the old function for backward compatibility if needed
const getModalTitle = (item) => {
    console.log('Modal item:', item); // Debug log
    console.log('isPenipuanReport:', isPenipuanReport.value);
    console.log('isInfrastrukturReport:', isInfrastrukturReport.value);
    
    if (isPenipuanReport.value) return 'Detail Pelaporan Penipuan';
    if (isInfrastrukturReport.value) return 'Detail Pelaporan Infrastruktur';
    return 'Detail Verifikasi';
};

// Fungsi untuk memberikan kelas status badge berdasarkan status
const getStatusBadgeClass = (status) => {
    if (!status) return 'default';
    return status.toLowerCase();
};
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.modal-content {
    width: 90%;
    max-width: 700px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    background-color: #0d6efd;
    color: white;
}

.modal-title {
    margin: 0;
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.btn-close:before {
    content: '×';
}

.btn-close:hover {
    opacity: 1;
    transform: rotate(180deg);
}

.modal-body {
    padding: 1rem;
    max-height: 65vh;
    overflow-y: auto;
    scrollbar-width: thin;
}

/* Custom scrollbar styles */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #0d6efd;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #0b5ed7;
}

.table {
    table-layout: fixed;
    width: 100%;
}

.table td {
    word-wrap: break-word;
    white-space: pre-line;
    vertical-align: top;
}

.table th {
    width: 30%;
    vertical-align: top;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

/* Transition effects */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.badge {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

/* Status Badge Styles */
.status-badge {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
}

.status-badge.pending {
    background-color: #fff8e1;
    color: #ffa000;
}

.status-badge.approved {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-badge.rejected {
    background-color: #ffebee;
    color: #c62828;
}

.status-badge.published {
    background-color: #e3f2fd;
    color: #1565c0;
}

.status-badge.solved {
    background: #d4edda;
    color: #155724;
}

.status-badge.open {
    background: #fff3cd;
    color: #856404;
}

.status-badge.default {
    background-color: #f0f0f0;
    color: #333;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    font-weight: 500;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-primary {
    background-color: #0d6efd;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}
</style>