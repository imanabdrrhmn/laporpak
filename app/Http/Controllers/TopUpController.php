<?php
namespace App\Http\Controllers;

use App\Models\TopUp;
use App\Models\TopUpStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TopUpController extends Controller
{
    use AuthorizesRequests;

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

        return redirect()->route('top-ups.index')->with('success', 'Top up request submitted');
    }

   public function adminIndex(Request $request)
{
    $query = TopUp::with('user')->orderBy('created_at', 'desc');

    if ($request->has('status') && in_array($request->status, ['pending','verified','rejected'])) {
        $query->where('status', $request->status);
    }

    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    $topUps = $query->paginate(10)->withQueryString()->through(function ($topUp) {
        return [
            'id' => $topUp->id,
            'amount' => $topUp->amount,
            'status' => $topUp->status,
            'proof' => $topUp->proof,
            'payment_method' => $topUp->payment_method,
            'created_at' => $topUp->created_at->toDateTimeString(),
            'user' => [
                'id' => $topUp->user->id,
                'name' => $topUp->user->name,
                'email' => $topUp->user->email,
                'avatar_url' => $topUp->user->avatar 
                    ? asset('storage/' . $topUp->user->avatar) 
                    : asset('/Default-Profile.png'),
            ],
        ];
    });

    return Inertia::render('Admin/TopUps/Index', [
        'topUps' => $topUps,
        'filters' => $request->only('status', 'search'),
    ]);
}


    public function verify($id)
    {
        $topUp = TopUp::findOrFail($id);
        $this->authorize('verify', $topUp);

        if ($topUp->status !== 'pending') {
            return redirect()->back()->with('error', 'Top up sudah diproses sebelumnya.');
        }

        $user = $topUp->user;
        $user->balance += $topUp->amount;
        $user->save();

        $topUp->status = 'verified';
        $topUp->save();

        return redirect()->back()->with('success', 'Top up berhasil diverifikasi.');
    }

    public function reject($id)
    {
        $topUp = TopUp::findOrFail($id);
        $this->authorize('reject', $topUp);

        if ($topUp->status !== 'pending') {
            return redirect()->back()->with('error', 'Top up sudah diproses sebelumnya.');
        }

        $topUp->status = 'rejected';
        $topUp->save();

        return redirect()->back()->with('success', 'Top up berhasil ditolak.');
    }

    public function exportTopUpLogsToCsv(Request $request)
    {
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
                    $log->notes,
                    $log->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
