<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Melihat laporan - hanya admin atau pemilik
     */
    public function view(User $user, Report $report)
    {
        return $user->id === $report->user_id || $user->hasRole('admin') ||  $user->hasRole('verifier') ;
    }

    /**
     * Menyetujui laporan (approve) - admin atau verifier
     */
    public function approve(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }

    /**
     * Menolak laporan (reject) - admin atau verifier
     */
    public function reject(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }

    /**
     * Mempublikasikan laporan - hanya admin
     */
    public function publish(User $user, Report $report)
    {
        return $user->hasRole('admin');
    }

    /**
     * Menghapus laporan - admin atau verifier
     */
    public function delete(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }

    /**
     * Mengubah laporan - admin atau pemilik
     */
    public function update(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }
}
