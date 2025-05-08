<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the report.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Report $report)
    {
        return $user->id === $report->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can approve the report.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function approve(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }

    /**
     * Determine whether the user can delete the report.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Report $report)
    {
        return $user->hasRole('admin') || $user->hasRole('verifier');
    }
}
