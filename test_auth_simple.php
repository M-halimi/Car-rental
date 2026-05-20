<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== User & Role Checks ===\n";

$agency = User::where('email', 'agency@carrental.ma')->first();
echo 'Agency found: '.($agency ? 'yes' : 'no')."\n";
if ($agency) {
    echo 'HasRole agency: '.($agency->hasRole('agency') ? 'YES' : 'NO')."\n";
    echo 'Roles: '.$agency->getRoleNames()."\n";
}

$admin = User::where('email', 'admin@admin.com')->first();
echo "\nAdmin found: ".($admin ? 'yes' : 'no')."\n";
if ($admin) {
    echo 'HasRole super_admin: '.($admin->hasRole('super_admin') ? 'YES' : 'NO')."\n";
    echo 'Roles: '.$admin->getRoleNames()."\n";
}

$customer = User::where('email', 'saad2saaad@gmail.com')->first();
echo "\nCustomer found: ".($customer ? 'yes' : 'no')."\n";
if ($customer) {
    echo 'HasRole customer: '.($customer->hasRole('customer') ? 'YES' : 'NO')."\n";
    echo 'Roles: '.$customer->getRoleNames()."\n";
}

echo "\n=== Auth Guard Attempts ===\n";

echo 'agency guard + agency@carrental.ma: ';
$r = Auth::guard('agency')->attempt(['email' => 'agency@carrental.ma', 'password' => 'password']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'customer guard + agency@carrental.ma: ';
$r = Auth::guard('customer')->attempt(['email' => 'agency@carrental.ma', 'password' => 'password']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'admin guard + admin@admin.com: ';
$r = Auth::guard('admin')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'customer guard + saad2saaad@gmail.com: ';
$r = Auth::guard('customer')->attempt(['email' => 'saad2saaad@gmail.com', 'password' => 'password']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'customer guard + admin@admin.com: ';
$r = Auth::guard('customer')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'admin guard + customer@saad2saaad@gmail.com: ';
$r = Auth::guard('admin')->attempt(['email' => 'saad2saaad@gmail.com', 'password' => 'password']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";

echo 'agency guard + admin@admin.com: ';
$r = Auth::guard('agency')->attempt(['email' => 'admin@admin.com', 'password' => 'admin123']);
echo ($r ? 'SUCCESS' : 'FAIL')."\n";
