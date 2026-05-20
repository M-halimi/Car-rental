<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Test 1: Direct hasRole check
echo "=== hasRole checks ===\n";
$agency = User::where('email', 'agency@carrental.ma')->first();
echo "Agency hasRole('agency'): ".($agency->hasRole('agency') ? 'YES' : 'NO')."\n";

$admin = User::where('email', 'admin@admin.com')->first();
echo "Admin hasRole('super_admin'): ".($admin->hasRole('super_admin') ? 'YES' : 'NO')."\n";

$customer = User::where('email', 'saad2saaad@gmail.com')->first();
echo "Customer hasRole('customer'): ".($customer->hasRole('customer') ? 'YES' : 'NO')."\n";

// Test 2: Password verification
echo "\n=== Password checks ===\n";
echo "Agency password matches 'password': ".(Hash::check('password', $agency->password) ? 'YES' : 'NO')."\n";
echo "Admin password matches 'admin123': ".(Hash::check('admin123', $admin->password) ? 'YES' : 'NO')."\n";

// Test 3: Auth::guard attempts
echo "\n=== Auth guard attempts ===\n";
echo 'customer guard for agency: '.(Auth::guard('customer')->attempt(['email' => 'agency@carrental.ma', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('customer')->logout();

echo 'agency guard for agency: '.(Auth::guard('agency')->attempt(['email' => 'agency@carrental.ma', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('agency')->logout();

echo 'admin guard for admin: '.(Auth::guard('admin')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('admin')->logout();

echo 'customer guard for customer: '.(Auth::guard('customer')->attempt(['email' => 'saad2saaad@gmail.com', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('customer')->logout();

echo "\ncustomer guard for admin: ".(Auth::guard('customer')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('customer')->logout();

echo 'agency guard for admin: '.(Auth::guard('agency')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('agency')->logout();

echo 'admin guard for agency: '.(Auth::guard('admin')->attempt(['email' => 'agency@carrental.ma', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('admin')->logout();

echo "\n=== Cross-guard denials (expected DENIED) ===\n";
echo 'customer guard for admin: '.(Auth::guard('customer')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('customer')->logout();
echo 'agency guard for customer: '.(Auth::guard('agency')->attempt(['email' => 'saad2saaad@gmail.com', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('agency')->logout();
echo 'admin guard for customer: '.(Auth::guard('admin')->attempt(['email' => 'saad2saaad@gmail.com', 'password' => 'password']) ? 'LOGGED IN' : 'DENIED')."\n";
Auth::guard('admin')->logout();
