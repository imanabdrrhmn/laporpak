<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Laporan masuk total
        $laporanMasukCount = Report::count();

        // Laporan masuk bulan ini dan bulan lalu
        $laporanMasukThisMonth = Report::where('created_at', '>=', $startOfThisMonth)->count();
        $laporanMasukLastMonth = Report::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $laporanMasukPercentage = $this->calcPercentageChange($laporanMasukThisMonth, $laporanMasukLastMonth);

        // Laporan terverifikasi total (misal status 'verified')
        $laporanTerverifikasiCount = Report::where('status', 'verified')->count();

        $laporanTerverifikasiThisMonth = Report::where('status', 'verified')
            ->where('updated_at', '>=', $startOfThisMonth)
            ->count();

        $laporanTerverifikasiLastMonth = Report::where('status', 'verified')
            ->whereBetween('updated_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $laporanTerverifikasiPercentage = $this->calcPercentageChange($laporanTerverifikasiThisMonth, $laporanTerverifikasiLastMonth);

        // Total pengguna
        $totalPenggunaCount = User::count();

        $totalPenggunaThisMonth = User::where('created_at', '>=', $startOfThisMonth)->count();
        $totalPenggunaLastMonth = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $totalPenggunaPercentage = $this->calcPercentageChange($totalPenggunaThisMonth, $totalPenggunaLastMonth);

        // Saldo Top-Up total & bulan ini vs bulan lalu
        $saldoTopUpAmount = DB::table('top_ups')->sum('amount');

        $saldoTopUpThisMonth = DB::table('top_ups')->where('created_at', '>=', $startOfThisMonth)->sum('amount');
        $saldoTopUpLastMonth = DB::table('top_ups')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');

        $saldoTopUpPercentage = $this->calcPercentageChange($saldoTopUpThisMonth, $saldoTopUpLastMonth);

        // Ambil 5 laporan terbaru
        $recentReports = Report::latest()->take(5)->get();

        $dashboardStats = [
            'laporanMasuk' => [
                'count' => $laporanMasukCount,
                'percentage' => $laporanMasukPercentage,
            ],
            'laporanTerverifikasi' => [
                'count' => $laporanTerverifikasiCount,
                'percentage' => $laporanTerverifikasiPercentage,
            ],
            'totalPengguna' => [
                'count' => $totalPenggunaCount,
                'percentage' => $totalPenggunaPercentage,
            ],
            'saldoTopUp' => [
                'amount' => $saldoTopUpAmount,
                'percentage' => $saldoTopUpPercentage,
            ],
        ];

        return inertia('Admin/Dashboard/DashboardAdmin', [
            'dashboardStats' => $dashboardStats,
            'reports' => $recentReports,
        ]);
    }

    private function calcPercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
