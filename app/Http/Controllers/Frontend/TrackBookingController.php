<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class TrackBookingController extends Controller
{
    public function showForm()
    {
        return view('frontend.track.index', [
            'bookings' => null,
            'email' => old('email', ''),
        ]);
    }

    public function track(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');

        $bookings = Booking::with(['vehicle', 'pickupCity', 'returnCity', 'payments'])
            ->where('customer_email', $email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.track.index', [
            'bookings' => $bookings,
            'email' => $email,
        ]);
    }
}
