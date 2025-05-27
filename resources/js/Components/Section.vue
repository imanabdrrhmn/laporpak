<template>
    <section class="about-section py-5 mt-md-5 mt-3">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Description -->
                <div class="col-lg-6 col-md-12">
                    <h2 class="fw-bold fs-2 mb-4 text-center text-lg-start">Apa itu LaporPak?</h2>
                    <p class="text-dark fs-5 mb-4 text-center text-lg-start">
                        LaporPak adalah platform digital yang mendorong partisipasi masyarakat dalam melaporkan masalah infrastruktur, layanan publik, dan penipuan melalui nomor HP. Suara masyarakat diubah menjadi data berharga untuk mendukung kinerja pemerintah dan perusahaan.
                    </p>
                    <div class="text-center text-lg-start">
                        <a href="/tentang-kami" class="btn btn-primary px-4 py-2 mt-2">Selengkapnya</a>
                    </div>
                </div>

                <!-- mission statement -->
                <div class="col-lg-6 col-md-12">
                    <div class="quote-box p-4 p-md-5 rounded-4">
                        <blockquote class="mb-0">
                            <p class="fs-5">
                                "LaporPak menjalankan misi mulia untuk memberantas penipuan yang terjadi di dunia maya, sekaligus mendorong partisipasi masyarakat dalam pelaporan masalah infrastruktur demi pembangunan yang lebih baik."
                            </p>
                        </blockquote>
                    </div>
                </div>
            </div>
            
            <!-- Report Check Form -->
            <div v-if="showSearch" class="row justify-content-center mt-5 pt-4">
                <div class="col-lg-8 col-md-10 text-center">
                    <h2 class="fw-bold fs-2 mb-4">Sudah Ada yang Melaporkan? Cek di Sini!</h2>
                    <div class="search-container mt-3 mb-4">
                        <div class="d-flex flex-column align-items-center">
                            <input 
                                type="text" 
                                class="form-control py-2 rounded-pill mb-3" 
                                placeholder="Nomor/Email" 
                                v-model="searchQuery"
                                aria-label="Search for reports">
                            <a 
                                class="btn btn-primary px-4 py-2 mt-2" 
                                :href="`/CariLaporan?query=${encodeURIComponent(searchQuery)}`"
                            >
                                Telusuri
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section in Triangular Layout -->
            <div class="statistics-triangular mt-5 pt-4">
                <div class="row">
                    <!-- Left Side: Mission Statement -->
                    <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                        <div class="mission-statement mt-4">
                            <h2 class="fw-bold fs-3 mt-3 text-center text-lg-start">Menjadi jembatan pelaporan penipuan yang cepat dan akurat</h2>
                            <p class="text-dark fs-6 text-center text-lg-start">
                                Kami hadir di sini berkat dedikasi untuk menciptakan ruang yang aman dari penipuan
                            </p>
                        </div>
                    </div>
                    
                    <!-- Right Side: Statistics in Triangle Form -->
                    <div class="col-lg-6 col-md-12">
                        <div class="stats-triangle">
                            <!-- Top Stat -->
                            <div class="stat-row justify-content-center">
                                <div class="stat-item text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
                                                <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0">{{ verifiedReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Laporan yang telah diverifikasi</p>
                                </div>
                            </div>
                            
                            <!-- Bottom Stats (Side by Side) -->
                            <div class="stat-row justify-content-between mt-4">
                                <!-- Bottom Left Stat -->
                                <div class="stat-item text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clipboard-data" viewBox="0 0 16 16">
                                                <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0V9z"/>
                                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0">{{ totalReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Total laporan yang diterima hingga saat ini</p>
                                </div>
                                
                                <!-- Bottom Right Stat -->
                                <div class="stat-item text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0">{{ fraudReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Pelaporan penipuan yang dilakukan oleh pengguna</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'aboutSection',
    props: {
        showSearch: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            searchQuery: '',
            verifiedReports: 5000,
            totalReports: 3232,
            fraudReports: 5000
        }
    },
    methods: {
        checkReport() {
            // Here you would implement the logic to check if a report exists
        },
        // Method to update statistics from an API or other data source
        fetchStatistics() {
            // This would be replaced with an actual API call
            // For example:
            // axios.get('/api/statistics').then(response => {
            //     this.verifiedReports = response.data.verified;
            //     this.totalReports = response.data.total;
            //     this.fraudReports = response.data.fraud;
            // });
        }
    },
    mounted() {
        // Fetch statistics when component is mounted
        this.fetchStatistics();
    }
}
</script>

<style scoped>
.about-section {
    background-color: #ffffff;
}

.bg-light {
    background-color: #f8f9ff !important;
    border-radius: 12px;
}

blockquote {
    position: relative;
    font-style: normal;
    color: #333;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    box-shadow: 0px 2px 10px rgba(101, 192, 237, 0.5);
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.quote-box {
    background-color: #F0F6FF;
    border-radius: 16px;
    color: #333;
}

.search-container {
    max-width: 580px;
    margin: 0 auto;
}

.form-control {
    border-radius: 20px;
    border: 1px solid #ced4da;
}

.form-control:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

.stat-icon {
    color: #0d6efd;
}

/* Triangle Layout Styling */
.statistics-triangular {
    position: relative;
}

.stats-triangle {
    display: flex;
    flex-direction: column;
}

.stat-row {
    display: flex;
}

.stat-item {
    padding: 1.5rem;
    transition: all 0.3s ease;
    flex: 1;
}

.stat-item:hover {
    transform: translateY(-5px);
}

/* Button sizing in search container */
.search-container .btn {
    width: 150px;
}

/* Mission statement adjustment */
.mission-statement {
    padding-top: 10px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .stat-row.justify-content-between {
        flex-direction: column;
    }
    
    .stat-item {
        margin-bottom: 1.5rem;
    }
    
    h2.fs-2 {
        font-size: 1.75rem !important;
    }
    
    .fs-5 {
        font-size: 1.1rem !important;
    }
}

@media (max-width: 768px) {
    .stat-row {
        flex-direction: column;
    }
    
    .stat-item {
        margin-bottom: 2rem;
    }
    
    .quote-box {
        padding: 1.5rem !important;
    }
    
    h2.fs-2 {
        font-size: 1.5rem !important;
    }
    
    .fs-5 {
        font-size: 1rem !important;
    }
    
    .mission-statement {
        margin-bottom: 2rem;
    }
}

@media (max-width: 576px) {
    .search-container .form-control {
        font-size: 0.9rem;
    }
    
    .search-container .btn {
        width: 120px;
        font-size: 0.9rem;
    }
    
    h3.fs-2 {
        font-size: 1.75rem !important;
    }
    
    .stat-icon svg {
        width: 20px;
        height: 20px;
    }
    
    .stat-item {
        padding: 1rem;
    }
    
    .text-muted {
        font-size: 0.85rem;
    }
}
</style>