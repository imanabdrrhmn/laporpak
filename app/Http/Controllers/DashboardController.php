<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\Inertia;
use App\Models\Feedback;
use App\Models\Report;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user(); 

        $reportCounts = Report::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Published' THEN 1 ELSE 0 END) as selected,
                SUM(CASE WHEN status IN ('Pending', 'Approved') THEN 1 ELSE 0 END) as in_process
            ")
            ->where('user_id', $user->id)
            ->first();

        $stats = [
            'reports' => $reportCounts->total ?? 0,
            'selected' => $reportCounts->selected ?? 0,
            'in_process' => $reportCounts->in_process ?? 0,
        ];

        $feedbacks = Feedback::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($feedback) {
                $isUpdated = $feedback->updated_at->gt($feedback->created_at);
                return [
                    'id' => $feedback->id,
                    'user_id' => $feedback->user_id,
                    'user' => [
                        'id' => $feedback->user->id,
                        'name' => $feedback->user->name,
                        'avatar_url' => $feedback->user->avatar 
                            ? asset('storage/' . $feedback->user->avatar) 
                            : asset('/Default-Profile.png'),
                    ],
                    'message' => $feedback->message,
                    'rating' => $feedback->rating,
                    'kategori' => $feedback->kategori,
                    'created_at' => $feedback->created_at->diffForHumans(), 
                    'updated_at' => $isUpdated ? $feedback->updated_at->diffForHumans() : null,
                    'is_updated' => $isUpdated,
                ];
            });
        $reports = Report::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'tanggal' => $report->created_at->format('d-m-Y'),
                    'jenis' => $report->category,
                    'sumber' => $report->source,
                    'description' => $report->description,
                    'status' => $report->status,
                ];
            });
            $activityLogs = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activity' => $log->activity,
                    'description' => $log->description,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });


        return Inertia::render('Dashboard', [
            'user' => $user,
            'feedbacks' => $feedbacks,
            'stats' => $stats,
            'reports' => $reports,
            'activityLogs' => $activityLogs,
        ]);
    }
}
