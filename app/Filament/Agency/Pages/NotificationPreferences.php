<?php

namespace App\Filament\Agency\Pages;

use App\Models\NotificationPreference;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\ContractGeneratedNotification;
use App\Notifications\CustomerUploadedDocumentsNotification;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\VehicleMarkedUnavailableNotification;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class NotificationPreferences extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Notification Preferences';

    protected static string|UnitEnum|null $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.agency.pages.notification-preferences';

    public ?array $data = [];

    protected static array $notificationTypes = [
        'booking_created' => [
            'label' => 'New Booking Created',
            'description' => 'When a new booking is received.',
            'class' => BookingCreatedNotification::class,
        ],
        'booking_confirmed' => [
            'label' => 'Booking Confirmed',
            'description' => 'When a booking is confirmed.',
            'class' => BookingConfirmedNotification::class,
        ],
        'booking_cancelled' => [
            'label' => 'Booking Cancelled',
            'description' => 'When a booking is cancelled.',
            'class' => BookingCancelledNotification::class,
        ],
        'payment_received' => [
            'label' => 'Payment Received',
            'description' => 'When a payment is received.',
            'class' => PaymentReceivedNotification::class,
        ],
        'payment_pending' => [
            'label' => 'Payment Pending',
            'description' => 'When a payment is pending.',
            'class' => PaymentPendingNotification::class,
        ],
        'contract_generated' => [
            'label' => 'Contract Generated',
            'description' => 'When a rental contract is generated.',
            'class' => ContractGeneratedNotification::class,
        ],
        'vehicle_unavailable' => [
            'label' => 'Vehicle Marked Unavailable',
            'description' => 'When a vehicle is marked as unavailable or under maintenance.',
            'class' => VehicleMarkedUnavailableNotification::class,
        ],
        'customer_documents_uploaded' => [
            'label' => 'Customer Documents Uploaded',
            'description' => 'When a customer uploads new documents.',
            'class' => CustomerUploadedDocumentsNotification::class,
        ],
    ];

    public function mount(): void
    {
        $user = Filament::auth()->user();

        $this->form->fill(
            collect(self::$notificationTypes)
                ->mapWithKeys(fn ($type, $key) => [
                    "{$key}_email" => $this->getPreference($user, $type['class'], 'email_enabled'),
                    "{$key}_database" => $this->getPreference($user, $type['class'], 'database_enabled'),
                ])
                ->toArray()
        );
    }

    public function form(Form $form): Form
    {
        $schema = collect(self::$notificationTypes)
            ->map(function ($type, $key) {
                return Section::make($type['label'])
                    ->description($type['description'])
                    ->schema([
                        Toggle::make("{$key}_email")
                            ->label('Email notification')
                            ->inline(false),
                        Toggle::make("{$key}_database")
                            ->label('In-app notification')
                            ->inline(false)
                            ->disabled(),
                    ])
                    ->columns(2);
            })
            ->toArray();

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Filament::auth()->user();
        $state = $this->form->getState();

        foreach (self::$notificationTypes as $key => $type) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $type['class'],
                ],
                [
                    'email_enabled' => (bool) ($state["{$key}_email"] ?? true),
                    'database_enabled' => true,
                ]
            );
        }

        Notification::make()
            ->title('Preferences saved')
            ->success()
            ->send();
    }

    private function getPreference($user, string $type, string $field): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        return $pref ? (bool) $pref->$field : true;
    }
}
