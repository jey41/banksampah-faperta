<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Operator / Admin'),
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approve_deposit' => 'success',
                        'approve_withdrawal' => 'success',
                        'reject_deposit' => 'danger',
                        'reject_withdrawal' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approve_deposit' => 'Setujui Setoran',
                        'reject_deposit' => 'Tolak Setoran',
                        'approve_withdrawal' => 'Setujui Penarikan',
                        'reject_withdrawal' => 'Tolak Penarikan',
                        default => $state,
                    })
                    ->label('Aktivitas')
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->label('Keterangan Detail'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i:s', 'Asia/Makassar') // WITA time timezone
                    ->sortable()
                    ->label('Waktu Kejadian'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'approve_deposit' => 'Setujui Setoran',
                        'reject_deposit' => 'Tolak Setoran',
                        'approve_withdrawal' => 'Setujui Penarikan',
                        'reject_withdrawal' => 'Tolak Penarikan',
                    ])
                    ->label('Jenis Aktivitas'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
