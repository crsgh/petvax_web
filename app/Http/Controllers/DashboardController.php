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

        $monthlyBookings = [];
        $currentMonth = now()->format('Y-m');

        foreach ($bookings as $booking) {
            if ($booking->status === 'completed') {
                $client = User::find($booking->staff_id);
                // Check if booking name already exists in the list
                $existingIndex = array_search($booking->name, array_column($topBookings, 'name'));
                
                if ($existingIndex !== false) {
                    // Increment completed count for existing booking
                    $topBookings[$existingIndex]['completed_count']++;
                } else {
                    // Add new booking entry
                    $topBookings[] = [
                        'name' => $client->name,
                        'email' => $client->email,
                        'completed_count' => 1
                    ];
                }

                // Add to monthly summary
                $bookingMonth = $booking->created_at->format('Y-m');
                if (!isset($monthlyBookings[$bookingMonth])) {
                    $monthlyBookings[$bookingMonth] = [
                        'count' => 0,
                        'income' => 0
                    ];
                }
                $monthlyBookings[$bookingMonth]['count']++;
                $monthlyBookings[$bookingMonth]['income'] += $booking->total_amount;
            }

            // Check if booking is for today
            if ($booking->status === 'completed' && $booking->created_at->isToday()) {
                $todayBookingCounts++;
                $todayIncome += $booking->total_amount;
            }
        }

        // Sort topBookings by completed date
        usort($topBookings, function($a, $b) {
            return strtotime($b['completed']) - strtotime($a['completed']);
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
        //dd(auth()->user()->id);//Notification::where('user_id', auth()->user()->id)->where('is_read',0)->get(),
        return view('dashboard', [
            'topBookings' => $topBookings,
            'todayStats' => $todayStats,
            'todayBookingCounts' => $todayBookingCounts,
            'todayIncome' => $todayIncome,
            'monthlyBookings' => $monthlyBookings,
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }
}