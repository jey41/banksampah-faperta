<?php

namespace App\Filament\Resources\PickupRequests\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use App\Models\User;

class PickupRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nasabah'),
                TextColumn::make('pickup_address')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->pickup_address)
                    ->label('Alamat'),
                TextColumn::make('estimated_distance')
                    ->suffix(' km')
                    ->sortable()
                    ->label('Jarak'),
                TextColumn::make('pickup_phone')
                    ->label('Telepon'),
                TextColumn::make('pickup_date')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tanggal'),
                TextColumn::make('pickup_time')
                    ->label('Jam'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'assigned' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'assigned' => 'Ditugaskan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->label('Status'),
                TextColumn::make('assignedPetugas.name')
                    ->label('Petugas')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Dibuat'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'assigned' => 'Ditugaskan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                Action::make('assign')
                    ->label('Tugaskan Petugas')
                    ->color('info')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Select::make('assigned_to')
                            ->options(User::whereIn('role', ['admin', 'petugas'])->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->label('Pilih Petugas'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'assigned_to' => $data['assigned_to'],
                            'status' => 'assigned',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Petugas Ditugaskan')
                            ->body('Permintaan jemput telah ditugaskan ke petugas.')
                            ->success()
                            ->send();
                    }),
                Action::make('complete')
                    ->label('Tandai Selesai')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'assigned']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);

                        \Filament\Notifications\Notification::make()
                            ->title('Penjemputan Selesai')
                            ->body('Permintaan jemput telah ditandai sebagai selesai.')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedXMark)
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'assigned']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);

                        \Filament\Notifications\Notification::make()
                            ->title('Penjemputan Dibatalkan')
                            ->body('Permintaan jemput telah dibatalkan.')
                            ->info()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }
}
