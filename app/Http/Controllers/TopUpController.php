<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\TopUpStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Services\ActivityLoggerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Jobs\SendTopUpVerifiedMailJob;
use App\Jobs\SendTopUpRejectedMailJob;
use App\Http\Resources\TopUpResource;

class TopUpController extends Controller
{
    use AuthorizesRequests;

    protected $logger;

    public function __construct(ActivityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function index()
    {
        $topUps = TopUp::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('TopUps/History', [
            'topUps' => $topUps,
        ]);
    }

    public function create()
    {
        return Inertia::render('TopUps/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100000',
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('proof')->store('topup_proofs', 'public');

        TopUp::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'proof' => $path,
            'status' => 'pending',
        ]);

        $this->logger->log('Ajukan Top Up', 'Pengguna mengajukan top up sebesar Rp ' . number_format($request->amount));

        return redirect()->route('top-ups.index')->with('success', 'Top up request submitted');
    }

    public function adminIndex(Request $request)
    {
        $canView = Auth::user()->can('viewAny', TopUp::class);

        return Inertia::render('Admin/TopUps/Index', [
            'canViewTopUp' => $canView,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function getTopUpData(Request $request)
    {
        $this->authorize('viewAny', TopUp::class);

        $query = TopUp::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status') && in_array($request->status, ['pending','verified','rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $topUps = $query->paginate($perPage)->withQueryString();

        $statusCounts = [
            'pending' => TopUp::where('status', 'pending')->count(),
            'verified' => TopUp::where('status', 'verified')->count(),
            'rejected' => TopUp::where('status', 'rejected')->count(),
        ];

        return TopUpResource::collection($topUps)->additional([
            'app_meta' => [
                'statusCounts' => $statusCounts,
                'filters' => $request->only('status', 'search'),
            ]
        ]);
    }

    public function verify(TopUp $topUp)
    {
        $this->authorize('verify', $topUp);

        if ($topUp->status !== 'pending') {
            return response()->json(['message' => 'Top up sudah diproses sebelumnya.'], 422);
        }

        $topUp->status = 'verified';
        $topUp->save();

        $user = $topUp->user;
        $user->balance += $topUp->amount;
        $user->save();

        TopUpStatusLog::create([
            'topup_id' => $topUp->id,
            'changed_by' => Auth::id(),
            'status' => 'verified',
        ]);

        SendTopUpVerifiedMailJob::dispatch($user, $topUp);

        $this->logger->log('Verifikasi Top Up', 'Admin memverifikasi top up ID #' . $topUp->id);

        return new TopUpResource($topUp->load('user'));
    }

    public function reject(Request $request, TopUp $topUp)
    {
        $this->authorize('reject', $topUp);

        if ($topUp->status !== 'pending') {
            return response()->json(['message' => 'Top up sudah diproses sebelumnya.'], 422);
        }

        $topUp->status = 'rejected';
        $topUp->save();

        TopUpStatusLog::create([
            'topup_id' => $topUp->id,
            'changed_by' => Auth::id(),
            'status' => 'rejected',
        ]);

        SendTopUpRejectedMailJob::dispatch($topUp->user, $topUp);

        $this->logger->log('Tolak Top Up', 'Admin menolak top up ID #' . $topUp->id);

        return new TopUpResource($topUp->load('user'));
    }

    public function exportTopUpLogsToCsv(Request $request)
    {
        $this->authorize('viewAny', TopUp::class);

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = TopUpStatusLog::with(['topUp.user', 'changedBy'])
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'top_up_status_logs_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Log ID', 'TopUp ID', 'User ID', 'User Name', 'Amount', 'Changed By ID', 'Changed By Name',
            'Status', 'Notes', 'Created At'
        ];

        $callback = function() use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($query as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->topup_id,
                    $log->topUp->user->id ?? '',
                    $log->topUp->user->name ?? '',
                    $log->topUp->amount ?? '',
                    $log->changed_by,
                    $log->changedBy->name ?? '',
                    $log->status,
                    $log->notes ?? '',
                    $log->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        $this->logger->log('Ekspor Log Top Up', 'Admin mengekspor log top up ke CSV');

        return Response::stream($callback, 200, $headers);
    }
}
