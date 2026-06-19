<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Colors\Color;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'petugas' => 'warning',
                        'nasabah' => 'success',
                        default => 'gray',
                    })
                    ->label('Peran'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),
                TextColumn::make('phone')
                    ->label('Telepon'),
                TextColumn::make('account_no')
                    ->label('No. Rekening')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('umur')
                    ->sortable()
                    ->label('Umur')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_pekerjaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bekerja' => 'success',
                        'tidak_bekerja' => 'gray',
                        'pelajar' => 'info',
                        'mahasiswa' => 'warning',
                        'pensiun' => 'primary',
                        default => 'gray',
                    })
                    ->label('Status Pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('universitas')
                    ->label('Universitas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fakultas')
                    ->label('Fakultas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pendidikan_terakhir')
                    ->badge()
                    ->label('Pendidikan Terakhir')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('saldo')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Saldo Tabungan'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tanggal Daftar'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'petugas' => 'Petugas',
                        'nasabah' => 'Nasabah',
                    ])
                    ->label('Peran'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->color('success')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn ($record) => $record->role === 'nasabah' && $record->status !== 'verified')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'verified';
                        if (empty($record->account_no)) {
                            // Generate account number: BS- + padded ID
                            $record->account_no = 'BS-' . str_pad($record->id, 5, '0', STR_PAD_LEFT);
                        }
                        $record->save();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon(Heroicon::OutlinedXMark)
                    ->visible(fn ($record) => $record->role === 'nasabah' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'rejected';
                        $record->save();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
