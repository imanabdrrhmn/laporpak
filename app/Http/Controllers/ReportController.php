<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportFlag;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{   
        protected $provinces = [
        'Aceh',
        'Bali',
        'Banten',
        'Bengkulu',
        'Gorontalo',
        'Jakarta',
        'Jambi',
        'Jawa Barat',
        'Jawa Tengah',
        'Jawa Timur',
        'Kalimantan Barat',
        'Kalimantan Selatan',
        'Kalimantan Tengah',
        'Kalimantan Timur',
        'Kalimantan Utara',
        'Kepulauan Bangka Belitung',
        'Kepulauan Riau',
        'Lampung',
        'Maluku',
        'Maluku Utara',
        'Nusa Tenggara Barat',
        'Nusa Tenggara Timur',
        'Papua',
        'Papua Barat',
        'Riau',
        'Sulawesi Barat',
        'Sulawesi Selatan',
        'Sulawesi Tengah',
        'Sulawesi Tenggara',
        'Sulawesi Utara',
        'Sumatera Barat',
        'Sumatera Selatan',
        'Sumatera Utara',
        'Yogyakarta',
    ];

    use AuthorizesRequests;

    public function create(Request $request)
    {
        $feedbacks = Feedback::where('kategori', 'Pelaporan')->with('user')->latest()->take(10)->get();

        return Inertia::render('Pelaporan/pelaporan', [
            'feedbacks' => $feedbacks,
            'provinces' => $this->provinces,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk memberikan laporan.');
        }

        if (!$user->email_verified_at && !$user->no_hp_verified_at) {
            return redirect()->route('laporan.index')->with('error', 'Akun kamu belum diverifikasi. Silakan verifikasi email atau nomor HP terlebih dahulu.');
        }

        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:1500',
            'evidence' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'location.lat' => 'required|numeric',
            'location.lng' => 'required|numeric',
            'service' => 'required|string',
            'source' => 'nullable|string',
            'address' => 'nullable|string',
            'region' => 'required|string|in:' . implode(',', $this->provinces),
        ]);
        
        $lat = $request->input('location.lat');
        $lng = $request->input('location.lng');
        $region = $request->input('region');

        $evidencePath = $request->file('evidence')->store('evidence', 'public');

        Report::create([
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

        return back()->with('success', true);
    }


    public function edit(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        return Inertia::render('Pelaporan/Edit', [
            'report' => $report,
        ]);
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:1500',
            'service' => 'required|string',
            'source' => 'nullable|string',
            'address' => 'nullable|string', 
        ]);

        $report->update([
            'category' => $request->category,
            'description' => $request->description,
            'service' => $request->service,
            'source' => $request->source, 
            'address' => $request->address,  
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        $query = $request->input('query', '');

        $reports = Report::with('user')
            ->where('status', 'published')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('description', 'like', "%{$query}%")
                    ->orWhere('source', 'like', "%{$query}%");  
                });
            })
            ->latest()
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'user_id' => $report->user_id,
                    'user' => [
                        'id' => $report->user->id,
                        'name' => $report->user->name,
                        'avatar_url' => $report->user->avatar 
                            ? asset('storage/' . $report->user->avatar) 
                            : asset('/Default-Profile.png'),
                    ],
                    'category' => $report->category,
                    'description' => $report->description,
                    'status' => $report->status,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'address' => $report->address,
                    'service' => $report->service,
                    'source' => $report->source,
                    'created_at' => $report->created_at->toDateTimeString(),
                    'evidence' => $report->evidence 
                        ? asset('storage/' . $report->evidence) 
                        : null,
                ];
            });

        $feedbacks = Feedback::where('kategori', 'Cari Laporan')->with('user')->latest()->take(10)->get();

        return Inertia::render('Pelaporan/CariLaporan', [
            'reports' => $reports,
            'feedbacks' => $feedbacks,
            'query' => $query,
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

        $exists = ReportFlag::where('report_id', $request->report_id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah pernah melaporkan laporan ini.');
        }

        ReportFlag::create([
            'report_id' => $request->report_id,
            'user_id' => $user->id,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Laporan berhasil ditandai sebagai hoax/misinformasi.');
    }
}
