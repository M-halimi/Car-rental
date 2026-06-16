<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            $existingUser->update([
                'password' => Hash::make($request->password),
                'name' => $request->first_name.' '.$request->last_name,
            ]);

            $customer = $existingUser->customer;

            if ($customer) {
                $customer->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                ]);
            } else {
                $customer = Customer::create([
                    'user_id' => $existingUser->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                ]);
            }

            Booking::where('customer_email', $request->email)
                ->where('customer_id', '!=', $customer->id)
                ->update(['customer_id' => $customer->id]);

            Auth::login($existingUser);

            return redirect()->route('frontend.dashboard')
                ->with('success', __('frontend.registration_linked_bookings'));
        }

        $user = User::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        $customer = Customer::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        Booking::where('customer_email', $request->email)
            ->whereNull('customer_id')
            ->update(['customer_id' => $customer->id]);

        Auth::login($user);

        return redirect()->route('frontend.dashboard');
    }
}
