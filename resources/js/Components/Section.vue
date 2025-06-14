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
            <div v-if="showSearch && showReportCheck" class="row justify-content-center mt-5 pt-4">
                <div class="col-lg-8 col-md-10 text-center">
                    <h2 class="fw-bold fs-2 mb-4">Sudah Ada yang Melaporkan? Cek di Sini!</h2>
                    <div class="search-container mt-3 mb-4">
                        <div class="d-flex flex-column align-items-center">
                            <input 
                                type="text" 
                                class="form-control py-2 rounded-pill mb-3" 
                                placeholder="Nomor/Email" 
                                v-model.lazy="searchQuery"
                                @keydown.enter="navigateToSearch"
                                aria-label="Search for reports"
                            />
                            <button 
                                type="button"
                                class="btn btn-primary px-4 py-2 mt-2"
                                @click="navigateToSearch"
                            >
                                Telusuri
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- report Statistics Section -->
            <div v-if="showReportStats" class="statistics-triangular mt-5 pt-4" ref="statsSection">
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
                                <div class="stat-item text-center" :class="{ 'animate-stat': animateStats }">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
                                                <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0 counter-number">{{ displayVerifiedReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Laporan yang telah diverifikasi</p>
                                </div>
                            </div>
                            
                            <!-- Bottom Stats (Side by Side) -->
                            <div class="stat-row justify-content-between mt-4">
                                <!-- Bottom Left Stat -->
                                <div class="stat-item text-center" :class="{ 'animate-stat': animateStats }">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clipboard-data" viewBox="0 0 16 16">
                                                <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V9z"/>
                                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0 counter-number">{{ displayTotalReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Total laporan yang diterima hingga saat ini</p>
                                </div>
                                
                                <!-- Bottom Right Stat -->
                                <div class="stat-item text-center" :class="{ 'animate-stat': animateStats }">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0 counter-number">{{ displayFraudReports }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Pelaporan penipuan yang dilakukan oleh pengguna</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Statistics Section -->
            <div v-if="showVerificationStats" class="statistics-triangular mt-5 pt-4" ref="statsSectionTwo">
                <div class="row">
                    <!-- Left Side: Mission Statement -->
                    <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                        <div class="mission-statement mt-4">
                            <h2 class="fw-bold fs-3 mt-3 text-center text-lg-start">Solusi verifikasi data untuk keputusan yang lebih cerdas</h2>
                            <p class="text-dark fs-6 text-center text-lg-start">
                                Memberikan akses cepat dan akurat untuk memvalidasi informasi penting melalui sistem yang terintegrasi dan terpercaya.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-12">
                        <div class="stats-triangle">
                            <div class="stat-row justify-content-between">
                                <!-- Left Stat -->
                                <div class="stat-item text-center" :class="{ 'animate-stat': animateStatsTwo }">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clipboard-check" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0 counter-number">{{ displayTotalReportsTwo }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Total data yang telah diverifikasi</p>
                                </div>
                                
                                <!-- Right Stat -->
                                <div class="stat-item text-center" :class="{ 'animate-stat': animateStatsTwo }">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="stat-icon text-primary me-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                            </svg>
                                        </div>
                                        <h3 class="fw-bold fs-2 mb-0 counter-number">{{ displayFraudReportsTwo }}</h3>
                                    </div>
                                    <p class="text-muted mt-2">Pengguna yang telah menggunakan layanan verifikasi</p>
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
    name: 'AboutSection',
    props: {
        showSearch: {
            type: Boolean,
            default: true
        },
        showReportCheck: {
            type: Boolean,
            default: true
        },
        showReportStats: {
            type: Boolean,
            default: true
        },
        showVerificationStats: {
            type: Boolean,
            default: false
        },
        verifiedReports: {
            type: Number,
            default: 0
        },
        totalReports: {
            type: Number,
            default: 0
        },
        fraudReports: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            searchQuery: '',
            displayVerifiedReports: 0,
            displayTotalReports: 0,
            displayFraudReports: 0,
            // Data terpisah untuk verification stats
            displayTotalReportsTwo: 0,
            displayFraudReportsTwo: 0,
            animateStats: false,
            animateStatsTwo: false,
            observer: null,
            observerTwo: null,
            hasAnimated: false,
            hasAnimatedTwo: false,
            animationFrameId: null
        }
    },
    computed: {
        encodedSearchQuery() {
            return encodeURIComponent(this.searchQuery.trim());
        }
    },
    mounted() {
        this.setupIntersectionObserver();
    },
    beforeUnmount() {
        this.cleanup();
    },
    methods: {
        navigateToSearch() {
            if (this.searchQuery.trim()) {
                window.location.href = `/CariLaporan?query=${this.encodedSearchQuery}`;
            }
        },

        setupIntersectionObserver() {
            const options = {
                root: null,
                rootMargin: '50px',
                threshold: 0.2
            };

            // Observer untuk report stats
            if (this.showReportStats) {
                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.hasAnimated) {
                            this.debounceAnimation();
                        }
                    });
                }, options);
            }

            // Observer untuk verification stats
            if (this.showVerificationStats) {
                this.observerTwo = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.hasAnimatedTwo) {
                            this.debounceAnimationTwo();
                        }
                    });
                }, options);
            }

            this.$nextTick(() => {
                if (this.$refs.statsSection && this.observer) {
                    this.observer.observe(this.$refs.statsSection);
                }
                if (this.$refs.statsSectionTwo && this.observerTwo) {
                    this.observerTwo.observe(this.$refs.statsSectionTwo);
                }
            });
        },

        debounceAnimation() {
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
            }
            
            this.animationFrameId = requestAnimationFrame(() => {
                this.startCountingAnimation();
            });
        },

        debounceAnimationTwo() {
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
            }
            
            this.animationFrameId = requestAnimationFrame(() => {
                this.startCountingAnimationTwo();
            });
        },

        startCountingAnimation() {
            if (this.hasAnimated) return;
            
            this.hasAnimated = true;
            this.animateStats = true;

            const delays = [0, 150, 300];
            const counters = [
                { prop: 'displayVerifiedReports', target: this.verifiedReports, duration: 1800 },
                { prop: 'displayTotalReports', target: this.totalReports, duration: 2000 },
                { prop: 'displayFraudReports', target: this.fraudReports, duration: 1900 }
            ];

            counters.forEach((counter, index) => {
                setTimeout(() => {
                    this.animateCounter(counter.prop, counter.target, counter.duration);
                }, delays[index]);
            });
        },

        startCountingAnimationTwo() {
            if (this.hasAnimatedTwo) return;
            
            this.hasAnimatedTwo = true;
            this.animateStatsTwo = true;

            const delays = [0, 150];
            const counters = [
                { prop: 'displayTotalReportsTwo', target: this.totalReports, duration: 2000 },
                { prop: 'displayFraudReportsTwo', target: this.fraudReports, duration: 1900 }
            ];

            counters.forEach((counter, index) => {
                setTimeout(() => {
                    this.animateCounter(counter.prop, counter.target, counter.duration);
                }, delays[index]);
            });
        },

        animateCounter(property, targetValue, duration) {
            if (targetValue === 0) {
                this[property] = 0;
                return;
            }

            const maxDuration = 3000;
            const minDuration = 1000;
            const adjustedDuration = Math.min(maxDuration, Math.max(minDuration, duration));
            
            const startTime = performance.now();
            const startValue = 0;
            const difference = targetValue - startValue;

            const updateCounter = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / adjustedDuration, 1);
                const easedProgress = this.easeOutCubic(progress);
                const currentValue = Math.floor(startValue + (difference * easedProgress));
                
                this[property] = this.formatNumber(currentValue, targetValue, progress === 1);

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    this[property] = this.formatNumber(targetValue, targetValue, true);
                }
            };

            requestAnimationFrame(updateCounter);
        },

        formatNumber(value, finalValue, isFinal) {
            // Special formatting for verification stats section
            if (this.showVerificationStats) {
                if (value >= 1000000) {
                    return (value / 1000000).toFixed(1) + 'M';
                } else if (value >= 1000) {
                    return (value / 1000).toFixed(0) + 'K';
                }
                return value.toString();
            }
            
            // Original formatting for other sections
            if (isFinal) {
                return finalValue.toLocaleString('id-ID');
            }
            
            if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 10000) {
                return (value / 1000).toFixed(0) + 'K';
            } else {
                return value.toLocaleString('id-ID');
            }
        },

        easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        },

        cleanup() {
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
            
            if (this.observerTwo) {
                this.observerTwo.disconnect();
                this.observerTwo = null;
            }
            
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
                this.animationFrameId = null;
            }
        },

        resetAnimation() {
            this.cleanup();
            this.hasAnimated = false;
            this.hasAnimatedTwo = false;
            this.animateStats = false;
            this.animateStatsTwo = false;
            this.displayVerifiedReports = 0;
            this.displayTotalReports = 0;
            this.displayFraudReports = 0;
            this.displayTotalReportsTwo = 0;
            this.displayFraudReportsTwo = 0;
            
            this.$nextTick(() => {
                this.setupIntersectionObserver();
            });
        }
    },

    watch: {
        verifiedReports: {
            handler(newVal, oldVal) {
                if (newVal !== oldVal && (this.hasAnimated || this.hasAnimatedTwo)) {
                    this.resetAnimation();
                }
            },
            immediate: false
        },
        totalReports: {
            handler(newVal, oldVal) {
                if (newVal !== oldVal && (this.hasAnimated || this.hasAnimatedTwo)) {
                    this.resetAnimation();
                }
            },
            immediate: false
        },
        fraudReports: {
            handler(newVal, oldVal) {
                if (newVal !== oldVal && (this.hasAnimated || this.hasAnimatedTwo)) {
                    this.resetAnimation();
                }
            },
            immediate: false
        }
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
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
    transform: translateY(-1px);
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
    transition: border-color 0.2s ease;
}

.form-control:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

/* Optimized triangle layout */
.statistics-triangular {
    position: relative;
    will-change: transform; /* Hint browser untuk optimisasi */
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
    flex: 1;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, opacity;
}

/* Optimized animations dengan GPU acceleration */
.stat-item.animate-stat {
    animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.stat-item:nth-child(1) { animation-delay: 0.1s; }
.stat-item:nth-child(2) { animation-delay: 0.2s; }
.stat-item:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0) translateZ(0);
    }
}

/* Optimized counter styling */
.counter-number {
    transition: transform 0.2s ease;
    display: inline-block;
    will-change: transform;
}

.animate-stat .counter-number {
    animation: numberScale 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes numberScale {
    0% { transform: scale(1) translateZ(0); }
    50% { transform: scale(1.05) translateZ(0); }
    100% { transform: scale(1) translateZ(0); }
}

/* Optimized hover effects */
.stat-item:hover {
    transform: translateY(-3px) translateZ(0);
}

.stat-item:hover .counter-number {
    color: #0d6efd;
    transform: scale(1.02) translateZ(0);
}

.stat-item:hover .stat-icon {
    transform: scale(1.05) translateZ(0);
}

.stat-icon {
    color: #0d6efd;
    transition: transform 0.2s ease;
    will-change: transform;
}

/* Reduce motion untuk accessibility */
@media (prefers-reduced-motion: reduce) {
    .stat-item,
    .counter-number,
    .stat-icon,
    .btn-primary {
        transition: none;
        animation: none;
    }
    
    .stat-item.animate-stat {
        opacity: 1;
        transform: none;
    }
}

/* Optimized responsive design */
@media (max-width: 992px) {
    .stat-row.justify-content-between {
        flex-direction: column;
    }
    
    .stat-item {
        margin-bottom: 1rem;
    }
    
    h2.fs-2 { font-size: 1.75rem !important; }
    .fs-5 { font-size: 1.1rem !important; }
}

@media (max-width: 768px) {
    .stat-row { flex-direction: column; }
    .stat-item { margin-bottom: 1.5rem; }
    .quote-box { padding: 1.5rem !important; }
    h2.fs-2 { font-size: 1.5rem !important; }
    .fs-5 { font-size: 1rem !important; }
    .mission-statement { margin-bottom: 2rem; }
}

@media (max-width: 576px) {
    .search-container .form-control { font-size: 0.9rem; }
    .search-container .btn { width: 120px; font-size: 0.9rem; }
    h3.fs-2 { font-size: 1.75rem !important; }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-item { padding: 1rem; }
    .text-muted { font-size: 0.85rem; }
}

/* Content visibility untuk better loading */
.statistics-triangular {
    content-visibility: auto;
    contain-intrinsic-size: 400px;
}
</style>