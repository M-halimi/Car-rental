<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\CustomerResource\Pages;
use App\Filament\Agency\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends AgencyPanelResource
{
    protected static ?string $model = Customer::class;

    public static function getNavigationIcon(): string|Heroicon
    {
        return 'heroicon-o-users';
    }

    protected static ?string $navigationLabel = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('first_name')
                                ->label('First Name')
                                ->required(),
                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Phone')
                                ->tel()
                                ->required(),
                            TextInput::make('nationality')
                                ->label('Nationality'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('birth_date')
                                ->label('Birth Date'),
                            TextInput::make('license_number')
                                ->label('Driving License Number'),
                        ]),
                        DatePicker::make('license_expiry')
                            ->label('License Expiry Date'),
                    ]),
                Section::make('Documents')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('id_document_path')
                                ->label('ID Card/Passport')
                                ->image()
                                ->visibility('public')
                                ->directory('customers/documents')
                                ->openable()
                                ->downloadable(),
                            FileUpload::make('license_document_path')
                                ->label('Driving License')
                                ->image()
                                ->visibility('public')
                                ->directory('customers/documents')
                                ->openable()
                                ->downloadable(),
                        ]),
                    ]),
                Section::make('Verification')
                    ->schema([
                        Select::make('is_verified')
                            ->label('Verification Status')
                            ->options([
                                0 => 'Not Verified',
                                1 => 'Verified',
                            ])
                            ->default(0),
                        Select::make('is_blocked')
                            ->label('Block Status')
                            ->options([
                                0 => 'Active',
                                1 => 'Blocked',
                            ])
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label('City'),
                TextColumn::make('nationality')
                    ->label('Nationality'),
                TextColumn::make('is_verified')
                    ->label('Verified')
                    ->badge()
                    ->color(fn (int $state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn (int $state): string => $state ? 'Verified' : 'Pending'),
                TextColumn::make('is_blocked')
                    ->label('Status')
                    ->badge()
                    ->color(fn (int $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (int $state): string => $state ? 'Blocked' : 'Active'),
            ])
            ->filters([
                SelectFilter::make('is_verified')
                    ->label('Verification')
                    ->options([
                        0 => 'Not Verified',
                        1 => 'Verified',
                    ]),
                SelectFilter::make('is_blocked')
                    ->label('Status')
                    ->options([
                        0 => 'Active',
                        1 => 'Blocked',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    protected static function applyAgencyScope(Builder $query, int $agencyId): Builder
    {
        return $query->where(function ($q) use ($agencyId) {
            $q->whereHas('bookings.vehicle', fn ($sq) => $sq->where('agency_id', $agencyId))
                ->orWhereDoesntHave('bookings');
        });
    }
}
