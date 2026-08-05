<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filterUser = $request->input('user', '');
        $filterAksi = $request->input('aksi', '');
        $filterEntitas = $request->input('entitas', '');
        $periodeMulai = $request->input('periode_mulai', '');
        $periodeSelesai = $request->input('periode_selesai', '');
        $perPage = (int) $request->input('per_page', 15);

        $query = ActivityLog::query()->with('user');

        if ($filterUser !== '') {
            $query->where('id_user', $filterUser);
        }

        if ($filterAksi !== '') {
            $query->where('aksi', $filterAksi);
        }

        if ($filterEntitas !== '') {
            $query->where('entitas', $filterEntitas);
        }

        if ($periodeMulai !== '' && $periodeSelesai !== '') {
            $query->whereBetween('created_at', [$periodeMulai, $periodeSelesai]);
        }

        $activityLogs = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $users = User::orderBy('nama_lengkap')->get();

        return view('admin.activity-log.index', compact(
            'activityLogs',
            'users',
            'filterUser',
            'filterAksi',
            'filterEntitas',
            'periodeMulai',
            'periodeSelesai',
            'perPage'
        ));
    }
}
