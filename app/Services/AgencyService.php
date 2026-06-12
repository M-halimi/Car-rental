<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgencyService
{
    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $email = $this->resolveEmail($data['email'] ?? null, $data['name']);
            $password = $data['password'] ?? 'password';
            $slug = $this->generateUniqueSlug($data['name']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => $password,
            ]);

            $user->assignRole('agency');

            $agency = Agency::create([
                'user_id' => $user->id,
                'city_id' => $data['city_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'email' => $email,
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return ['user' => $user, 'agency' => $agency];
        });
    }

    private function resolveEmail(?string $email, string $name): string
    {
        if ($email && ! User::where('email', $email)->exists()) {
            return $email;
        }

        $base = Str::slug($name, '.');
        $suffix = 1;

        do {
            $candidate = "{$base}.{$suffix}@carrental.ma";
            $suffix++;
        } while (User::where('email', $candidate)->exists());

        return $candidate;
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (Agency::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
