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
        if ($user->can('view_reports_by_region')) {
            $allowedRegions = $user->allowed_regions ?? [];

            if (!in_array($report->region, $allowedRegions)) {
                return false;
            }
        }

        if ($report->service === 'Penipuan') {
            return $user->can('view_reports_penipuan');
        }

        if ($report->service === 'Infrastruktur') {
            return $user->can('view_reports_infrastruktur');
        }

        return false;
    }

}
