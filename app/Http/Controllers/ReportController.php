<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    use AuthorizesRequests;

    // Menampilkan semua laporan
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifier'))) {
            abort(403, 'Unauthorized action.');
        }

        $reports = Report::with('user')->latest()->get();

        return Inertia::render('Pelaporan/Index', [
            'reports' => $reports,
        ]);
    }

    // Menampilkan form untuk membuat laporan
    public function create(Request $request)
    {
        $feedbacks = Feedback::where('kategori', 'Pelaporan')->with('user')->latest()->take(10)->get();

        return Inertia::render('Pelaporan/pelaporan', [
            'feedbacks' => $feedbacks,
        ]);
    }

    // Menyimpan laporan baru
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
            'source' => 'require|string',  
            'address' => 'nullable|string', 
        ]);

        $evidencePath = $request->file('evidence')->store('evidence', 'public');

        Report::create([
            'user_id' => auth()->id(),
            'category' => $request->category,
            'description' => $request->description,
            'evidence' => $evidencePath,
            'latitude' => $request->input('location.lat'),
            'longitude' => $request->input('location.lng'),
            'service' => $request->service,
            'status' => 'pending',
            'source' => $request->source,  
            'address' => $request->address,
        ]);

        return redirect()->route('laporan.history')->with('success', 'Laporan berhasil diajukan');
    }

    // Menyunting laporan
    public function edit(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        return Inertia::render('Pelaporan/Edit', [
            'report' => $report,
        ]);
    }

    // Memperbarui laporan
    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        // Validasi data laporan
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:1500',
            'service' => 'required|string',
            'source' => 'required|string',
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

    public function destroy(Request $request, Report $report)
    {
        $this->authorize('delete', $report);

        $report->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus');
    }

    public function accept(Report $report)
    {
        $this->authorize('approve', $report);

        $report->status = 'approved';
        $report->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan diterima');
    }

    public function reject(Report $report)
    {
        $this->authorize('reject', $report);

        $report->status = 'rejected';
        $report->save();

        return redirect()->route('laporan.index')->with('error', 'Laporan ditolak');
    }

    public function publish(Report $report)
    {
        $this->authorize('publish', $report);

        if ($report->status !== 'approved') {
            return redirect()->route('laporan.index')->with('error', 'Laporan harus disetujui terlebih dahulu sebelum dipublikasikan.');
        }

        $report->status = 'published';
        $report->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan dipublikasikan');
    }

    public function search()
    {
        $reports = Report::where('status', 'published')->get();

        return Inertia::render('CariLaporan', [
            'reports' => $reports
        ]);
    }
}
