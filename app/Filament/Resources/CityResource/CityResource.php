<?php

namespace App\Filament\Resources\CityResource;

use App\Filament\Resources\CityResource\Pages\CreateCity;
use App\Filament\Resources\CityResource\Pages\EditCity;
use App\Filament\Resources\CityResource\Pages\ListCities;
use App\Models\City;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|UnitEnum|null $navigationGroup = 'Platform Configuration';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Cities';

    protected static ?string $slug = 'cities';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('City Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('City Name')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ]),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('vehicles_count')
                    ->label('Vehicles')
                    ->counts('vehicles')
                    ->sortable(),
                TextColumn::make('agencies_count')
                    ->label('Agencies')
                    ->counts('agencies')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
                Filter::make('is_inactive')
                    ->label('Inactive')
                    ->query(fn (Builder $query) => $query->where('is_active', false)),
            ])
            ->defaultSort('name')
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
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
