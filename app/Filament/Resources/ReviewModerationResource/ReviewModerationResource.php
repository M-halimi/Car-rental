<?php

namespace App\Filament\Resources\ReviewModerationResource;

use App\Filament\Resources\ReviewModerationResource\Pages\EditReviewModeration;
use App\Filament\Resources\ReviewModerationResource\Pages\ListReviewModerations;
use App\Models\VehicleReview;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReviewModerationResource extends Resource
{
    protected static ?string $model = VehicleReview::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|UnitEnum|null $navigationGroup = 'Moderation';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Review Moderation';

    protected static ?string $slug = 'review-moderation';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->schema([
                        TextEntry::make('vehicle.brand')
                            ->label('Vehicle'),
                        TextEntry::make('customer.user.name')
                            ->label('Customer'),
                        TextEntry::make('rating')
                            ->label('Rating'),
                        TextEntry::make('comment')
                            ->label('Comment')
                            ->columnSpanFull(),
                    ]),
                Section::make('Moderation')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Approved')
                            ->default(false),
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
                TextColumn::make('vehicle.brand')
                    ->label('Vehicle')
                    ->description(fn (VehicleReview $record) => "{$record->vehicle?->model} ({$record->vehicle?->plate_number})")
                    ->searchable(),
                TextColumn::make('vehicle.agency.name')
                    ->label('Agency')
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable(),
                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_verified_booking')
                    ->label('Verified')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('pending')
                    ->label('Pending Approval')
                    ->query(fn (Builder $query) => $query->where('is_approved', false)),
                Filter::make('approved')
                    ->label('Approved')
                    ->query(fn (Builder $query) => $query->where('is_approved', true)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => VehicleReview::whereIn('id', $records)->update(['is_approved' => true])),
                    BulkAction::make('rejectSelected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => VehicleReview::whereIn('id', $records)->update(['is_approved' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviewModerations::route('/'),
            'edit' => EditReviewModeration::route('/{record}/edit'),
        ];
    }
}
