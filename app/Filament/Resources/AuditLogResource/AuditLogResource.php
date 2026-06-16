<?php

namespace App\Filament\Resources\AuditLogResource;

use App\Filament\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Moderation';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $slug = 'audit-logs';

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
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->searchable(),
                TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                TextColumn::make('model_id')
                    ->label('Model ID'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                    ]),
            ])
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
