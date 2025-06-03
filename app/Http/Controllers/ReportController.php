<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportFlag;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\ActivityLoggerService;
use App\Http\Resources\ReportResource;

class ReportController extends Controller
{  
    use AuthorizesRequests;

    protected $provinces = [
        'Aceh', 'Bali', 'Banten', 'Bengkulu', 'Gorontalo', 'Jakarta', 'Jambi',
        'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Kalimantan Barat', 'Kalimantan Selatan',
        'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara', 'Kepulauan Bangka Belitung',
        'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara', 'Nusa Tenggara Barat',
        'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Riau', 'Sulawesi Barat',
        'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara',
        'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara', 'Yogyakarta',
    ];

    protected $logger;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function create(Request $request)
    {
        $feedbacks = Feedback::where('kategori', 'Pelaporan')->with('user')->latest()->take(10)->get();
        $verifiedReports = Report::whereIn('status', ['published', 'approved'])->count();
        $totalReports = Report::count();
        $fraudReports = Report::where('service', 'Penipuan')->count();

        return Inertia::render('Pelaporan/pelaporan', [
            'feedbacks' => $feedbacks,
            'provinces' => $this->provinces,
            'verifiedReports' => $verifiedReports,
            'totalReports' => $totalReports,
            'fraudReports' => $fraudReports,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk memberikan laporan.');
        }

        if (!$user->email_verified_at && !$user->no_hp_verified_at) {
            return redirect()->back()->with('error', 'Akun kamu belum diverifikasi. Silakan verifikasi email atau nomor HP terlebih dahulu.');
        }

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:1500',
            'evidence' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'location.lat' => 'required|numeric',
            'location.lng' => 'required|numeric',
            'service' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'region' => 'required|string|in:' . implode(',', $this->provinces),
        ]);
        
        $lat = $request->input('location.lat');
        $lng = $request->input('location.lng');
        $region = $request->input('region');

        $evidencePath = $request->file('evidence')->store('evidence', 'public');

        $report = Report::create([
            'user_id' => auth()->id(),
            'category' => $request->category,
            'description' => $request->description,
            'evidence' => $evidencePath,
            'latitude' => $lat,
            'longitude' => $lng,
            'region' => $region,
            'service' => $request->service,
            'status' => 'pending',
            'source' => $request->source,
            'address' => $request->address,
        ]);

        $this->logger->log('Kirim Laporan', 'Pengguna mengirim laporan baru di wilayah ' . $region . ' ID: ' . $report->id);

        return back()->with('success', 'Laporan Anda berhasil dikirim dan sedang menunggu verifikasi.');
    }

    public function edit(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        return Inertia::render('Pelaporan/Edit', [
            'report' => new ReportResource($report),
            'provinces' => $this->provinces,
        ]);
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:1500',
            'service' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $report->update($request->only([
            'category', 'description', 'service', 'source', 'address'
        ]));

        $this->logger->log('Update Laporan', 'Pengguna memperbarui laporan ID #' . $report->id);

        return redirect()->route('history')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function index() 
    {
        $feedbacks = Feedback::where('kategori', 'Cari Laporan') 
                            ->with('user')
                            ->latest()
                            ->take(8) 
                            ->get();

        $publishedReportsQuery = Report::where('status', 'published');

        $reportStats = [
            'verifiedReports' => Report::whereIn('status', ['published', 'approved'])->count(),
            'totalReports' => Report::count(),
            'fraudReports' => Report::where('service', 'Penipuan')->count(),
        ];

        $initialCategories = Report::where('status', 'published')
                                ->whereNotNull('category')
                                ->where('category', '!=', '')
                                ->distinct()
                                ->orderBy('category')
                                ->pluck('category')
                                // ->filter() // filter() akan menghapus nilai null atau false, pluck sudah ambil non-null
                                ->values() 
                                ->all();

        $user = auth()->user();

        return Inertia::render('Pelaporan/CariLaporan', [
            'feedbacks' => $feedbacks,
            'reportStats' => $reportStats,
            'initialCategories' => $initialCategories,
            'user' => $user,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'sortOrder' => 'nullable|string|in:newest,oldest',
            'page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1|max:50',
        ]);

        $searchQuery = $request->input('query');
        $category = $request->input('category');
        $sortOrder = $request->input('sortOrder', 'newest');
        $perPage = (int) $request->input('perPage', 12);

        $reportsQueryBuilder = Report::with('user')
            ->where('status', 'published');

        if ($searchQuery) {
            $reportsQueryBuilder->where(function ($q) use ($searchQuery) {
                $q->where('description', 'like', "%{$searchQuery}%")
                  ->orWhere('address', 'like', "%{$searchQuery}%")
                  ->orWhere('category', 'like', "%{$searchQuery}%")
                  ->orWhere('service', 'like', "%{$searchQuery}%")
                  ->orWhere('source', 'like', "%{$searchQuery}%")
                  ->orWhereHas('user', function($userQ) use ($searchQuery) {
                      $userQ->where('name', 'like', "%{$searchQuery}%");
                  });
            });
        }

        if ($category && $category !== 'all' && $category !== '') {
            $reportsQueryBuilder->where('category', $category);
        }

        if ($sortOrder === 'oldest') {
            $reportsQueryBuilder->orderBy('created_at', 'asc');
        } else {
            $reportsQueryBuilder->orderBy('created_at', 'desc');
        }

        $reports = $reportsQueryBuilder->paginate($perPage);

        return ReportResource::collection($reports)->additional([
            'app_meta' => []
        ]);
    }


    public function flagReport(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melaporkan laporan.');
        }

        $reportToFlag = Report::find($request->report_id);
        if (!$reportToFlag || $reportToFlag->status !== 'published') {
            return back()->with('error', 'Hanya laporan yang sudah dipublikasi yang bisa Anda laporkan.');
        }

        $exists = ReportFlag::where('report_id', $request->report_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah pernah menandai laporan ini.');
        }

        try {
            ReportFlag::create([
                'report_id' => $request->report_id,
                'user_id' => $user->id,
                'reason' => $request->reason,
            ]);

            if (property_exists($this, 'logger') && method_exists($this->logger, 'log')) {
                $this->logger->log('Menandai Laporan', 'Pengguna menandai laporan ID #' . $request->report_id);
            } else {
                Log::info('User #' . $user->id . ' menandai laporan ID #' . $request->report_id);
            }

            return back()->with('success', 'Laporan berhasil ditandai.');

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Kamu sudah pernah menandai laporan ini.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menandai laporan. Coba lagi nanti.');
        }
    }

}
