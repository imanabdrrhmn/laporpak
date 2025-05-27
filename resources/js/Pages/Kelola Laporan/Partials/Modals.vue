<template>
    <Teleport to="body">
        <transition name="modal-fade">
            <div v-if="show" class="modal-backdrop" @click.self="closeModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Detail {{ getModalTitle(item) }}
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
                                            <th>Deskripsi</th>
                                            <td>{{ item.description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="status-indicator" :class="getStatusClass(item.status)"></div>
                                                    <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
                                                </div>
                                            </td>
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
                                            <th>Deskripsi</th>
                                            <td>{{ item.description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="status-indicator" :class="getStatusClass(item.status)"></div>
                                                    <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Verification Report -->
                                    <template v-else>
                                        <tr>
                                            <th width="30%">Tanggal</th>
                                            <td>{{ formatDate(item.tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Data</th>
                                            <td>{{ item.data }}</td>
                                        </tr>
                                        <tr>
                                            <th>Query</th>
                                            <td>{{ item.query }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="status-indicator" :class="getStatusClass(item.hasil)"></div>
                                                    <span :class="getStatusTextClass(item.hasil)">{{ item.hasil }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Tutup</button>
                        <button type="button" class="btn btn-primary">Cetak</button>
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
    getStatusClass: Function,
    getStatusTextClass: Function,
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

const getModalTitle = (item) => {
    if (isPenipuanReport.value) return 'Laporan Penipuan';
    if (isInfrastrukturReport.value) return 'Laporan Infrastruktur';
    return 'Verifikasi';
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
}

.modal-body {
    padding: 1rem;
    max-height: 70vh;
    overflow-y: auto;
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
</style>