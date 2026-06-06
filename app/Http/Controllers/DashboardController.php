<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $clinicId = $user->clinic_id;
        $isSuperAdmin = $user->role_id === 1;

        // Total users count (uses COUNT query, not collection)
        $totalUser = $isSuperAdmin
            ? User::count()
            : User::where('clinic_id', $clinicId)->count();

        // Completed bookings with date scope (last 6 months)
        $bookingsQuery = Booking::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select('id', 'staff_id', 'total_amount', 'created_at', 'clinic_id');

        if (!$isSuperAdmin) {
            $bookingsQuery->where('clinic_id', $clinicId);
        }

        $completedBookings = $bookingsQuery->get();

        // Top performers - aggregate in PHP from filtered dataset
        $staffCounts = [];
        $staffIds = [];
        foreach ($completedBookings as $b) {
            $staffIds[] = $b->staff_id;
            $staffCounts[$b->staff_id] = ($staffCounts[$b->staff_id] ?? 0) + 1;
        }
        arsort($staffCounts);
        $topStaffIds = array_slice(array_keys($staffCounts), 0, 10);

        $topBookings = [];
        if (!empty($topStaffIds)) {
            $staffMap = User::whereIn('id', $topStaffIds)->get()->keyBy('id');
            foreach ($topStaffIds as $sid) {
                $s = $staffMap->get($sid);
                if ($s) {
                    $topBookings[] = [
                        'avatar' => $s->avatar,
                        'name' => $s->name,
                        'email' => $s->email,
                        'completed_count' => $staffCounts[$sid],
                    ];
                }
            }
        }

        // Weekly and daily aggregations
        $weeklyBookings = [];
        $dailyBookings = [];
        $todayBookingCounts = 0;
        $todayIncome = 0;
        $todayStr = today()->toDateString();

        foreach ($completedBookings as $b) {
            $amount = $b->total_amount ?? 0;
            $dateStr = $b->created_at->format('Y-m-d');

            // Today's stats
            if ($dateStr === $todayStr) {
                $todayBookingCounts++;
                $todayIncome += $amount;
            }

            // Weekly
            $weekKey = $b->created_at->startOfWeek()->format('Y-W');
            if (!isset($weeklyBookings[$weekKey])) {
                $weeklyBookings[$weekKey] = [
                    'count' => 0, 'income' => 0,
                    'start_date' => $b->created_at->startOfWeek()->format('Y-m-d'),
                    'end_date' => $b->created_at->endOfWeek()->format('Y-m-d'),
                ];
            }
            $weeklyBookings[$weekKey]['count']++;
            $weeklyBookings[$weekKey]['income'] += $amount;

            // Daily
            if (!isset($dailyBookings[$dateStr])) {
                $dailyBookings[$dateStr] = ['count' => 0, 'income' => 0];
            }
            $dailyBookings[$dateStr]['count']++;
            $dailyBookings[$dateStr]['income'] += $amount;
        }

        // Today's user stats (using COUNT queries, not collections)
        $userBase = $isSuperAdmin
            ? User::whereDate('created_at', today())
            : User::where('clinic_id', $clinicId)->whereDate('created_at', today());

        $todayStats = [
            'new_users' => (clone $userBase)->count(),
            'new_clients' => (clone $userBase)->where('role_id', 4)->count(),
            'new_staff' => (clone $userBase)->whereIn('role_id', [1, 2, 3])->count(),
            'latest_users' => (clone $userBase)->latest()->take(5)->get(),
            'total_users' => $totalUser,
        ];

        // Notifications
        $notifications = match($user->role_id) {
            1 => collect([]),
            2, 3 => Notification::where('clinic_id', $clinicId)->where('is_read', 0)->get(),
            default => Notification::where('user_id', $user->id)->where('is_read', 0)->get(),
        };

        return view('dashboard', compact(
            'topBookings', 'todayStats', 'todayBookingCounts', 'todayIncome',
            'weeklyBookings', 'dailyBookings', 'notifications'
        ));
    }
}