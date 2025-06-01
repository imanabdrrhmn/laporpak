<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Jobs\SendReportPublishedMailJob;
use App\Jobs\SendReportRejectedMailJob;
use App\Jobs\SendReportUnpublishedMailJob;
use App\Jobs\SendReportSolvedMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReportResource;
use App\Http\Resources\FlagResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportManagementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifier'))) {
            return response()->json(['message' => 'Akses tidak diizinkan untuk peran Anda.'], 403);
        }

        $baseQueryForStats = Report::visibleTo($user);
        $paginatedQuery = Report::with(['user'])->visibleTo($user); 

        if (!$user->hasRole('admin') && !(clone $baseQueryForStats)->exists()) {
            return ReportResource::collection(collect())->additional($this->getEmptyResponseMetadata());
        }

        $reportStats = $this->calculateGlobalStats(clone $baseQueryForStats);
        $uniqueCategories = (clone $baseQueryForStats)->distinct()->whereNotNull('category')->pluck('category')->filter()->values()->all();
        $uniqueServices = (clone $baseQueryForStats)->distinct()->whereNotNull('service')->pluck('service')->filter()->values()->all();

        // Terapkan filter dari request
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $paginatedQuery->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('service', 'like', "%{$searchTerm}%")
                  ->orWhere('source', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($userQuery) use ($searchTerm){ 
                      $userQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        if ($request->filled('category')) {
            $paginatedQuery->where('category', $request->category);
        }
        if ($user->hasRole('admin') && $request->filled('service')) {
             $paginatedQuery->where('service', $request->service);
        }
        if ($request->filled('status')) {
            $paginatedQuery->where('status', $request->status);
        }

        $sortDirection = $request->input('sort_direction', 'desc');
        $paginatedQuery->orderBy('created_at', $sortDirection === 'asc' ? 'asc' : 'desc');

        $perPage = $request->input('per_page', $request->input('itemsPerPage', 15));
        $reports = $paginatedQuery->withCount('flags')->paginate((int)$perPage);

        return ReportResource::collection($reports)->additional([
            'meta' => [
                'stats' => $reportStats,
                'filter_options' => [
                    'categories' => $uniqueCategories,
                    'services' => $uniqueServices,
                ],
            ]
        ]);
    }

    
    private function calculateGlobalStats($query) 
    {
        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'published' => (clone $query)->where('status', 'published')->count(),
            'solved' => (clone $query)->where('status', 'solved')->count(),
            'unpublished' => (clone $query)->where('status', 'unpublished')->count(),
        ];
    }

    private function getEmptyResponseMetadata() 
    {
        return [
            'stats' => $this->calculateGlobalStats(Report::query()->whereRaw('1=0')),
            'filter_options' => ['categories' => [], 'services' => []],
        ];
    }

    public function getFlags(Request $request, Report $report)
    {
        $this->authorize('view', $report); 
        $flags = $report->flags()->with('user')->latest()->get(); 
        return FlagResource::collection($flags);
    }

    private function validateReportStatus(Report $report, $expectedStatus, string $actionName = 'melakukan aksi ini')
    {
        $statuses = is_array($expectedStatus) ? $expectedStatus : [$expectedStatus];
        if (!in_array($report->status, $statuses)) {
            $statusString = implode("' atau '", $statuses);
            abort(422, "Laporan harus berstatus '{$statusString}' untuk {$actionName}. Status saat ini: '{$report->status}'.");
        }
    }

    public function accept(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report); 
        $this->validateReportStatus($report, 'pending', 'diterima');
        $report->status = 'approved';
        $report->save();
        return new ReportResource($report->loadMissing(['user'])->loadCount('flags'));
    }

    public function reject(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report); 
        $this->validateReportStatus($report, 'pending', 'ditolak');
        
        $validated = $request->validate(['reason' => 'nullable|string|max:1000']);

        $report->status = 'rejected';
        if (isset($validated['reason'])) {
            $report->rejected_reason = $validated['reason']; 
        }
        $report->save();
        SendReportRejectedMailJob::dispatch($report->user, $report); 
        return new ReportResource($report->loadMissing(['user'])->loadCount('flags'));
    }

    public function publish(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report);
        $this->validateReportStatus($report, 'approved', 'dipublikasikan');
        $report->status = 'published';
        $report->save();
        SendReportPublishedMailJob::dispatch($report->user, $report);
        return new ReportResource($report->loadMissing(['user'])->loadCount('flags'));
    }

    public function unpublish(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report);
        $this->validateReportStatus($report, 'published', 'dibatalkan publikasinya');
        $validated = $request->validate(['reason' => 'required|string|max:1000']);
        $report->status = 'unpublished';
        $report->reason = $validated['reason']; 
        $report->save();
        SendReportUnpublishedMailJob::dispatch($report->user, $report);
        return new ReportResource($report->loadMissing(['user'])->loadCount('flags'));
    }

    public function solved(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report);
        $this->validateReportStatus($report, 'published', 'ditandai selesai');
        $report->status = 'solved';
        $report->save();
        SendReportSolvedMailJob::dispatch($report->user, $report);
        return new ReportResource($report->loadMissing(['user'])->loadCount('flags'));
    }

    public function destroy(Request $request, Report $report)
    {
        $this->authorize('verify_reports', $report); 
        
        $report->delete();
        return response()->json(['message' => 'Laporan berhasil dihapus.'], 200);
    }
}
