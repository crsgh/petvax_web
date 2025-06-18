<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;

// Daily - Delete activity records older than 7 weeks
Schedule::call(function () {
    DB::table('activity_records')
        ->where('created_at', '<', Carbon::now()->subWeeks(7))
        ->delete();
})->everyMinute();

// Every minute (for testing) - Delete read notifications
Schedule::call(function () {
    DB::table('notifications')
        ->where('is_read', 1)
        ->delete();
})->everyMinute();

Schedule::call(function () {
    $lowStockItems = DB::table('inventory_items')
        ->where('quantity', '<', 10)
        ->get();

    foreach ($lowStockItems as $item) {
        DB::table('notifications')->insert([
            'clinic_id'   => $item->clinic_id,
            'user_id'     => 5,
            'title'       => 'Low Stock Alert',
            'message'     => "Low stock alert: {$item->name} (Qty: {$item->quantity})",
            'type'        => 'inventory',
            'for_user'    => 0,
            'is_read'     => 0,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
})->everyMinute();