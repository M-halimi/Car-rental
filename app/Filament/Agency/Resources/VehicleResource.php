<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\VehicleResource\Pages;
use App\Filament\Agency\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationLabel = 'Fleet';

    public static function getNavigationIcon(): string|Heroicon
    {
        return 'heroicon-o-truck';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Vehicle Details')
                    ->tabs([
                        Tabs\Tab::make('Basic Info')
                            ->schema([
                                Section::make()->schema([
                                    Grid::make(3)->schema([
                                        Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        Select::make('city_id')
                                            ->label('City')
                                            ->relationship('city', 'name')
                                            ->required(),
                                        TextInput::make('brand')
                                            ->label('Brand')
                                            ->required(),
                                    ]),
                                    Grid::make(2)->schema([
                                        TextInput::make('model')
                                            ->label('Model')
                                            ->required(),
                                        TextInput::make('year')
                                            ->label('Year')
                                            ->numeric()
                                            ->required(),
                                    ]),
                                    Grid::make(3)->schema([
                                        TextInput::make('daily_rate')
                                            ->label('Daily Rate (MAD)')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('weekly_rate')
                                            ->label('Weekly Rate')
                                            ->numeric(),
                                        TextInput::make('monthly_rate')
                                            ->label('Monthly Rate')
                                            ->numeric(),
                                    ]),
                                    Grid::make(2)->schema([
                                        TextInput::make('registration_number')
                                            ->label('Registration Number')
                                            ->required(),
                                        TextInput::make('plate_number')
                                            ->label('Plate Number'),
                                    ]),
                                    Grid::make(2)->schema([
                                        Select::make('transmission')
                                            ->label('Transmission')
                                            ->options([
                                                'automatic' => 'Automatic',
                                                'manual' => 'Manual',
                                            ])
                                            ->required(),
                                        Select::make('fuel_type')
                                            ->label('Fuel Type')
                                            ->options([
                                                'gasoline' => 'Gasoline',
                                                'diesel' => 'Diesel',
                                                'electric' => 'Electric',
                                                'hybrid' => 'Hybrid',
                                            ])
                                            ->required(),
                                    ]),
                                    TextInput::make('color')
                                        ->label('Color'),
                                ]),

                                Grid::make(2)->schema([
                                    TextInput::make('doors')
                                        ->label('Doors')
                                        ->numeric(),
                                    TextInput::make('seats')
                                        ->label('Seats')
                                        ->numeric(),
                                ]),
                                TextInput::make('price_per_day')
                                    ->label('Price per Day (MAD)')
                                    ->numeric()
                                    ->prefix('MAD')
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'available' => 'Available',
                                        'rented' => 'Rented',
                                        'maintenance' => 'Maintenance',
                                        'unavailable' => 'Unavailable',
                                    ])
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Images & Description')
                            ->schema([
                                Section::make()->schema([
                                    FileUpload::make('images')
                                        ->disk('public')
                                        ->label('Vehicle Images')
                                        ->image()
                                        ->multiple()
                                        ->visibility('public')
                                        ->directory('vehicles')
                                        ->imagePreviewHeight('100')
                                        ->reorderable()
                                        ->openable(),
                                    Textarea::make('description')
                                        ->label('Description')
                                        ->rows(3),
                                ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->size(60),
                TextColumn::make('brand')
                    ->label('Brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),
                TextColumn::make('plate_number')
                    ->label('Plate'),
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('city.name')
                    ->label('City'),
                TextColumn::make('price_per_day')
                    ->label('Price/Day')
                    ->money('MAD'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'maintenance' => 'danger',
                        'unavailable' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Unavailable',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        if (! $user || ! $user->agency) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('agency_id', $user->agency->id);
    }
}
