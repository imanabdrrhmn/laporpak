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
        // Jika user punya permission view_reports_by_region, batasi akses berdasarkan region
        if ($user->can('view_reports_by_region')) {
            $allowedRegions = $user->allowed_regions ?? [];

            // Jika region laporan tidak ada di allowedRegions, tolak akses
            if (!in_array($report->region, $allowedRegions)) {
                return false;
            }
        }

        // Cek permission spesifik berdasarkan jenis service laporan
        if ($report->service === 'Penipuan') {
            return $user->can('view_reports_penipuan');
        }

        if ($report->service === 'Infrastruktur') {
            return $user->can('view_reports_infrastruktur');
        }

        // Default tolak jika service tidak dikenali
        return false;
    }

}
