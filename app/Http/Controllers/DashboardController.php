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
        $bookings = Booking::all();
        $topBookings = [];
        $todayBookingCounts = 0;
        $todayIncome = 0;
        $totalUser = User::all()->count();

        $weeklyBookings = [];
        $dailyBookings = []; // Added missing dailyBookings array
        $currentWeek = now()->startOfWeek()->format('Y-W');

        foreach ($bookings as $booking) {
            if ($booking->status === 'completed') {
                $client = User::find($booking->staff_id);
                
                // Skip if client not found
                if (!$client) {
                    continue;
                }
                
                // Use client name consistently for both searching and storing
                $clientName = $client->name;
                
                // Check if client name already exists in the list
                $existingIndex = array_search($clientName, array_column($topBookings, 'name'));
                
                if ($existingIndex !== false) {
                    // Increment completed count for existing booking
                    $topBookings[$existingIndex]['completed_count']++;
                } else {
                    // Add new booking entry
                    $topBookings[] = [
                        'avatar' => $client->avatar,
                        'name' => $clientName,
                        'email' => $client->email,
                        'completed_count' => 1
                    ];
                }

                // Add to weekly summary
                $bookingWeek = $booking->created_at->startOfWeek()->format('Y-W');
                if (!isset($weeklyBookings[$bookingWeek])) {
                    $weeklyBookings[$bookingWeek] = [
                        'count' => 0,
                        'income' => 0,
                        'start_date' => $booking->created_at->startOfWeek()->format('Y-m-d'),
                        'end_date' => $booking->created_at->endOfWeek()->format('Y-m-d')
                    ];
                }
                $weeklyBookings[$bookingWeek]['count']++;
                $weeklyBookings[$bookingWeek]['income'] += $booking->total_amount;

                // Add to daily summary
                $bookingDate = $booking->created_at->format('Y-m-d');
                if (!isset($dailyBookings[$bookingDate])) {
                    $dailyBookings[$bookingDate] = [
                        'count' => 0,
                        'income' => 0
                    ];
                }
                $dailyBookings[$bookingDate]['count']++;
                $dailyBookings[$bookingDate]['income'] += $booking->total_amount;
            }

            // Check if booking is for today
            if ($booking->status === 'completed' && $booking->created_at->isToday()) {
                $todayBookingCounts++;
                $todayIncome += $booking->total_amount;
            }
        }

        // Sort topBookings by completed_count in descending order
        usort($topBookings, function($a, $b) {
            return $b['completed_count'] - $a['completed_count'];
        });

        // Get today's new users
        $todayUsers = User::whereDate('created_at', today())->get();

        // Get major statistics for today
        $todayStats = [
            'new_users' => $todayUsers->count(),
            'new_clients' => $todayUsers->where('role_id', 4)->count(),
            'new_staff' => $todayUsers->whereIn('role_id', [1, 2, 3])->count(),
            'latest_users' => $todayUsers->take(5),
            'total_users' => $totalUser,
        ];

        return view('dashboard', [
            'topBookings' => $topBookings,
            'todayStats' => $todayStats,
            'todayBookingCounts' => $todayBookingCounts,
            'todayIncome' => $todayIncome,
            'weeklyBookings' => $weeklyBookings,
            'dailyBookings' => $dailyBookings, // Added dailyBookings to view data
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }
}