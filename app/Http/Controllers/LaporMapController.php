<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LaporMapController extends Controller
{
    public function index(Request $request)
    {
        $reportCount = Report::where('status', 'published')->count();

        $locationCount = $this->countDistinctLocations();

        $userCount = User::count();

        // Ambil feedbacks kategori 'Lapor Map', ambil 10 terbaru dengan relasi user
        $feedbacks = Feedback::with('user')
            ->where('kategori', 'Lapor Map')
            ->latest()
            ->take(10)
            ->get();

        $region = $request->query('region');

        $regions = Report::where('status', 'published')
            ->select('region')
            ->whereNotNull('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        $locationItems = $this->getLocationItems($region);

        return Inertia::render('LaporMap', [
            'reportCount' => $reportCount,
            'locationCount' => $locationCount,
            'userCount' => $userCount,
            'locationItems' => $locationItems,
            'maxReports' => $this->getMaxReports(),
            'feedbacks' => $feedbacks,
            'regions' => $regions,
            'selectedRegion' => $region,
        ]);
    }

    private function countDistinctLocations()
    {
        return Report::where('status', 'published')
                     ->select('latitude', 'longitude')
                     ->distinct()
                     ->count();
    }

    private function getLocationItems($region = null)
    {
        $query = Report::where('status', 'published')->select(
            DB::raw('latitude'),
            DB::raw('longitude'),
            DB::raw('region'), 
            DB::raw('COUNT(*) as reports'),
            DB::raw('MAX(created_at) as lastReport'),
            DB::raw('GROUP_CONCAT(DISTINCT category) as fraudTypes'),
            DB::raw('MIN(address) as address')
        );

        if ($region) {
            $query->where('region', $region);
        }

        $rawLocations = $query->groupBy('latitude', 'longitude', 'region')->get();

        return $rawLocations->map(function ($item) {
            return $this->mapLocationItem($item);
        });
    }


    private function mapLocationItem($item)
    {
        if ($item->reports > 500) {
            $type = 'high';
            $status = 'Area Waspada Tinggi';
        } elseif ($item->reports >= 100) {
            $type = 'medium';
            $status = 'Area Waspada Menengah';
        } else {
            $type = 'low';
            $status = 'Area Waspada Rendah';
        }

        return [
            'id' => md5($item->latitude . ',' . $item->longitude),
            'name' => $item->address ?? 'Unknown Location',
            'region' => $item->region ?? 'Tidak Diketahui',
            'coordinates' => [(float)$item->latitude, (float)$item->longitude],
            'reports' => (int)$item->reports,
            'type' => $type,
            'lastReport' => date('d M Y', strtotime($item->lastReport)),
            'address' => $item->address,
            'fraudTypes' => explode(',', $item->fraudTypes),
            'status' => $status,
            'victims' => rand(50, 2000),  // contoh dummy data
            'totalLoss' => rand(100000000, 5000000000),
            'recentReports' => []
        ];
    }

    private function getMaxReports()
    {
        $max = Report::where('status', 'published')
                    ->select(DB::raw('COUNT(*) as count'))
                    ->groupBy('latitude', 'longitude')
                    ->orderByDesc('count')
                    ->limit(1)
                    ->value('count');
        return $max ?? 0;
    }
}
