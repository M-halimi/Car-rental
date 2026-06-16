<?php

namespace App\Filament\Resources\AddonResource;

use App\Filament\Resources\AddonResource\Pages\CreateAddon;
use App\Filament\Resources\AddonResource\Pages\EditAddon;
use App\Filament\Resources\AddonResource\Pages\ListAddons;
use App\Models\Extra;
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

class AddonResource extends Resource
{
    protected static ?string $model = Extra::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Platform Configuration';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Add-ons';

    protected static ?string $slug = 'addons';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Add-on Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Name (English)')
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
                            TextInput::make('name_ar')
                                ->label('Name (Arabic)')
                                ->maxLength(255),
                            TextInput::make('name_fr')
                                ->label('Name (French)')
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('price_per_day')
                                ->label('Price per Day')
                                ->numeric()
                                ->prefix('MAD')
                                ->required()
                                ->minValue(0),
                            TextInput::make('icon')
                                ->label('Icon')
                                ->helperText('e.g., heroicon-o-globe-alt')
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
                    ->label('Add-on')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price_per_day')
                    ->label('Price/Day')
                    ->money('MAD')
                    ->sortable(),
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
            'index' => ListAddons::route('/'),
            'create' => CreateAddon::route('/create'),
            'edit' => EditAddon::route('/{record}/edit'),
        ];
    }
}
