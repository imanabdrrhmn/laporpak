<?php
namespace App\Http\Controllers;

use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class TopUpController extends Controller
{
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


    public function verify($id)
    {
        $topUp = TopUp::findOrFail($id);
        $this->authorize('verify', $topUp);

        if ($topUp->status !== 'pending') {
            return response()->json(['message' => 'Top up already processed'], 400);
        }

        $user = $topUp->user;
        $user->balance += $topUp->amount;
        $user->save();

        $topUp->status = 'verified';
        $topUp->save();

        return response()->json(['message' => 'Top up verified']);
    }

    public function reject($id)
    {
        $topUp = TopUp::findOrFail($id);
        $this->authorize('reject', $topUp);

        if ($topUp->status !== 'pending') {
            return response()->json(['message' => 'Top up already processed'], 400);
        }

        $topUp->status = 'rejected';
        $topUp->save();

        return response()->json(['message' => 'Top up rejected']);
    }
}
