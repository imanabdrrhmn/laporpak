<template>
    <Teleport to="body">
        <transition name="modal-fade">
            <div v-if="show" class="modal-backdrop" @click.self="closeModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Detail Pelaporan {{ report.service }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <!-- Penipuan & Infrastruktur Report -->
                                    <template v-if="isPenipuanReport || isInfrastrukturReport">
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(report.created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td><span class="badge bg-light text-dark border">{{ report.category }}</span></td>
                                        </tr>
                                        <tr v-if="report.service === 'Penipuan'">
                                            <th>Sumber Penipuan</th>
                                            <td>{{ report.source || 'SMS' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="status-badge" :class="getStatusBadgeClass(report.status)">
                                                    {{ report.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <td>{{ report.description }}</td>
                                        </tr>
                                    </template>

                                    <!-- Verification Report -->
                                    <template v-else>
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(report.tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="status-badge" :class="getStatusBadgeClass(report.hasil)">
                                                    {{ report.hasil }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Data</th>
                                            <td>{{ report.data }}</td>
                                        </tr>
                                        <tr>
                                            <th>Query</th>
                                            <td>{{ report.query }}</td>
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
    report: Object,
    formatDate: Function,
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};

// Logika penentuan jenis laporan
const isPenipuanReport = computed(() => {
    return props.report && 'source' in props.report;
});

const isInfrastrukturReport = computed(() => {
    // Infrastruktur: memiliki 'category' tapi TIDAK memiliki 'source'
    return props.report && 'category' in props.report && !('source' in props.report);
});

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
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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
    font-size: 1.25rem;
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
    scrollbar-color: white #f1f1f1;
}

/* Clean White Scrollbar */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: white;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.modal-body::-webkit-scrollbar-thumb {
    background: white;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #f8f9fa;
    border-color: #ced4da;
}

.modal-body::-webkit-scrollbar-corner {
    background: white;
}

.table {
    table-layout: fixed;
    width: 100%;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background: white;
}

.table td, .table th {
    word-wrap: break-word;
    white-space: pre-line;
    vertical-align: top;
    padding: 1rem;
    border-color: #e9ecef;
}

.table th {
    width: 30%;
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.table td {
    background-color: white;
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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 1rem;
    }
    
    .modal-title {
        font-size: 1.1rem;
    }
    
    .table th,
    .table td {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    /* Mobile scrollbar adjustments */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        border-radius: 4px;
    }
}
</style>