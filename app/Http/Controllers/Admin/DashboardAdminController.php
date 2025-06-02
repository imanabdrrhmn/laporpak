<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Pastikan DB facade di-import
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth; // Jika ada otorisasi
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ReportResource; // Untuk recentReports

class DashboardAdminController extends Controller
{
    /**
     * Menampilkan halaman shell untuk dashboard admin.
     * Data utama akan diambil via API.
     */
    public function index(Request $request)
    {
        // Anda bisa mengirim props awal yang statis atau konfigurasi ke Vue jika perlu
        // Misalnya, daftar opsi periode filter
        $availablePeriods = [
            ['value' => 'today', 'label' => 'Hari Ini'],
            ['value' => 'yesterday', 'label' => 'Kemarin'],
            ['value' => 'this_week', 'label' => 'Minggu Ini'],
            ['value' => 'last_week', 'label' => 'Minggu Lalu'],
            ['value' => 'this_month', 'label' => 'Bulan Ini'],
            ['value' => 'last_month', 'label' => 'Bulan Lalu'],
            ['value' => 'this_year', 'label' => 'Tahun Ini'],
            ['value' => 'last_year', 'label' => 'Tahun Lalu'],
            ['value' => 'all_time', 'label' => 'Semua Waktu'],
            ['value' => 'custom', 'label' => 'Kustom Rentang'],
        ];

        return inertia('Admin/Dashboard/DashboardAdmin', [
            'availablePeriods' => $availablePeriods,
        ]);
    }

    /**
     * Menyediakan data dashboard sebagai API endpoint.
     * Menerima parameter filter periode.
     */
    public function getDashboardData(Request $request)
    {
        // Otorisasi jika perlu (misalnya, pastikan user adalah admin/verifier)
        $user = Auth::user();
        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifier'))) {
            return response()->json(['message' => 'Akses tidak diizinkan.'], 403);
        }

        $filterPeriod = $request->input('period', 'this_month');
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        try {
            $dateRanges = $this->resolveDateRanges($filterPeriod, $customStartDate, $customEndDate);
        } catch (\Exception $e) {
            Log::error('Error resolving date ranges in DashboardAdminController: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses rentang tanggal.', 'error' => $e->getMessage()], 400);
        }


        // Laporan Masuk
        $laporanMasukThisPeriod = $this->getReportCount($dateRanges->currentStart, $dateRanges->currentEnd);
        $laporanMasukLastPeriod = $this->getReportCount($dateRanges->previousStart, $dateRanges->previousEnd);
        $laporanMasukPercentage = $this->calcPercentageChange($laporanMasukThisPeriod, $laporanMasukLastPeriod);
        $totalLaporanMasuk = Report::count(); // Total keseluruhan

        // Laporan Terverifikasi
        $laporanTerverifikasiThisPeriod = $this->getVerifiedReportCount($dateRanges->currentStart, $dateRanges->currentEnd);
        $laporanTerverifikasiLastPeriod = $this->getVerifiedReportCount($dateRanges->previousStart, $dateRanges->previousEnd);
        $laporanTerverifikasiPercentage = $this->calcPercentageChange($laporanTerverifikasiThisPeriod, $laporanTerverifikasiLastPeriod);
        $totalLaporanTerverifikasi = Report::whereIn('status', ['published', 'approved'])->count(); // Total keseluruhan

        // Total Pengguna
        $totalPenggunaThisPeriod = $this->getUserCount($dateRanges->currentStart, $dateRanges->currentEnd);
        $totalPenggunaLastPeriod = $this->getUserCount($dateRanges->previousStart, $dateRanges->previousEnd);
        $totalPenggunaPercentage = $this->calcPercentageChange($totalPenggunaThisPeriod, $totalPenggunaLastPeriod);
        $totalPengguna = User::count(); // Total keseluruhan

        // Saldo Top Up (Pastikan tabel 'top_ups' dan kolom 'amount', 'status', 'created_at' ada)
        $saldoTopUpThisPeriod = $this->getTopUpAmount($dateRanges->currentStart, $dateRanges->currentEnd);
        $saldoTopUpLastPeriod = $this->getTopUpAmount($dateRanges->previousStart, $dateRanges->previousEnd);
        $saldoTopUpPercentage = $this->calcPercentageChange($saldoTopUpThisPeriod, $saldoTopUpLastPeriod);
        $totalSaldoTopUp = DB::table('top_ups')->where('status', 'verified')->sum('amount'); // Total keseluruhan

        // Data untuk Charts (sekarang difilter berdasarkan periode)
        $reportStatusCounts = Report::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$dateRanges->currentStart, $dateRanges->currentEnd]) // Filter berdasarkan periode
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Laporan Terbaru (bisa tetap all-time atau difilter periode jika diinginkan)
        // Untuk contoh ini, kita buat recentReports juga mengikuti periode filter
        $recentReportsQuery = Report::with('user')
                                ->whereBetween('created_at', [$dateRanges->currentStart, $dateRanges->currentEnd]);
        
        // Jika periode 'all_time', mungkin tidak ingin limit, atau limit lebih besar
        if ($filterPeriod === 'all_time') {
            $recentReports = $recentReportsQuery->latest()->take(10)->get(); // Ambil lebih banyak untuk all_time
        } else {
            $recentReports = $recentReportsQuery->latest()->take(5)->get();
        }


        $dashboardStats = [
            'laporanMasuk' => [
                'count' => $laporanMasukThisPeriod,
                'total_overall' => $totalLaporanMasuk,
                'percentage' => $laporanMasukPercentage,
                'period_label' => $dateRanges->label,
            ],
            'laporanTerverifikasi' => [
                'count' => $laporanTerverifikasiThisPeriod,
                'total_overall' => $totalLaporanTerverifikasi,
                'percentage' => $laporanTerverifikasiPercentage,
                'period_label' => $dateRanges->label,
            ],
            'totalPengguna' => [
                'count' => $totalPenggunaThisPeriod,
                'total_overall' => $totalPengguna,
                'percentage' => $totalPenggunaPercentage,
                'period_label' => $dateRanges->label,
            ],
            'saldoTopUp' => [
                'amount' => $saldoTopUpThisPeriod,
                'total_overall' => $totalSaldoTopUp,
                'percentage' => $saldoTopUpPercentage,
                'period_label' => $dateRanges->label,
            ],
            // Informasi filter yang sedang aktif bisa dikirim kembali untuk konfirmasi di FE
            'filter_period' => $filterPeriod,
            'current_period_start' => $dateRanges->currentStart->toDateString(),
            'current_period_end' => $dateRanges->currentEnd->toDateString(),
        ];

        return response()->json([
            'dashboardStats' => $dashboardStats,
            'recentReports' => ReportResource::collection($recentReports), // Gunakan resource
            'reportStatusCounts' => $reportStatusCounts, // Sebelumnya 'reportData'
        ]);
    }

    // Helper methods (getReportCount, getVerifiedReportCount, dll.) tetap sama
    // Pastikan semua helper method ini ada dan berfungsi dengan benar.
    // ... (kode helper method dari controller aslimu) ...
    private function getReportCount(CarbonImmutable $start, CarbonImmutable $end)
    {
        return Report::whereBetween('created_at', [$start, $end])->count();
    }

    private function getVerifiedReportCount(CarbonImmutable $start, CarbonImmutable $end)
    {
        // Menggunakan updated_at untuk laporan terverifikasi mungkin lebih tepat jika status berubah
        // Jika verifikasi dicatat berdasarkan created_at laporan, maka gunakan 'created_at'
        return Report::whereIn('status', ['published', 'approved'])
            ->whereBetween('created_at', [$start, $end]) // atau 'updated_at'
            ->count();
    }

    private function getUserCount(CarbonImmutable $start, CarbonImmutable $end)
    {
        return User::whereBetween('created_at', [$start, $end])->count();
    }

    private function getTopUpAmount(CarbonImmutable $start, CarbonImmutable $end)
    {
        // Pastikan tabel 'top_ups' ada dan memiliki kolom yang benar
        if (!\Illuminate\Support\Facades\Schema::hasTable('top_ups')) {
            Log::warning('Table "top_ups" does not exist. Returning 0 for getTopUpAmount.');
            return 0;
        }
        return DB::table('top_ups')
            ->where('status', 'verified') // Asumsi status 'verified' atau 'completed'
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function resolveDateRanges(string $period, ?string $customStartDate, ?string $customEndDate): object
    {
        $now = CarbonImmutable::now();
        $currentStart = $now->startOfMonth(); // Default: this_month
        $currentEnd = $now->endOfMonth();
        $previousStart = $now->subMonthNoOverflow()->startOfMonth();
        $previousEnd = $now->subMonthNoOverflow()->endOfMonth();
        $label = "vs Bulan Sebelumnya"; // Default label

        switch ($period) {
            case 'today':
                $currentStart = $now->startOfDay();
                $currentEnd = $now->endOfDay();
                $previousStart = $now->subDay()->startOfDay();
                $previousEnd = $now->subDay()->endOfDay();
                $label = "vs Kemarin";
                break;
            case 'yesterday':
                $currentStart = $now->subDay()->startOfDay();
                $currentEnd = $now->subDay()->endOfDay();
                $previousStart = $now->subDays(2)->startOfDay();
                $previousEnd = $now->subDays(2)->endOfDay();
                $label = "vs Hari Sebelumnya";
                break;
            case 'this_week':
                $currentStart = $now->startOfWeek(CarbonImmutable::MONDAY);
                $currentEnd = $now->endOfWeek(CarbonImmutable::SUNDAY);
                $previousStart = $now->subWeek()->startOfWeek(CarbonImmutable::MONDAY);
                $previousEnd = $now->subWeek()->endOfWeek(CarbonImmutable::SUNDAY);
                $label = "vs Minggu Lalu";
                break;
            case 'last_week':
                $currentStart = $now->subWeek()->startOfWeek(CarbonImmutable::MONDAY);
                $currentEnd = $now->subWeek()->endOfWeek(CarbonImmutable::SUNDAY);
                $previousStart = $now->subWeeks(2)->startOfWeek(CarbonImmutable::MONDAY);
                $previousEnd = $now->subWeeks(2)->endOfWeek(CarbonImmutable::SUNDAY);
                $label = "vs Minggu Sebelumnya";
                break;
            case 'this_month':
                // Default sudah benar
                break;
            case 'last_month':
                $currentStart = $now->subMonthNoOverflow()->startOfMonth();
                $currentEnd = $now->subMonthNoOverflow()->endOfMonth();
                $previousStart = $now->subMonthsNoOverflow(2)->startOfMonth();
                $previousEnd = $now->subMonthsNoOverflow(2)->endOfMonth();
                $label = "vs 2 Bulan Lalu";
                break;
            case 'this_year':
                $currentStart = $now->startOfYear();
                $currentEnd = $now->endOfYear();
                $previousStart = $now->subYearNoOverflow()->startOfYear();
                $previousEnd = $now->subYearNoOverflow()->endOfYear();
                $label = "vs Tahun Lalu";
                break;
            case 'last_year':
                $currentStart = $now->subYearNoOverflow()->startOfYear();
                $currentEnd = $now->subYearNoOverflow()->endOfYear();
                $previousStart = $now->subYearsNoOverflow(2)->startOfYear();
                $previousEnd = $now->subYearsNoOverflow(2)->endOfYear();
                $label = "vs 2 Tahun Lalu";
                break;
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    try {
                        $parsedCustomStart = CarbonImmutable::parse($customStartDate)->startOfDay();
                        $parsedCustomEnd = CarbonImmutable::parse($customEndDate)->endOfDay();

                        if ($parsedCustomStart->lte($parsedCustomEnd)) {
                            $currentStart = $parsedCustomStart;
                            $currentEnd = $parsedCustomEnd;
                            $durationInDays = $currentEnd->diffInDays($currentStart);
                            $previousEnd = $currentStart->subDay()->endOfDay();
                            $previousStart = $previousEnd->copy()->subDays($durationInDays)->startOfDay();
                            $label = "vs Periode Kustom Sebelumnya";
                        } else {
                            throw new \InvalidArgumentException('Tanggal mulai kustom harus sebelum atau sama dengan tanggal akhir.');
                        }
                    } catch (\Exception $e) {
                        Log::error('Invalid custom date range in DashboardAdminController: ' . $e->getMessage() . " Dates: $customStartDate, $customEndDate");
                        // Fallback ke default (this_month) jika custom date tidak valid
                        // Atau bisa throw error ke client
                        throw new \InvalidArgumentException("Rentang tanggal kustom tidak valid: " . $e->getMessage());
                    }
                } else {
                     throw new \InvalidArgumentException("Tanggal mulai dan akhir kustom dibutuhkan untuk periode 'custom'.");
                }
                break;
            case 'all_time':
                // Untuk all_time, currentStart bisa dari data terlama, currentEnd adalah sekarang
                // Previous period bisa di set sangat jauh ke belakang atau tidak dibandingkan (percentage 0)
                $minReportDate = Report::min('created_at');
                $currentStart = $minReportDate ? CarbonImmutable::parse($minReportDate)->startOfDay() : $now->subYears(10)->startOfYear(); // Fallback 10 tahun jika tidak ada report
                $currentEnd = $now->endOfDay();
                // Untuk 'all_time', perbandingan persentase mungkin tidak relevan, jadi previous bisa diabaikan atau diset agar persentase 0
                $previousStart = $currentStart->subSecond(); // Periode sebelumnya yang tidak akan ada datanya
                $previousEnd = $currentStart->subSecond();
                $label = "(Semua Waktu)";
                break;
            default:
                Log::warning("Invalid period '{$period}' requested for dashboard. Defaulting to 'this_month'.");
                // Default sudah 'this_month'
                break;
        }

        return (object) [
            'currentStart' => $currentStart,
            'currentEnd' => $currentEnd,
            'previousStart' => $previousStart,
            'previousEnd' => $previousEnd,
            'label' => $label,
            'selected_period' => $period // Kirim kembali periode yang dipilih
        ];
    }

    private function calcPercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        if ($current == 0 && $previous == 0) return 0.0; // Hindari NaN jika keduanya 0
        return round((($current - $previous) / abs($previous)) * 100, 1); // Gunakan abs($previous) untuk menghindari pembagian dengan negatif jika ada penurunan drastis
    }
}
