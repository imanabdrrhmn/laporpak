<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;

class ReportPolicy

{
    public function verify_reports(User $user, Report $report)
    {
        return $user->can('verify_reports');
    }

    public function view(User $user, Report $report)
    {
        // Cek permission berdasarkan service laporan
        if ($report->service === 'Penipuan') {
            return $user->can('view_reports_penipuan');
        }
        if ($report->service === 'Infrastruktur') {
            return $user->can('view_reports_infrastruktur');
        }
        return false;
    }
}
