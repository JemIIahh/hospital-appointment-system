<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'kpis'             => $this->kpis(),
            'statusBreakdown'  => $this->statusBreakdown(),
            'byDepartment'     => $this->appointmentsByDepartment(),
            'last30Days'       => $this->bookingsLast30Days(),
            'topDoctors'       => $this->topDoctors(),
        ]);
    }

    private function kpis(): array
    {
        $total = Appointment::count();
        $thisMonth = Appointment::whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->count();

        $completed = Appointment::where('status', 'completed')->count();
        $noShow = Appointment::where('status', 'no_show')->count();
        $finalised = Appointment::whereIn('status', ['completed', 'cancelled', 'no_show'])->count();

        return [
            'total'           => $total,
            'this_month'      => $thisMonth,
            'completion_rate' => $finalised > 0 ? round(($completed / $finalised) * 100, 1) : 0,
            'no_show_rate'    => $finalised > 0 ? round(($noShow / $finalised) * 100, 1) : 0,
        ];
    }

    private function statusBreakdown(): array
    {
        $rows = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $labels = [];
        $data = [];
        foreach ($statuses as $s) {
            $labels[] = ucfirst(str_replace('_', ' ', $s));
            $data[] = $rows[$s] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function appointmentsByDepartment(): array
    {
        $rows = Department::leftJoin('doctors', 'departments.id', '=', 'doctors.department_id')
            ->leftJoin('appointments', function ($join) {
                $join->on('doctors.id', '=', 'appointments.doctor_id')
                     ->where('appointments.status', '!=', 'cancelled');
            })
            ->select('departments.name', DB::raw('COUNT(appointments.id) as count'))
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('name')->all(),
            'data'   => $rows->pluck('count')->map(fn ($n) => (int) $n)->all(),
        ];
    }

    private function bookingsLast30Days(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $rows = Appointment::where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->pluck('count', 'day')
            ->all();

        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($date)->format('M j');
            $data[] = (int) ($rows[$date] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function topDoctors(int $limit = 5): array
    {
        $rows = Doctor::leftJoin('appointments', function ($join) {
                $join->on('doctors.id', '=', 'appointments.doctor_id')
                     ->where('appointments.status', '!=', 'cancelled');
            })
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(appointments.id) as count'))
            ->groupBy('doctors.id', 'users.name')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('name')->map(fn ($n) => 'Dr. '.$n)->all(),
            'data'   => $rows->pluck('count')->map(fn ($n) => (int) $n)->all(),
        ];
    }
}
