<?php

namespace App\Filament\Resources\VehicleCategoryResource;

use App\Filament\Resources\VehicleCategoryResource\Pages\CreateVehicleCategory;
use App\Filament\Resources\VehicleCategoryResource\Pages\EditVehicleCategory;
use App\Filament\Resources\VehicleCategoryResource\Pages\ListVehicleCategories;
use App\Models\VehicleCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VehicleCategoryResource extends Resource
{
    protected static ?string $model = VehicleCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Platform Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Vehicle Categories';

    protected static ?string $slug = 'vehicle-categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Category Name')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('icon')
                                ->label('Icon (Heroicon)')
                                ->helperText('e.g., heroicon-o-truck, heroicon-o-car')
                                ->maxLength(100),
                            TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('icon')
                    ->label('Icon')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('vehicles_count')
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
                Filter::make('is_inactive')
                    ->label('Inactive')
                    ->query(fn (Builder $query) => $query->where('is_active', false)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleCategories::route('/'),
            'create' => CreateVehicleCategory::route('/create'),
            'edit' => EditVehicleCategory::route('/{record}/edit'),
        ];
    }
}
