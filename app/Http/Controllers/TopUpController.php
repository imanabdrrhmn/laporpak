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

        // Hitung jumlah total berdasarkan status, *abaikan filter status dan search* agar total selalu global
        $pendingCount = TopUp::where('status', 'pending')->count();
        $verifiedCount = TopUp::where('status', 'verified')->count();
        $rejectedCount = TopUp::where('status', 'rejected')->count();

        return Inertia::render('Admin/TopUps/Index', [
            'topUps' => $topUps,
            'filters' => $request->only('status', 'search'),
            'statusCounts' => [
                'pending' => $pendingCount,
                'verified' => $verifiedCount,
                'rejected' => $rejectedCount,
            ],
        ]);
    }
}