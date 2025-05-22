<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController
{
    use AuthorizesRequests;
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
            'source' => 'nullable|string',  
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

        return back()->with('success', true);
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
        $reports = Report::where('status', 'published')->get();
        $feedbacks = Feedback::where('kategori', 'Cari Laporan')->with('user')->latest()->take(10)->get();

        return Inertia::render('Pelaporan/CariLaporan', [
            'reports' => $reports,
            'feedbacks' => $feedbacks,
        ]);
    }
}
