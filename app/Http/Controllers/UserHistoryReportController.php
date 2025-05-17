<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Verification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserHistoryReportController extends Controller
{
    public function allHistory(Request $request)
    {
        $user = $request->user();

        $reports = Report::where('user_id', $user->id)->latest()->get();
        // $verifications = Verification::where('user_id', $user->id)->latest()->get();

        return Inertia::render('Kelola Laporan/RiwayatPelaporan', [
            'reports' => $reports,
            // 'verifications' => $verifications,
        ]);
    }
}
