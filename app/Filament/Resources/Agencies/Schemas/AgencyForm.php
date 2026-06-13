<?php

namespace App\Filament\Resources\Agencies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
// use Filament\Schemas\Components\Select;
use Filament\Forms\Components\Textarea;
// use Filament\Schemas\Components\TextInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Owner Information')
                    ->visibleOn('create')
                    ->columns(2)
                    ->schema([
                        TextInput::make('owner_name')
                            ->label('Owner Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('owner_email')
                            ->label('Owner Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: 'users',
                                column: 'email',
                            ),
                        TextInput::make('owner_password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->revealable()
                            ->default(fn () => Str::random(12)),
                    ]),

                Section::make('Agency Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Agency Name')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->helperText('Auto-generated from name.'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('email')
                                ->label('Contact Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Contact Phone')
                                ->tel()
                                ->required()
                                ->maxLength(50),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('city_id')
                                ->label('City')
                                ->relationship('city', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('country')
                                ->label('Country')
                                ->default('Morocco')
                                ->maxLength(100),
                        ]),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->maxLength(500),
                        Grid::make(2)->schema([
                            FileUpload::make('logo')
                                ->label('Logo')
                                ->image()
                                ->visibility('public')
                                ->directory('agencies/logos')
                                ->maxSize(2048),
                            TextInput::make('registration_number')
                                ->label('Registration Number')
                                ->maxLength(100),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('tax_id')
                                ->label('Tax ID')
                                ->maxLength(100),
                            Select::make('legal_form')
                                ->label('Legal Form')
                                ->options([
                                    'sarl' => 'SARL',
                                    'sas' => 'SAS',
                                    's_a' => 'S.A.',
                                    'individual' => 'Individual',
                                    'other' => 'Other',
                                ]),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('capital')
                                ->label('Capital')
                                ->numeric()
                                ->prefix('MAD'),
                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2),
                        ]),
                    ]),

                Section::make('Subscription & Status')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'suspended' => 'Suspended',
                                    'expired' => 'Expired',
                                ])
                                ->required()
                                ->default('active'),
                            Select::make('subscription_plan')
                                ->label('Subscription Plan')
                                ->options([
                                    'basic' => 'Basic',
                                    'premium' => 'Premium',
                                    'enterprise' => 'Enterprise',
                                ])
                                ->searchable(),
                            DatePicker::make('subscription_start_date')
                                ->label('Subscription Start Date'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('subscription_end_date')
                                ->label('Subscription End Date'),
                        ]),
                    ]),
            ]);
    }
}
