<?php

namespace App\Filament\Agency\Resources\PaymentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Payment Timeline';

    protected static ?string $recordTitleAttribute = 'action';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'gray',
                        'deposited' => 'success',
                        'refunded' => 'warning',
                        'overdue' => 'danger',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2).' MAD' : '-'),
                TextColumn::make('performer.name')
                    ->label('Performed By'),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
