<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer;

        $bookings = $customer->bookings()
            ->with('vehicle')
            ->latest()
            ->paginate(10);

        $activeBookings = $customer->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->count();

        $completedBookings = $customer->bookings()
            ->where('status', 'completed')
            ->count();

        $totalBookings = $customer->bookings()->count();

        return view('frontend.dashboard', compact(
            'bookings',
            'activeBookings',
            'completedBookings',
            'totalBookings',
            'customer'
        ));
    }
}
