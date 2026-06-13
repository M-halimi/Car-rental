<?php

namespace App\Filament\Agency\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLoginPage;
use Filament\Notifications\Notification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Hash;

class Login extends BaseLoginPage
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (Limit $_) {
            $this->getRateLimitedNotification(null)?->send();

            return null;
        }

        $data = $this->form->getState();

        $user = User::where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            $agency = $user->agency;

            if ($agency && ! $agency->isActive()) {
                Notification::make()
                    ->title('Account suspended')
                    ->body('Your agency account has been suspended. Please contact support.')
                    ->danger()
                    ->send();

                $this->addError('email', 'Your agency account has been suspended. Please contact support.');

                return null;
            }
        }

        return parent::authenticate();
    }
}
