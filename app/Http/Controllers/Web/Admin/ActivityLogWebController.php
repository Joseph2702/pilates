<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\ActivityLog;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;

class ActivityLogWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('roles.nama_role', 'admin')->where('user_roles.is_active', true);
                });
            })
            ->orderBy('tanggal_log', 'desc')
            ->paginate(20);

        $permissions = $this->buildPermissions('activity_logs');
        return view('admin.activity-logs.index', compact('logs', 'permissions'));
    }
}
