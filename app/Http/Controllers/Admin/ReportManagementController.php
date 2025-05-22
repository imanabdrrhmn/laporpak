<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportManagementController
{
    use AuthorizesRequests;

    // Menampilkan semua laporan
    public function index(Request $request)
    {
    $user = $request->user();

    if (!$user || !($user->hasRole('admin') || $user->hasRole('verifier'))) {
        abort(403, 'Unauthorized action.');
    }

    $reports = Report::with('user')->latest()->get()->map(function ($report) {
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
        'service' => $report->service,
        'source' => $report->source,
        'created_at' => $report->created_at->toDateTimeString(),
        'evidence' => $report->evidence 
            ? asset('storage/' . $report->evidence) 
            : null,
    ];
});


    return Inertia::render('Admin/Pelaporan/Index', [
        'reports' => $reports,
    ]);
}

    public function destroy(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report);

        $report->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus');
    }

    public function accept(Report $report)
    {
        $this->authorize('verify_reports', $report);

        $report->status = 'approved';
        $report->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan diterima');
    }

    public function reject(Report $report)
    {
        $this->authorize('verify_reports', $report);

        $report->status = 'rejected';
        $report->save();

        return redirect()->route('laporan.index')->with('error', 'Laporan ditolak');
    }

    public function publish(Report $report)
    {
        $this->authorize('verify_reports', $report);

        if ($report->status !== 'approved') {
            return redirect()->route('laporan.index')->with('error', 'Laporan harus disetujui terlebih dahulu sebelum dipublikasikan.');
        }

        $report->status = 'published';
        $report->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan dipublikasikan');
    }

    public function search(Request $request)
    {
        $reports = Report::where('status', 'published')->get();
        $feedbacks = Feedback::where('kategori', 'Cari Laporan')->with('user')->latest()->take(10)->get();

        return Inertia::render('Pelaporan/CariLaporan', [
            'reports' => $reports,
            'feedbacks' => $feedbacks,
        ]);
    }
}
