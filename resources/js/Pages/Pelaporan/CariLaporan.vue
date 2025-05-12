<template>
  <AppLayout>
    <Head title="Cari Laporan "/>
    <div class="search-container">
      
      <h2 class="mb-4">Pencarian Laporan Penipuan</h2>

      <!-- Filter and search section -->
      <div class="filter-bar">
        <div class="row g-3 align-items-center">
          <div class="col-lg-6">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" aria-hidden="true">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                type="text"
                class="form-control border-start-0"
                placeholder="Cari nomor telepon, email, atau website..."
                v-model.trim="searchQuery"
                @keyup.enter="searchReports"
                @input="debouncedSearch"
                aria-label="Pencarian laporan"
              >
              <button
                class="btn btn-primary"
                @click="searchReports"
                :disabled="loading"
                aria-label="Cari laporan"
              >
                <i class="fas fa-search me-1 d-none d-sm-inline" aria-hidden="true"></i>
                Cari
              </button>
            </div>
            <div v-if="searchError" class="text-danger mt-1 small" role="alert">
              {{ searchError }}
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row g-2">
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="typeFilter"
                  aria-label="Filter jenis laporan"
                >
                  <option value="all">Semua Jenis</option>
                  <option value="Telepon">Telepon</option>
                  <option value="Email">Email</option>
                  <option value="Website">Website</option>
                  <option value="WhatsApp">WhatsApp</option>
                </select>
              </div>
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="statusFilter"
                  aria-label="Filter status laporan"
                >
                  <option value="all">Semua Status</option>
                  <option value="process">Diproses</option>
                  <option value="completed">Bersih</option>
                  <option value="rejected">Berbahaya</option>
                </select>
              </div>
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="sortOrder"
                  aria-label="Urutkan laporan"
                >
                  <option value="newest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="most_reports">Terbanyak Dilaporkan</option>
                  <option value="highest_rating">Rating Tertinggi</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="active-filters" v-if="hasActiveFilters">
            <span class="me-2">Filter aktif:</span>
            <span 
              v-if="searchQuery" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              "{{ searchQuery }}"
              <button @click="clearSearch" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter pencarian"></button>
            </span>
            <span 
              v-if="typeFilter !== 'all'" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              {{ typeFilter }}
              <button @click="typeFilter = 'all'" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter jenis"></button>
            </span>
            <span 
              v-if="statusFilter !== 'all'" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              {{ getFilterStatusLabel(statusFilter) }}
              <button @click="statusFilter = 'all'" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter status"></button>
            </span>
          </div>
          <div>
            <button
              class="btn btn-outline-secondary btn-sm"
              @click="resetFilters"
              aria-label="Reset filter"
              :disabled="!hasActiveFilters"
            >
              <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Results stats -->
      <div v-if="!loading && filteredReports.length > 0" class="results-stats mb-3">
        <div class="d-flex justify-content-between align-items-center">
          <p class="mb-0">
            Menampilkan {{ paginatedReports.length }} dari {{ filteredReports.length }} hasil
          </p>
          <div class="d-flex align-items-center">
            <label for="itemsPerPage" class="me-2 mb-0">Tampilkan:</label>
            <select
              id="itemsPerPage"
              class="form-select form-select-sm"
              v-model="itemsPerPage"
              style="width: auto"
              aria-label="Jumlah item per halaman"
            >
              <option value="6">6</option>
              <option value="12">12</option>
              <option value="24">24</option>
              <option value="48">48</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="row">
        <div v-for="n in 6" :key="n" class="col-md-6 col-lg-4">
          <div class="card skeleton-card">
            <div class="skeleton-type-indicator"></div>
            <div class="card-header skeleton-header"></div>
            <div class="card-body">
              <div class="skeleton-title"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-footer"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- No results message -->
      <div v-if="!loading && filteredReports.length === 0" class="no-results">
        <div class="text-center">
          <i class="fas fa-search fa-3x text-muted mb-3" aria-hidden="true"></i>
          <h5>Tidak ada hasil ditemukan</h5>
          <p class="text-muted">
            Coba gunakan kata kunci yang berbeda atau reset filter pencarian.
          </p>
          <button
            class="btn btn-outline-primary mt-2"
            @click="resetFilters"
            aria-label="Reset semua filter"
          >
            <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Results grid -->
      <transition-group name="reports-fade" tag="div" class="row">
        <div
          v-for="(report, index) in paginatedReports"
          :key="report.id || (report.contact + index)"
          class="col-md-6 col-lg-4"
        >
          <div 
            class="card report-card"
            :class="{ 'report-card--danger': report.status === 'danger' }"
          >
            <div :class="getTypeClass(report.type)" class="report-card__type-indicator"></div>
            <div class="card-header report-card__header">
              <div class="report-card__type">
                <i :class="getTypeIcon(report.type)" class="me-2" aria-hidden="true"></i>
                <span>{{ report.type }}</span>
              </div>
              <span
                :class="getStatusClass(report.status)"
                class="report-card__status-badge"
              >
                {{ getStatusLabel(report.status) }}
              </span>
            </div>
            <div class="card-body report-card__body">
              <h5 class="card-title report-card__title">{{ report.contact }}</h5>
              <p class="card-text report-card__description">{{ truncateText(report.description, 100) }}</p>

              <div class="report-card__rating mb-3">
                <div class="report-card__rating-stars" aria-label="Rating: {{ report.rating }} dari 5">
                  <i
                    v-for="i in 5"
                    :key="'star-' + i"
                    :class="getStarClass(report.rating, i)"
                    aria-hidden="true"
                  ></i>
                </div>
                <span class="report-card__report-count">
                  <i class="fas fa-flag me-1" aria-hidden="true"></i>
                  {{ report.reportCount }} Laporan
                </span>
              </div>

              <div class="report-card__footer">
                <small class="text-muted">
                  <i class="far fa-calendar-alt me-1" aria-hidden="true"></i>
                  {{ report.date }}
                </small>
                <button
                  class="btn btn-sm btn-outline-primary report-card__detail-btn"
                  @click="handleShowDetails(report)"
                  aria-label="Lihat detail laporan"
                >
                  <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                  Detail
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition-group>

      <!-- Pagination -->
      <nav v-if="totalPages > 1" aria-label="Navigasi halaman laporan">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button 
              class="page-link" 
              @click="changePage(currentPage - 1)" 
              :disabled="currentPage === 1"
              aria-label="Halaman sebelumnya"
            >
              <i class="fas fa-chevron-left" aria-hidden="true"></i>
              <span class="d-none d-sm-inline ms-1">Sebelumnya</span>
            </button>
          </li>
          
          <li v-if="showStartEllipsis" class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          
          <li 
            v-for="page in visiblePageNumbers" 
            :key="page" 
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button 
              class="page-link" 
              @click="changePage(page)"
              :aria-current="page === currentPage ? 'page' : undefined"
            >
              {{ page }}
            </button>
          </li>
          
          <li v-if="showEndEllipsis" class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button 
              class="page-link" 
              @click="changePage(currentPage + 1)" 
              :disabled="currentPage === totalPages"
              aria-label="Halaman selanjutnya"
            >
              <span class="d-none d-sm-inline me-1">Selanjutnya</span>
              <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
          </li>
        </ul>
      </nav>

      <!-- Report detail modal -->
      <div
        class="modal fade"
        id="reportDetailModal"
        tabindex="-1"
        aria-labelledby="reportDetailModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content" v-if="selectedReport">
            <div 
              class="modal-header"
              :class="{'bg-danger text-white': selectedReport.status === 'danger'}"
            >
              <h5 class="modal-title" id="reportDetailModalLabel">
                <i :class="getTypeIcon(selectedReport.type)" class="me-2" aria-hidden="true"></i>
                {{ selectedReport.contact }}
              </h5>
              <button
                type="button"
                class="btn-close"
                :class="{'btn-close-white': selectedReport.status === 'danger'}"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <div class="mb-3 p-2 rounded" :class="getStatusBackgroundClass(selectedReport.status)">
                <div class="d-flex align-items-center">
                  <i class="fas fa-exclamation-triangle me-2" v-if="selectedReport.status === 'danger'" aria-hidden="true"></i>
                  <i class="fas fa-exclamation-circle me-2" v-if="selectedReport.status === 'warning'" aria-hidden="true"></i>
                  <i class="fas fa-check-circle me-2" v-if="selectedReport.status === 'safe'" aria-hidden="true"></i>
                  <strong>{{ getStatusLabel(selectedReport.status) }}</strong>
                </div>
              </div>
              
              <div class="report-detail__item">
                <strong>Jenis:</strong> {{ selectedReport.type }}
              </div>
              <div class="report-detail__item">
                <strong>Kontak:</strong> {{ selectedReport.contact }}
              </div>
              <div class="report-detail__item">
                <strong>Deskripsi:</strong> {{ selectedReport.description }}
              </div>
              <div class="report-detail__item">
                <strong>Rating Bahaya:</strong>
                <div class="d-inline-block">
                  <i
                    v-for="i in 5"
                    :key="'modal-star-' + i"
                    :class="getStarClass(selectedReport.rating, i)"
                    aria-hidden="true"
                  ></i>
                  <span class="ms-2">({{ selectedReport.rating }} / 5)</span>
                </div>
              </div>
              <div class="report-detail__item">
                <strong>Jumlah Laporan:</strong> {{ selectedReport.reportCount }}
              </div>
              <div class="report-detail__item">
                <strong>Dilaporkan pada:</strong> {{ selectedReport.date }}
              </div>
              
              <div class="alert alert-warning mt-4" v-if="selectedReport.status === 'danger' || selectedReport.status === 'warning'">
                <i class="fas fa-shield-alt me-2" aria-hidden="true"></i>
                <strong>Tindakan yang disarankan:</strong>
                <ul class="mb-0 mt-2">
                  <li v-if="selectedReport.type === 'Telepon' || selectedReport.type === 'WhatsApp'">Jangan menerima panggilan dari nomor ini</li>
                  <li v-if="selectedReport.type === 'Email'">Jangan membalas atau mengklik tautan pada email ini</li>
                  <li v-if="selectedReport.type === 'Website'">Jangan mengunjungi atau memasukkan data pada website ini</li>
                  <li>Laporkan ke pihak berwenang jika Anda telah menjadi korban</li>
                </ul>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Tutup
              </button>
              <button
                type="button" 
                class="btn btn-primary"
                @click="handleReportContactClick"
              >
                <i class="fas fa-flag me-1" aria-hidden="true"></i>
                Laporkan Kontak Ini
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Report form modal -->
      <div
        class="modal fade"
        id="reportFormModal"
        tabindex="-1"
        aria-labelledby="reportFormModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="reportFormModalLabel">
                <i class="fas fa-flag me-2" aria-hidden="true"></i>
                Laporkan Kontak
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="submitReport">
                <div class="mb-3">
                  <label Sty for="reportContact" class="form-label">Kontak yang Dilaporkan</label>
                  <input
                    type="text"
                    class="form-control"
                    id="reportContact"
                    v-model="reportForm.contact"
                    readonly
                  />
                </div>
                
                <div class="mb-3">
                  <label for="reportType" class="form-label">Jenis Kontak</label>
                  <input
                    type="text"
                    class="form-control"
                    id="reportType"
                    v-model="reportForm.type"
                    readonly
                  />
                </div>
                
                <div class="mb-3">
                  <label for="reportDescription" class="form-label">Deskripsi Kejadian</label>
                  <textarea
                    class="form-control"
                    id="reportDescription"
                    rows="4"
                    v-model="reportForm.description"
                    placeholder="Jelaskan bagaimana Anda mengalami penipuan dari kontak ini..."
                    required
                  ></textarea>
                </div>
                
                <div class="mb-3">
                  <label class="form-label d-block">Tingkat Bahaya</label>
                  <div class="rating-input">
                    <i
                      v-for="i in 5"
                      :key="'rating-' + i"
                      :class="['fas', i <= reportForm.rating ? 'fa-star' : 'fa-star-o']"
                      @click="reportForm.rating = i"
                      style="cursor: pointer;"
                      aria-hidden="true"
                    ></i>
                    <span class="ms-2">{{ reportForm.rating || 0 }} / 5</span>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="reporterName" class="form-label">Nama Anda (opsional)</label>
                  <input
                    type="text"
                    class="form-control"
                    id="reporterName"
                    v-model="reportForm.reporterName"
                    placeholder="Nama Anda"
                  />
                </div>
                
                <div class="mb-3">
                  <label for="reporterEmail" class="form-label">Email Anda (opsional)</label>
                  <input
                    type="email"
                    class="form-control"
                    id="reporterEmail"
                    v-model="reportForm.reporterEmail"
                    placeholder="email@contoh.com"
                  />
                  <div class="form-text">Kami tidak akan membagikan email Anda kepada pihak lain.</div>
                </div>
                
                <div class="form-check mb-3">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="confirmCheck"
                    v-model="reportForm.confirmed"
                    required
                  />
                  <label class="form-check-label" for="confirmCheck">
                    Saya mengonfirmasi bahwa laporan ini akurat dan benar
                  </label>
                </div>
                
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary" :disabled="!reportForm.confirmed">
                    <i class="fas fa-paper-plane me-1" aria-hidden="true"></i>
                    Kirim Laporan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Success toast -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div 
          id="successToast" 
          class="toast" 
          role="alert" 
          aria-live="assertive" 
          aria-atomic="true"
          data-bs-delay="3000"
        >
          <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
            <strong class="me-auto">Berhasil</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            Laporan berhasil dikirim. Terima kasih atas kontribusi Anda!
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { debounce } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Modal, Toast } from 'bootstrap';
import { usePage } from '@inertiajs/vue3'

// Constants
const STATUS_LABELS = {
  danger: 'Sangat Berbahaya',
  warning: 'Berbahaya',
  safe: 'Bersih',
};

const STATUS_FILTER_MAP = {
  process: 'warning',
  completed: 'safe',
  rejected: 'danger',
};

const FILTER_STATUS_LABELS = {
  process: 'Diproses',
  completed: 'Bersih',
  rejected: 'Berbahaya',
};

const TYPE_CLASSES = {
  Telepon: 'report-card__type-indicator--phone',
  Email: 'report-card__type-indicator--email',
  Website: 'report-card__type-indicator--web',
  WhatsApp: 'report-card__type-indicator--whatsapp',
};

const TYPE_ICONS = {
  Telepon: 'fas fa-phone text-danger',
  Email: 'fas fa-envelope text-primary',
  Website: 'fas fa-globe text-warning',
  WhatsApp: 'fab fa-whatsapp text-success',
};

const STATUS_CLASSES = {
  danger: 'report-card__status-badge--danger',
  warning: 'report-card__status-badge--warning',
  safe: 'report-card__status-badge--safe',
};

const STATUS_BG_CLASSES = {
  danger: 'bg-danger-subtle text-danger',
  warning: 'bg-warning-subtle text-warning-emphasis',
  safe: 'bg-success-subtle text-success',
};

// Utilities
function parseReportDate(dateStr) {
  const parts = dateStr.split(' ');
  const months = {
    'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'Mei': 4, 'Jun': 5,
    'Jul': 6, 'Agu': 7, 'Sep': 8, 'Okt': 9, 'Nov': 10, 'Des': 11
  };
  
  return new Date(parts[2], months[parts[1]], parts[0]);
}

export default {
  name: 'FraudReportSearch',
  components: {
    AppLayout
  },
  data() {
    return {
      feedbacks: [],
      searchQuery: '',
      statusFilter: 'all',
      typeFilter: 'all',
      sortOrder: 'newest',
      reports: [],
      loading: false,
      searchError: '',
      selectedReport: null,
      itemsPerPage: 6,
      currentPage: 1,
      reportForm: {
        contact: '',
        type: '',
        description: '',
        rating: 0,
        reporterName: '',
        reporterEmail: '',
        confirmed: false
      },
      sampleReports: [
        {
          id: 1,
          type: 'Telepon',
          contact: '0812-3456-7890',
          description:
            'Penipuan berkedok Giveaway dengan meminta kode OTP dan informasi kartu kredit. Pelaku mengaku sebagai perwakilan dari sebuah bank terkenal dan menjanjikan hadiah menarik jika memberikan kode OTP.',
          rating: 5,
          reportCount: 87,
          date: '16 Apr 2025',
          status: 'danger',
        },
        {
          id: 2,
          type: 'Email',
          contact: 'bank-notification@gmail.id',
          description:
            'Email phishing yang mengaku dari bank dan meminta pembaruan data nasabah karena keamanan. Email berisi tautan palsu yang mengarah ke situs penipuan untuk mencuri data perbankan.',
          rating: 4.5,
          reportCount: 62,
          date: '15 Apr 2025',
          status: 'warning',
        },
        {
          id: 3,
          type: 'Website',
          contact: 'www.investasi-cepat.com',
          description:
            'Situs investasi palsu dengan janji keuntungan hingga 20% per minggu. Banyak korban telah melaporkan bahwa setelah melakukan investasi, website tidak bisa diakses dan kontak admin menghilang.',
          rating: 4,
          reportCount: 43,
          date: '14 Apr 2025',
          status: 'warning',
        },
        {
          id: 4,
          type: 'WhatsApp',
          contact: '+62 877-5432-1098',
          description:
            'Nomor WhatsApp ini tercatat sebagai nomor yang valid dan tidak memiliki laporan negatif. Nomor ini digunakan oleh layanan pelanggan resmi dari perusahaan telekomunikasi.',
          rating: 0,
          reportCount: 0,
          date: '14 Apr 2025',
          status: 'safe',
        },
        {
          id: 5,
          type: 'Telepon',
          contact: '0878-3333-4444',
          description:
            'Penipuan berkedok customer service bank, meminta data pribadi dan PIN. Pelaku menelepon mengatasnamakan bank dan mengatakan ada masalah dengan rekening yang harus segera diselesaikan.',
          rating: 4.8,
          reportCount: 56,
          date: '13 Apr 2025',
          status: 'danger',
        },
        {
          id: 6,
          type: 'Email',
          contact: 'info-pajak@kemenkeuan.co.id',
          description:
            'Email phishing yang mengaku dari Kemenkeu, meminta bayar pajak dengan link palsu. Perhatikan domain email yang salah ketik (seharusnya kemenkeu.go.id).',
          rating: 4.7,
          reportCount: 38,
          date: '12 Apr 2025',
          status: 'danger',
        },
        {
          id: 7,
          type: 'Website',
          contact: 'www.diskon-belanja-online.com',
          description:
            'Website belanja palsu dengan diskon besar namun barang tidak pernah dikirim. Banyak pembeli melaporkan setelah transfer pembayaran, barang tidak pernah sampai dan customer service tidak bisa dihubungi.',
          rating: 4.3,
          reportCount: 27,
          date: '10 Apr 2025',
          status: 'warning',
        },
        {
          id: 8,
          type: 'WhatsApp',
          contact: '+62 855-6789-1234',
          description:
            'Penipuan berkedok undian berhadiah dari perusahaan e-commerce terkenal. Pelaku mengirimkan pesan WhatsApp bahwa penerima telah memenangkan hadiah dan diminta untuk mentransfer biaya administrasi.',
          rating: 4.5,
          reportCount: 41,
          date: '9 Apr 2025',
          status: 'danger',
        },
        {
          id: 9,
          type: 'Telepon',
          contact: '0898-7777-6666',
          description:
            'Penipuan berkedok petugas BPJS yang meminta verifikasi data dan transfer biaya pembaruan kartu. Pelaku menghubungi dengan nomor pribadi dan mengancam akan memblokir layanan BPJS jika tidak segera diproses.',
          rating: 4.6,
          reportCount: 33,
          date: '8 Apr 2025',
          status: 'danger',
        },
        {
          id: 10,
          type: 'Email',
          contact: 'cs-tokopedia@gmail.com',
          description:
            'Email phishing yang mengaku dari Tokopedia dan meminta konfirmasi akun dengan memasukkan password. Pesan berisi ancaman bahwa akun akan diblokir jika tidak segera dikonfirmasi.',
          rating: 4.2,
          reportCount: 29,
          date: '7 Apr 2025',
          status: 'warning',
        },
        {
          id: 11,
          type: 'Website',
          contact: 'www.pinjaman-cepat-24jam.com',
          description:
            'Website pinjaman online tidak resmi yang menawarkan pinjaman cepat tanpa syarat namun dengan bunga sangat tinggi. Banyak korban melaporkan adanya intimidasi dan ancaman jika terlambat membayar.',
          rating: 4.9,
          reportCount: 67,
          date: '6 Apr 2025',
          status: 'danger',
        },
        {
          id: 12,
          type: 'Telepon',
          contact: '0811-2222-3333',
          description:
            'Nomor telepon yang digunakan untuk penipuan investasi saham. Pelaku menawarkan saham dengan keuntungan tidak masuk akal dan meminta transfer dana melalui rekening pribadi.',
          rating: 4.4,
          reportCount: 19,
          date: '5 Apr 2025',
          status: 'warning',
        },
        {
          id: 13,
          type: 'WhatsApp',
          contact: '+62 813-9876-5432',
          description:
            'Penipuan berkedok bantuan sosial yang meminta data pribadi dan biaya administrasi untuk menerima dana bantuan. Pelaku mengaku dari lembaga sosial terpercaya.',
          rating: 4.7,
          reportCount: 35,
          date: '4 Apr 2025',
          status: 'danger',
        }
      ]
    };
  },
  computed: {
    filteredReports() {
      let filtered = [...this.sampleReports];

      // Apply search query filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(report =>
          report.contact.toLowerCase().includes(query) ||
          report.description.toLowerCase().includes(query)
        );
      }

      // Apply type filter
      if (this.typeFilter !== 'all') {
        filtered = filtered.filter(report => report.type === this.typeFilter);
      }

      // Apply status filter
      if (this.statusFilter !== 'all') {
        const status = STATUS_FILTER_MAP[this.statusFilter];
        filtered = filtered.filter(report => report.status === status);
      }

      // Apply sorting
      switch (this.sortOrder) {
        case 'newest':
          filtered.sort((a, b) => parseReportDate(b.date) - parseReportDate(a.date));
          break;
        case 'oldest':
          filtered.sort((a, b) => parseReportDate(a.date) - parseReportDate(b.date));
          break;
        case 'most_reports':
          filtered.sort((a, b) => b.reportCount - a.reportCount);
          break;
        case 'highest_rating':
          filtered.sort((a, b) => b.rating - a.rating);
          break;
      }

      return filtered;
    },
    paginatedReports() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredReports.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredReports.length / this.itemsPerPage);
    },
    visiblePageNumbers() {
      const maxPages = 5;
      const pages = [];
      let startPage = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
      let endPage = Math.min(this.totalPages, startPage + maxPages - 1);

      if (endPage - startPage + 1 < maxPages) {
        startPage = Math.max(1, endPage - maxPages + 1);
      }

      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      return pages;
    },
    showStartEllipsis() {
      return this.visiblePageNumbers[0] > 1;
    },
    showEndEllipsis() {
      return this.visiblePageNumbers[this.visiblePageNumbers.length - 1] < this.totalPages;
    },
    hasActiveFilters() {
      return this.searchQuery || this.typeFilter !== 'all' || this.statusFilter !== 'all';
    }
  },
  methods: {
    searchReports() {
      if (this.searchQuery.length < 3 && this.searchQuery.length > 0) {
        this.searchError = 'Kata kunci pencarian minimal 3 karakter';
        return;
      }
      this.searchError = '';
      this.currentPage = 1;
      this.loading = true;
      setTimeout(() => {
        this.reports = this.sampleReports;
        this.loading = false;
      }, 500);
    },
    debouncedSearch: debounce(function () {
      this.searchReports();
    }, 300),
    clearSearch() {
      this.searchQuery = '';
      this.searchError = '';
      this.currentPage = 1;
      this.searchReports();
    },
    resetFilters() {
      this.searchQuery = '';
      this.typeFilter = 'all';
      this.statusFilter = 'all';
      this.sortOrder = 'newest';
      this.currentPage = 1;
      this.searchError = '';
      this.searchReports();
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    handleShowDetails(report) {
      this.selectedReport = report;
      const modal = new Modal(document.getElementById('reportDetailModal'));
      modal.show();
    },
    handleReportContactClick() {
      const detailModal = Modal.getInstance(document.getElementById('reportDetailModal'));
      detailModal.hide();

      this.reportForm.contact = this.selectedReport.contact;
      this.reportForm.type = this.selectedReport.type;
      const reportModal = new Modal(document.getElementById('reportFormModal'));
      reportModal.show();
    },
    submitReport() {
      if (!this.reportForm.confirmed) return;

      const reportModal = Modal.getInstance(document.getElementById('reportFormModal'));
      reportModal.hide();

      const toast = new Toast(document.getElementById('successToast'));
      toast.show();

      this.reportForm = {
        contact: '',
        type: '',
        description: '',
        rating: 0,
        reporterName: '',
        reporterEmail: '',
        confirmed: false
      };
    },
    getTypeClass(type) {
      return TYPE_CLASSES[type] || '';
    },
    getTypeIcon(type) {
      return TYPE_ICONS[type] || 'fas fa-question';
    },
    getStatusClass(status) {
      return STATUS_CLASSES[status] || '';
    },
    getStatusLabel(status) {
      return STATUS_LABELS[status] || 'Tidak Diketahui';
    },
    getStatusBackgroundClass(status) {
      return STATUS_BG_CLASSES[status] || '';
    },
    getFilterStatusLabel(status) {
      return FILTER_STATUS_LABELS[status] || 'Semua';
    },
    getStarClass(rating, index) {
      if (rating >= index) {
        return 'fas fa-star text-warning';
      } else if (rating >= index - 0.5) {
        return 'fas fa-star-half-alt text-warning';
      }
      return 'far fa-star text-warning';
    },
    truncateText(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    },
    fetchFeedbacks() {
      const page = usePage();
      this.feedbacks = page.props.feedbacks;
    },
  },
  mounted() {
    this.searchReports();
    this.fetchFeedbacks();
  }
};
</script>

<style scoped>
.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.filter-bar {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.active-filter .btn-close-sm {
  font-size: 0.6rem;
  vertical-align: middle;
}

.results-stats {
  font-size: 0.9rem;
  color: #6c757d;
}

.skeleton-card {
  height: 200px;
  background: #f8f9fa;
  animation: pulse 1.5s infinite;
}

.skeleton-type-indicator,
.skeleton-header,
.skeleton-title,
.skeleton-text,
.skeleton-footer {
  background: #e9ecef;
  border-radius: 4px;
  margin-bottom: 10px;
}

.skeleton-type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
}

.skeleton-header {
  height: 20px;
  width: 60%;
}

.skeleton-title {
  height: 24px;
  width: 80%;
}

.skeleton-text {
  height: 16px;
  width: 100%;
}

.skeleton-footer {
  height: 20px;
  width: 40%;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

.no-results {
  text-align: center;
  padding: 40px 0;
}

.report-card {
  position: relative;
  margin-bottom: 20px;
  transition: transform 0.2s;
}

.report-card:hover {
  transform: translateY(-5px);
}

.report-card--danger {
  border-color: #dc3545;
}

.report-card__type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
}

.report-card__type-indicator--phone {
  background: #dc3545;
}

.report-card__type-indicator--email {
  background: #007bff;
}

.report-card__type-indicator--web {
  background: #ffc107;
}

.report-card__type-indicator--whatsapp {
  background: #28a745;
}

.report-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.report-card__status-badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
}

.report-card__status-badge--danger {
  background: #dc3545;
  color: white;
}

.report-card__status-badge--warning {
  background: #ffc107;
  color: #212529;
}

.report-card__status-badge--safe {
  background: #28a745;
  color: white;
}

.report-card__rating-stars i {
  font-size: 0.9rem;
}

.report-card__report-count {
  font-size: 0.85rem;
  color: #6c757d;
}

.report-card__footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.reports-fade-enter-active,
.reports-fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.reports-fade-enter-from,
.reports-fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

.pagination .page-link {
  cursor: pointer;
}

.report-detail__item {
  margin-bottom: 10px;
}
</style>