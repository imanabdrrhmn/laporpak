<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\Feedback;
use App\Models\ReportFlag;
use App\Policies\ReportPolicy;
use App\Jobs\SendReportPublishedMailJob;
use App\Jobs\SendReportRejectedMailJob;
use App\Jobs\SendReportUnpublishedMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;


class ReportManagementController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifier'))) {
            abort(403, 'Unauthorized action.');
        }

        $reports = Report::with('user')
            ->withCount('flags')
            ->latest()
            ->get()
            ->filter(function ($report) use ($user) {
                return $user->can('view', $report);
            })
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
                    'flags_count' => $report->flags_count,
                ];
            });

        return Inertia::render('Admin/Pelaporan/Index', [
            'reports' => $reports->values()->all(),
            'can' => [
                'verifyReports' => $request->user()->can('verify_reports'),
            ],
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

        if ($report->status !== 'pending') {
            return redirect()->route('laporan.index')->with('error', 'Laporan sudah diproses sebelumnya.');
        }

        $report->status = 'rejected';
        $report->save();

        SendReportRejectedMailJob::dispatch($report->user, $report);

        return redirect()->route('laporan.index')->with('success', 'Laporan ditolak');
    }
    public function publish(Report $report)
    {
        $this->authorize('verify_reports', $report);

        if ($report->status !== 'approved') {
            return redirect()->route('laporan.index')->with('error', 'Laporan harus disetujui terlebih dahulu sebelum dipublikasikan.');
        }

        $report->status = 'published';
        $report->save();

        SendReportPublishedMailJob::dispatch($report->user, $report);

        return redirect()->route('laporan.index')->with('success', 'Laporan dipublikasikan');
    }

    public function solved(Report $report)
    {
        $this->authorize('verify_reports', $report);

        if ($report->status !== 'published') {
            return redirect()->route('laporan.index')->with('error', 'Hanya laporan yang dipublikasikan yang bisa ditandai sebagai selesai.');
        }

        $report->status = 'solved';
        $report->save();

        return redirect()->route('laporan.index')->with('success', 'Laporan telah ditandai sebagai selesai');
    }

    public function unpublish(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report);

        if ($report->status !== 'published') {
            return redirect()->route('laporan.index')->with('error', 'Hanya laporan yang dipublikasikan yang bisa dibatalkan publikasinya.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $report->status = 'unpublished'; 
        $report->reason = $request->input('reason');
        $report->save();

        SendReportUnpublishedMailJob::dispatch($report->user, $report);

        return redirect()->route('laporan.index')->with('success', 'Publikasi laporan dibatalkan dengan alasan.');
    }

    public function getFlags(Report $report)
    {
        $this->authorize('verify_reports', $report);

        $flags = $report->flags()->with('user')->get()->map(function ($flag) {
            return [
                'id' => $flag->id,
                'reason' => $flag->reason,
                'created_at' => $flag->created_at->toDateTimeString(),
                'user' => [
                    'id' => $flag->user->id,
                    'name' => $flag->user->name,
                    'avatar_url' => $flag->user->avatar
                        ? asset('storage/' . $flag->user->avatar)
                        : asset('/Default-Profile.png'),
                ],
            ];
        });

        return response()->json([
            'flags' => $flags,
        ]);
    }
}
