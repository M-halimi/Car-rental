<?php

namespace App\Filament\Resources\ReportResource;

use App\Filament\Resources\ReportResource\Pages\EditReport;
use App\Filament\Resources\ReportResource\Pages\ListReports;
use App\Models\Report;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|UnitEnum|null $navigationGroup = 'Moderation';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $slug = 'reports';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reportable_type')
                            ->label('Reported Type')
                            ->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('reportable_id')
                            ->label('Reported ID'),
                        TextEntry::make('reporter.name')
                            ->label('Reported By'),
                        TextEntry::make('reason')
                            ->label('Reason'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),
                Section::make('Moderation')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'resolved' => 'Resolved',
                                    'dismissed' => 'Dismissed',
                                ])
                                ->required(),
                            TextEntry::make('moderator.name')
                                ->label('Moderator')
                                ->visible(fn ($state) => $state !== null),
                        ]),
                        Textarea::make('moderation_notes')
                            ->label('Moderation Notes')
                            ->rows(3),
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
                TextColumn::make('reportable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => Str::of(class_basename($state))->headline())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Agency' => 'warning',
                        'Vehicle' => 'info',
                        'Booking' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reportable_id')
                    ->label('Reported ID'),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('reporter.name')
                    ->label('Reported By')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'resolved' => 'success',
                        'dismissed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'resolved' => 'Resolved',
                        'dismissed' => 'Dismissed',
                    ]),
                SelectFilter::make('reportable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\Agency' => 'Agency',
                        'App\Models\Vehicle' => 'Vehicle',
                        'App\Models\Booking' => 'Booking',
                    ]),
                Filter::make('pending')
                    ->label('Pending Only')
                    ->query(fn (Builder $query) => $query->where('status', 'pending')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('resolveSelected')
                        ->label('Resolve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => Report::whereIn('id', $records)->update([
                            'status' => 'resolved',
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReports::route('/'),
            'edit' => EditReport::route('/{record}/edit'),
        ];
    }
}
