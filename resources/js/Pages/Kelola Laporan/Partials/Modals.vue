    <template>
    <Teleport to="body">
        <transition name="modal-fade">
            <div v-if="show" class="modal-backdrop" @click.self="closeModal">
                <div class="modal-content" @click.stop>
                    <div class="modal-header">
                        <h5 class="modal-title">Detail {{ item.jenis }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <!-- Dynamic rendering of data based on query type -->
                                    <template v-if="item.jenis === 'NPWP Check V2'">
                                        <tr>
                                            <th width="30%">Nama</th>
                                            <td>{{ item.detailData.nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>{{ item.detailData.nik }}</td>
                                        </tr>
                                        <tr>
                                            <th>NPWP</th>
                                            <td>{{ item.detailData.npwp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-success">{{ item.detailData.status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ item.detailData.keterangan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Pemeriksaan</th>
                                            <td>{{ item.detailData.tanggalPemeriksa }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pemeriksa</th>
                                            <td>{{ item.detailData.pemeriksa }}</td>
                                        </tr>
                                    </template>

                                    <template v-else-if="item.jenis === 'Income Tax Grade'">
                                        <tr>
                                            <th width="30%">Nama</th>
                                            <td>{{ item.detailData.nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>{{ item.detailData.nik }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-success">{{ item.detailData.status }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tax Grade</th>
                                            <td>{{ item.detailData.taxGrade }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ item.detailData.keterangan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Pemeriksaan</th>
                                            <td>{{ item.detailData.tanggalPemeriksa }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pemeriksa</th>
                                            <td>{{ item.detailData.pemeriksa }}</td>
                                        </tr>
                                    </template>

                                    <!-- Default template for other query types -->
                                    <template v-else>
                                        <tr v-for="(value, key) in item.detailData" :key="key">
                                            <th width="30%">{{ formatLabel(key) }}</th>
                                            <td>
                                                <span v-if="key === 'status'" class="badge bg-success">{{ value }}</span>
                                                <span v-else>{{ value }}</span>
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
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    item: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

const closeModal = () => {
    emit('close');
};

const formatLabel = (key) => {
    // Format camelCase keys to Title Case words
    const result = key.replace(/([A-Z])/g, ' $1');
    return result.charAt(0).toUpperCase() + result.slice(1);
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