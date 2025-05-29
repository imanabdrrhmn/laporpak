<template>
  <AppLayout>
    <Head title="Manajemen Laporan" />
    <div class="wrapper">
      <div class="container">
        <DashboardHeader :report-stats="reportStats" />
        <FilterSection
          v-model:search-query="searchQuery"
          v-model:selected-category="selectedCategory"
          v-model:selected-service="selectedService"
          v-model:selected-status="selectedStatus"
          v-model:sort-direction="sortDirection"
          v-model:view-mode="viewMode"
          :categories="categories"
          :services="services"
          :has-active-filters="hasActiveFilters"
          :filtered-reports="filteredReports"
          @reset-filters="resetFilters"
        />
        <ReportsContainer
          :reports="paginatedReports"
          :view-mode="viewMode"
          :current-page="currentPage"
          :items-per-page="itemsPerPage"
          :loading="loading"
          :can="props.can"
          @view-report="viewReport"
          @quick-action="onQuickAction"
        />
        <Pagination
          v-if="filteredReports.length > 0"
          v-model:current-page="currentPage"
          :total-pages="totalPages"
        />
        <Modal
          :report="currentReport"
          :is-visible="showModal"
          @close="closeModal"
          @update-status="updateReportStatus"
        />
      </div>
    </div>

    <UnpublishModal
      :is-visible="showUnpublishModal"
      :loading="loadingUnpublish"
      @close="closeUnpublishModal"
      @confirm="confirmUnpublish"
    />

    <Notification
      :show="toast.visible"
      :type="toast.type"
      :message="toast.message"
      @close="toast.visible = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Head, router } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardHeader from './Components/DashboardHeader.vue'
import FilterSection from './Components/FilterSection.vue'
import ReportsContainer from './Components/ReportsContainer.vue'
import Pagination from './Components/Pagination.vue'
import Modal from './Components/LaporanDetailModal.vue'
import UnpublishModal from './Components/UnpublishModal.vue'
import Notification from '@/Components/Notification.vue'

const props = defineProps({
  reports: {
    type: Array,
    default: () => [],
  },
  can: {
    type: Object,
    default: () => ({}),
  },
})

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedService = ref('')
const selectedStatus = ref('')
const sortDirection = ref('desc')
const viewMode = ref('grid')
const currentPage = ref(1)
const itemsPerPage = ref(20)
const loading = ref({})
const showModal = ref(false)
const currentReport = ref({})

const showUnpublishModal = ref(false)
const loadingUnpublish = ref(false)
let unpublishTargetReport = null

const toast = ref({
  visible: false,
  message: '',
  type: 'success',
})

const categories = computed(() => [...new Set(props.reports.map(r => r.category))].filter(Boolean))
const services = computed(() => [...new Set(props.reports.map(r => r.service))].filter(Boolean))
const hasActiveFilters = computed(() =>
  searchQuery.value || selectedCategory.value || selectedService.value || selectedStatus.value
)

const filteredReports = computed(() => {
  return props.reports
    .filter((report) => {
      const matchesCategory = selectedCategory.value ? report.category === selectedCategory.value : true
      const matchesService = selectedService.value ? report.service === selectedService.value : true
      const matchesStatus = selectedStatus.value ? report.status === selectedStatus.value : true
      const matchesSearch = searchQuery.value
        ? report.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          report.category?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          report.service?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          report.user?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          (report.source && report.source.toLowerCase().includes(searchQuery.value.toLowerCase()))
        : true
      return matchesCategory && matchesService && matchesStatus && matchesSearch
    })
    .sort((a, b) =>
      sortDirection.value === 'desc'
        ? new Date(b.created_at) - new Date(a.created_at)
        : new Date(a.created_at) - new Date(b.created_at)
    )
})

const paginatedReports = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value
  const endIndex = startIndex + itemsPerPage.value
  return filteredReports.value.slice(startIndex, endIndex)
})

const totalPages = computed(() => Math.ceil(filteredReports.value.length / itemsPerPage.value) || 1)

const reportStats = computed(() => {
  const stats = {
    total: props.reports.length,
    pending: 0,
    approved: 0,
    rejected: 0,
    published: 0,
    solved: 0,
    unpublished: 0,
  }
  props.reports.forEach((report) => {
    if (report.status in stats) stats[report.status]++
  })
  return stats
})

function showToast(message, type = 'success') {
  toast.value.visible = false;

  setTimeout(() => {
    toast.value.message = message;
    toast.value.type = type;
    toast.value.visible = true;


    setTimeout(() => {
      toast.value.visible = false;
    }, 3000);
  }, 50);
}



const viewReport = (report) => {
  if (!report) return
  currentReport.value = { ...report }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  setTimeout(() => {
    currentReport.value = {}
  }, 300)
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedService.value = ''
  selectedStatus.value = ''
  currentPage.value = 1
}

const actionEndpoints = (report) => ({
  approved: `/pelaporan/${report.id}/terima`,
  rejected: `/pelaporan/${report.id}/tolak`,
  publish: `/pelaporan/${report.id}/publikasikan`,
  solved: `/pelaporan/${report.id}/selesai`,
  unpublish: `/pelaporan/${report.id}/batalkan-publikasi`,
})

const onQuickAction = ({ report, action }) => {
  if (!report?.id) {
    showToast('Laporan tidak valid. Silakan coba lagi.', 'danger')
    return
  }

  if (action === 'unpublish') {
    if (report.status !== 'published') {
      showToast('Hanya laporan yang sudah dipublikasikan yang bisa dibatalkan publikasinya.', 'danger')
      return
    }
    unpublishTargetReport = report
    showUnpublishModal.value = true
    return
  }

  if (action === 'solved') {
    if (report.status !== 'published') {
      showToast('Hanya laporan yang sudah dipublikasikan yang bisa ditandai selesai.', 'danger')
      return
    }
  }

  const url = actionEndpoints(report)[action]
  if (!url) {
    showToast('Aksi tidak dikenali.', 'danger')
    return
  }

  patchStatus(report.id, action, url)
}

const patchStatus = (reportId, action, url, extraData = {}) => {
  loading.value[reportId] = true

  router.patch(url, extraData, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      const idx = props.reports.findIndex((r) => r.id === reportId)
      if (idx !== -1) {
        if (action === 'approved') props.reports[idx].status = 'approved'
        else if (action === 'rejected') props.reports[idx].status = 'rejected'
        else if (action === 'publish') props.reports[idx].status = 'published'
        else if (action === 'solved') props.reports[idx].status = 'solved'
        else if (action === 'unpublish') props.reports[idx].status = 'unpublished'

        if (extraData.reason) props.reports[idx].reason = extraData.reason
      }
      if (currentReport.value.id === reportId) {
        currentReport.value.status = props.reports[idx].status
        if (extraData.reason) currentReport.value.reason = extraData.reason
      }
      loading.value[reportId] = false
      loadingUnpublish.value = false
      showUnpublishModal.value = false
      showToast('Status laporan berhasil diperbarui.', 'success')
    },
    onError: (errors) => {
      showToast('Gagal memperbarui status: ' + (errors.error || 'Terjadi kesalahan'), 'danger')
      loading.value[reportId] = false
      loadingUnpublish.value = false
      showUnpublishModal.value = false
    },
  })
}

const closeUnpublishModal = () => {
  showUnpublishModal.value = false
  unpublishTargetReport = null
}

const confirmUnpublish = (reason) => {
  if (!unpublishTargetReport) return
  if (!reason || !reason.trim()) {
    showToast('Alasan harus diisi', 'danger')
    return
  }
  loadingUnpublish.value = true

  patchStatus(unpublishTargetReport.id, 'unpublish', `/pelaporan/${unpublishTargetReport.id}/batalkan-publikasi`, { reason })
}
</script>

<style scoped>
.wrapper {
  padding: 20px;
}
.container {
  max-width: 1400px;
  margin: 0 auto;
}
</style>
