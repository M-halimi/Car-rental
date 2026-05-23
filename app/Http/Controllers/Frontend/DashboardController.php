<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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

        $bookingIds = $customer->bookings()->pluck('id');

        $totalPaid = Payment::whereIn('booking_id', $bookingIds)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingPaymentCount = Payment::whereIn('booking_id', $bookingIds)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->count();

        $recentPayments = Payment::whereIn('booking_id', $bookingIds)
            ->with('booking.vehicle')
            ->latest()
            ->take(5)
            ->get();

        return view('frontend.dashboard', compact(
            'bookings',
            'activeBookings',
            'completedBookings',
            'totalBookings',
            'totalPaid',
            'pendingPaymentCount',
            'recentPayments',
            'customer'
        ));
    }
}
