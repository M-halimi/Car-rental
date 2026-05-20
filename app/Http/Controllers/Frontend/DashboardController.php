<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('frontend.login');
        }

        $customer = $user->customer;

        if (! $customer) {
            return redirect()->route('frontend.home')
                ->with('error', 'Customer profile not found.');
        }

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
